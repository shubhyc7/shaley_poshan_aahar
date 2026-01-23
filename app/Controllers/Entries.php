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

        // 1. Capture Filter Values
        $filterMonth = $this->request->getGet('month') ?? date('n');
        $filterYear  = $this->request->getGet('year') ?? date('Y');

        $data['main_items'] = $itemModel->where(['item_type' => 'MAIN', 'is_disable' => 0])->findAll();
        $data['support_items'] = $itemModel->where(['item_type' => 'SUPPORT', 'is_disable' => 0])->findAll();

        // 2. Filter Parent Entries by Month and Year
        $data['entries'] = $DailyAaharEntriesModel->where('is_disable', 0)
            ->where('MONTH(entry_date)', $filterMonth)
            ->where('YEAR(entry_date)', $filterYear)
            ->orderBy('entry_date', 'DESC')
            ->findAll();

        // Pass filters back to the view
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

        return redirect()->to('/entries')->with('status', 'नोंद यशस्वीरित्या हटवण्यात आली.');
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

        // 1. Fetch Filtered Parent Entries
        $entries = $DailyAaharEntriesModel->where('is_disable', 0)
            ->where('MONTH(entry_date)', $month)
            ->where('YEAR(entry_date)', $year)
            ->orderBy('entry_date', 'ASC')
            ->findAll();

        // 2. Fetch all Item Masters (to create columns)
        $mainItems = $itemModel->where(['item_type' => 'MAIN', 'is_disable' => 0])->findAll();
        $supportItems = $itemModel->where(['item_type' => 'SUPPORT', 'is_disable' => 0])->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 3. Set Dynamic Headers
        $headers = ['Date', 'Category', 'Total Students', 'Present Students'];

        // Add Main Item names to header list
        foreach ($mainItems as $mi) {
            $headers[] = $mi['item_name'];
        }
        // Add Support Item names to header list
        foreach ($supportItems as $si) {
            $headers[] = $si['item_name'];
        }

        $colIndex = 1;
        foreach ($headers as $headerText) {
            $colLetter = $this->getColumnLetter($colIndex);
            $sheet->setCellValue($colLetter . '1', $headerText);
            $colIndex++;
        }

        // Header Styling
        $lastCol = $this->getColumnLetter(count($headers));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // 4. Fill Data Rows
        $rowNum = 2;
        foreach ($entries as $entry) {
            // Fetch all item quantities for this specific entry ID
            $childItems = $db->table('daily_aahar_entries_items')
                ->where(['daily_aahar_entries_id' => $entry['id'], 'is_disable' => 0])
                ->get()->getResultArray();

            // Create a lookup map [item_id => qty]
            $itemQtyMap = array_column($childItems, 'qty', 'item_id');

            // Static Columns
            $sheet->setCellValue('A' . $rowNum, date('d-M-Y', strtotime($entry['entry_date'])));
            $sheet->setCellValue('B' . $rowNum, 'Class ' . $entry['category']);
            $sheet->setCellValue('C' . $rowNum, $entry['total_students']);
            $sheet->setCellValue('D' . $rowNum, $entry['present_students']);

            $currentCol = 5;

            // Dynamic Main Item Columns
            foreach ($mainItems as $mi) {
                $qty = $itemQtyMap[$mi['id']] ?? 0;
                $sheet->setCellValue($this->getColumnLetter($currentCol) . $rowNum, number_format($qty, 3));
                $currentCol++;
            }

            // Dynamic Support Item Columns
            foreach ($supportItems as $si) {
                $qty = $itemQtyMap[$si['id']] ?? 0;
                $sheet->setCellValue($this->getColumnLetter($currentCol) . $rowNum, number_format($qty, 3));
                $currentCol++;
            }

            $rowNum++;
        }

        // Auto-size all columns
        for ($i = 1; $i < $currentCol; $i++) {
            $sheet->getColumnDimension($this->getColumnLetter($i))->setAutoSize(true);
        }

        // 5. Export File
        $filename = 'Aahar_Register_' . date('Y-m-d_His') . '.xlsx';
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
