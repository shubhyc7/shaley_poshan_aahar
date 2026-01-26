<?php

namespace App\Controllers;

use App\Models\DailyAaharEntriesModel;
use App\Models\ItemModel;
use App\Models\StudentStrengthModel;
use App\Models\DailyAaharEntriesItemsModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Entries extends BaseController
{
    // entries_view
    public function index()
    {
        $itemModel = new ItemModel();
        $DailyAaharEntriesModel = new DailyAaharEntriesModel();
        $db = \Config\Database::connect();

        $filterMonth = $this->request->getGet('month') ?? date('n');
        $filterYear  = $this->request->getGet('year') ?? date('Y');
        $startDate   = "$filterYear-" . str_pad($filterMonth, 2, "0", STR_PAD_LEFT) . "-01";

        $data['main_items'] = $itemModel->where(['item_type' => 'MAIN', 'is_disable' => 0])->findAll();
        $data['support_items'] = $itemModel->where(['item_type' => 'SUPPORT', 'is_disable' => 0])->findAll();

        // Calculate Monthly Stock Context
        $monthlyStock = [];
        foreach (array_merge($data['main_items'], $data['support_items']) as $item) {
            // 1. Opening: Balance before 1st of selected month
            $opening = $db->table('stock_transactions')
                ->select("SUM(CASE WHEN transaction_type IN ('OPENING', 'IN') THEN quantity ELSE -quantity END) as bal", false)
                ->where(['item_id' => $item['id'], 'transaction_date <' => $startDate, 'is_disable' => 0])
                ->get()->getRow()->bal ?? 0;

            // 2. Month Inward: Stock received during the filtered month
            $received = $db->table('stock_transactions')
                ->selectSum('quantity')
                ->where(['item_id' => $item['id'], 'transaction_type' => 'IN', 'is_disable' => 0])
                ->where('MONTH(transaction_date)', $filterMonth)
                ->where('YEAR(transaction_date)', $filterYear)
                ->get()->getRow()->quantity ?? 0;

            $monthlyStock[$item['id']] = [
                'opening' => (float)$opening,
                'received' => (float)$received,
                'available' => (float)$opening + (float)$received // Total available to spend this month
            ];
        }

        $data['monthly_stock_logic'] = $monthlyStock;

        $data['entries'] = $DailyAaharEntriesModel->where('is_disable', 0)
            ->where('MONTH(entry_date)', $filterMonth)
            ->where('YEAR(entry_date)', $filterYear)
            ->orderBy('entry_date', 'DESC')
            ->findAll();

        $data['filterMonth'] = $filterMonth;
        $data['filterYear']  = $filterYear;

        return view('entries_view', $data);
    }

    // store
    public function store()
    {
        $entryModel = new DailyAaharEntriesModel();
        $itemsModel = new DailyAaharEntriesItemsModel();

        $date = $this->request->getPost('entry_date');
        $category = $this->request->getPost('category');

        // 1. Duplicate Validation
        $existing = $entryModel->where([
            'entry_date' => $date,
            'category'   => $category,
            'is_disable' => 0
        ])->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', "इयत्ता $category साठी या तारखेची नोंद आधीच अस्तित्वात आहे!");
        }

        // 2. Insert Parent Entry
        $mainData = [
            'category'         => $category,
            'entry_date'       => $date,
            'total_students'   => $this->request->getPost('student_strength'),
            'present_students' => $this->request->getPost('present_students'),
            'is_disable'       => 0
        ];

        $entryModel->insert($mainData);
        $parentId = $entryModel->getInsertID();

        // 3. Insert Main Items (Checked ones)
        $main_item_ids = $this->request->getPost('main_item_id');
        $main_qtys = $this->request->getPost('main_item_qty');

        $db = \Config\Database::connect();

        if ($main_item_ids) {
            foreach ($main_item_ids as $idx => $mid) {
                // Since checkboxes only send values for checked items, 
                // we need to find the correct qty based on item ID index logic
                // usually better to use item ID as key in HTML: main_item_qty[ID]
                if ($main_qtys[$idx] > 0) {
                    $itemsModel->insert([
                        'daily_aahar_entries_id' => $parentId,
                        'item_id'                => $mid,
                        'qty'                    => $main_qtys[$idx],
                        'is_disable'             => 0
                    ]);

                    // Inside Entries::store loop after inserting into daily_aahar_entries_items
                    $db->table('stock_transactions')->insert([
                        'item_id' => $mid,
                        'transaction_type' => 'OUT',
                        'daily_aahar_entries_id' => $parentId, // ID from child table
                        'quantity' => $main_qtys[$idx],
                        'transaction_date' => $date,
                        'is_disable' => 0
                    ]);
                }
            }
        }

        // 4. Insert Support Items
        $support_ids = $this->request->getPost('support_item_id');
        $support_qtys = $this->request->getPost('support_qty');

        if ($support_ids) {
            foreach ($support_ids as $idx => $sid) {
                if ($support_qtys[$idx] > 0) {
                    $itemsModel->insert([
                        'daily_aahar_entries_id' => $parentId,
                        'item_id'                => $sid,
                        'qty'                    => $support_qtys[$idx],
                        'is_disable'             => 0
                    ]);

                    // Inside Entries::store loop after inserting into daily_aahar_entries_items
                    $db->table('stock_transactions')->insert([
                        'item_id' => $sid,
                        'transaction_type' => 'OUT',
                        'daily_aahar_entries_id' => $parentId, // ID from child table
                        'quantity' => $support_qtys[$idx],
                        'transaction_date' => $date,
                        'is_disable' => 0
                    ]);
                }
            }
        }

        return redirect()->to('/entries')->with('status', 'नोंद यशस्वीरित्या जतन केली!');
    }

    // getStrengthAjax
    public function getStrengthAjax()
    {
        $date = $this->request->getPost('date');
        $category = $this->request->getPost('category');
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));

        $model = new StudentStrengthModel();
        $strength = $model->where(['month' => $month, 'year' => $year, 'category' => $category, 'is_disable' => '0'])->first();

        return $this->response->setJSON(['total' => $strength['total_students'] ?? 0]);
    }

    // calculate
    public function calculate()
    {
        $date = $this->request->getPost('date');
        $present = (int)$this->request->getPost('present');
        $category = $this->request->getPost('category');

        $rateModel = new \App\Models\RateModel();
        $rates = $rateModel->where([
            'month'    => date('n', strtotime($date)),
            'year'     => date('Y', strtotime($date)),
            'category' => $category
        ])->findAll();

        $all_calculated = [];
        foreach ($rates as $rate) {
            // Calculate the theoretical amount for every item
            $all_calculated[$rate['item_id']] = number_format($present * $rate['per_student_qty'], 3, '.', '');
        }

        return $this->response->setJSON(['rates' => $all_calculated]);
    }

    // delete_session
    public function delete($id)
    {
        $db = \Config\Database::connect();

        // 1. Soft delete the main parent entry
        $db->table('daily_aahar_entries')
            ->where('id', $id)
            ->update(['is_disable' => 1]);

        // 2. Soft delete all items (Main & Support) linked to this parent ID
        // This targets your new 'daily_aahar_entries_items' table
        $db->table('daily_aahar_entries_items')
            ->where('daily_aahar_entries_id', $id)
            ->update(['is_disable' => 1]);


        $db->table('stock_transactions')
            ->where('daily_aahar_entries_id', $id)
            ->update(['is_disable' => 1]);

        return redirect()->to('/entries')->with('status', 'नोंद यशस्वीरित्या हटवण्यात आली!');
    }

    // export
    public function export()
    {
        $DailyAaharEntriesModel = new \App\Models\DailyAaharEntriesModel();
        $itemModel = new \App\Models\ItemModel();
        $db = \Config\Database::connect();

        // Capture filters from the URL
        $month = $this->request->getGet('month') ?? date('n');
        $year  = $this->request->getGet('year') ?? date('Y');
        $startDate = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";

        // 1. Fetch Items and Filtered Parent Entries
        $mainItems = $itemModel->where(['item_type' => 'MAIN', 'is_disable' => 0])->findAll();
        $supportItems = $itemModel->where(['item_type' => 'SUPPORT', 'is_disable' => 0])->findAll();
        $allItems = array_merge($mainItems, $supportItems);

        $entries = $DailyAaharEntriesModel->where('is_disable', 0)
            ->where('MONTH(entry_date)', $month)
            ->where('YEAR(entry_date)', $year)
            ->orderBy('entry_date', 'ASC')
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 2. Set Headers
        $headers = ['अ.क्र.', 'तारीख', 'इयत्ता', 'एकूण', 'उपस्थित'];
        foreach ($allItems as $it) {
            $headers[] = $it['item_name'];
        }

        foreach ($headers as $idx => $title) {
            $sheet->setCellValue($this->getColumnLetter($idx + 1) . '1', $title);
        }

        // Header Styling
        $lastCol = $this->getColumnLetter(count($headers));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // 3. ADD MONTHLY RUNTIME STOCK ROW (Row 2)
        $sheet->setCellValue('A2', 'शिल्लक साठा (Opening + In)');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getFont()->setBold(true);

        $col = 6;
        $monthlyAvailable = [];
        foreach ($allItems as $item) {
            // Calculate Opening + Received for the month
            $opening = $db->table('stock_transactions')
                ->select("SUM(CASE WHEN transaction_type IN ('OPENING', 'IN') THEN quantity ELSE -quantity END) as bal", false)
                ->where(['item_id' => $item['id'], 'transaction_date <' => $startDate, 'is_disable' => 0])
                ->get()->getRow()->bal ?? 0;

            $received = $db->table('stock_transactions')
                ->selectSum('quantity')
                ->where(['item_id' => $item['id'], 'transaction_type' => 'IN', 'is_disable' => 0])
                ->where('MONTH(transaction_date)', $month)
                ->where('YEAR(transaction_date)', $year)
                ->get()->getRow()->quantity ?? 0;

            $available = (float)$opening + (float)$received;
            $monthlyAvailable[$item['id']] = $available;

            $sheet->setCellValue($this->getColumnLetter($col) . '2', number_format($available, 3));
            $col++;
        }
        // Style the Stock Row
        $sheet->getStyle("A2:{$lastCol}2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF2CC');

        // 4. Fill Entry Data Rows
        $rowNum = 3;
        $srNo = 1;
        $itemTotals = [];
        foreach ($entries as $entry) {
            $childItems = $db->table('daily_aahar_entries_items')
                ->where(['daily_aahar_entries_id' => $entry['id'], 'is_disable' => 0])
                ->get()->getResultArray();

            $qtyMap = array_column($childItems, 'qty', 'item_id');

            $sheet->setCellValue('A' . $rowNum, $srNo++);
            $sheet->setCellValue('B' . $rowNum, date('d-m-Y', strtotime($entry['entry_date'])));
            $sheet->setCellValue('C' . $rowNum, $entry['category']);
            $sheet->setCellValue('D' . $rowNum, $entry['total_students']);
            $sheet->setCellValue('E' . $rowNum, $entry['present_students']);

            $currentCol = 6;
            foreach ($allItems as $it) {
                $qty = $qtyMap[$it['id']] ?? 0;
                $itemTotals[$it['id']] = ($itemTotals[$it['id']] ?? 0) + $qty;
                $sheet->setCellValue($this->getColumnLetter($currentCol) . $rowNum, $qty > 0 ? number_format($qty, 3) : '-');
                $currentCol++;
            }
            $rowNum++;
        }

        // 5. ADD SUMMARY FOOTER ROWS
        // Row: Total Used
        $sheet->setCellValue('A' . $rowNum, 'एकूण वापर (Total Used)');
        $sheet->mergeCells("A$rowNum:E$rowNum");
        $col = 6;
        foreach ($allItems as $it) {
            $sheet->setCellValue($this->getColumnLetter($col) . $rowNum, number_format($itemTotals[$it['id']] ?? 0, 3));
            $col++;
        }
        $sheet->getStyle("A$rowNum:{$lastCol}$rowNum")->getFont()->setBold(true);
        $rowNum++;

        // Row: Remaining Stock
        $sheet->setCellValue('A' . $rowNum, 'अखेरची शिल्लक (Remaining Stock)');
        $sheet->mergeCells("A$rowNum:E$rowNum");
        $col = 6;
        foreach ($allItems as $it) {
            $remaining = $monthlyAvailable[$it['id']] - ($itemTotals[$it['id']] ?? 0);
            $sheet->setCellValue($this->getColumnLetter($col) . $rowNum, number_format($remaining, 3));
            $col++;
        }
        $sheet->getStyle("A$rowNum:{$lastCol}$rowNum")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'C00000']], // Red color for remaining
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9EAD3']]
        ]);

        // Final Auto-size
        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension($this->getColumnLetter($i))->setAutoSize(true);
        }

        $filename = 'दैनंदिन_बहु-वस्तू_नोंद_' . $month . '_' . $year . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function getColumnLetter($num)
    {
        $letter = '';
        $num--; // Convert to 0-based
        while ($num >= 0) {
            $letter = chr(65 + ($num % 26)) . $letter;
            $num = intval($num / 26) - 1;
        }
        return $letter;
    }
}
