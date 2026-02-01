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
        // Run the backup check
        $this->handleAutoBackup();

        $itemModel = new ItemModel();
        $DailyAaharEntriesModel = new DailyAaharEntriesModel();
        $db = \Config\Database::connect();

        $filterMonth = (int)($this->request->getGet('month') ?? date('n'));
        $filterYear  = (int)($this->request->getGet('year') ?? date('Y'));
        $filterMonth = ($filterMonth < 1 || $filterMonth > 12) ? (int)date('n') : $filterMonth;
        $filterYear  = ($filterYear < 2020 || $filterYear > 2030) ? (int)date('Y') : $filterYear;
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
            // ->orderBy('entry_date', 'DESC')
            ->orderBy('entry_date', 'ASC')
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

        $date = trim($this->request->getPost('entry_date') ?? '');
        $category = trim($this->request->getPost('category') ?? '');
        $totalStudents = (int)($this->request->getPost('student_strength') ?? 0);
        $presentStudents = (int)($this->request->getPost('present_students') ?? 0);

        // Validation
        if (empty($date)) {
            return redirect()->back()->withInput()->with('error', 'कृपया तारीख निवडा!');
        }
        if (empty($category)) {
            return redirect()->back()->withInput()->with('error', 'कृपया इयत्ता निवडा!');
        }
        if ($presentStudents <= 0) {
            return redirect()->back()->withInput()->with('error', 'उपस्थित विद्यार्थी संख्या ० पेक्षा जास्त असणे आवश्यक आहे!');
        }
        if ($totalStudents > 0 && $presentStudents > $totalStudents) {
            return redirect()->back()->withInput()->with('error', 'उपस्थित विद्यार्थी संख्या एकूण संख्येपेक्षा जास्त असू शकत नाही!');
        }

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
            'total_students'   => $totalStudents,
            'present_students' => $presentStudents,
            'is_disable'       => 0
        ];

        $entryModel->insert($mainData);
        $parentId = $entryModel->getInsertID();

        // 3. Insert Main Items (Checked ones) - use item_id as key for correct qty mapping
        $main_item_ids = $this->request->getPost('main_item_id') ?: [];
        $main_qtys = $this->request->getPost('main_item_qty') ?: [];

        $db = \Config\Database::connect();

        if ($main_item_ids) {
            foreach ($main_item_ids as $mid) {
                $qty = isset($main_qtys[$mid]) ? (float)$main_qtys[$mid] : 0;
                if ($qty > 0) {
                    $itemsModel->insert([
                        'daily_aahar_entries_id' => $parentId,
                        'item_id'                => $mid,
                        'qty'                    => $qty,
                        'is_disable'             => 0
                    ]);
                    $db->table('stock_transactions')->insert([
                        'item_id' => $mid,
                        'category' => $category,
                        'transaction_type' => 'OUT',
                        'daily_aahar_entries_id' => $parentId,
                        'quantity' => $qty,
                        'transaction_date' => $date,
                        'is_disable' => 0
                    ]);
                }
            }
        }

        // 4. Insert Support Items - use item_id as key for correct qty mapping
        $support_ids = $this->request->getPost('support_item_id') ?: [];
        $support_qtys = $this->request->getPost('support_qty') ?: [];

        if ($support_ids) {
            foreach ($support_ids as $sid) {
                $qty = isset($support_qtys[$sid]) ? (float)$support_qtys[$sid] : 0;
                if ($qty > 0) {
                    $itemsModel->insert([
                        'daily_aahar_entries_id' => $parentId,
                        'item_id'                => $sid,
                        'qty'                    => $qty,
                        'is_disable'             => 0
                    ]);
                    $db->table('stock_transactions')->insert([
                        'item_id' => $sid,
                        'category' => $category,
                        'transaction_type' => 'OUT',
                        'daily_aahar_entries_id' => $parentId,
                        'quantity' => $qty,
                        'transaction_date' => $date,
                        'is_disable' => 0
                    ]);
                }
            }
        }

        $filterMonth = $this->request->getPost('filter_month') ?? $this->request->getGet('month') ?? date('n');
        $filterYear = $this->request->getPost('filter_year') ?? $this->request->getGet('year') ?? date('Y');
        return redirect()->to('/entries?month=' . $filterMonth . '&year=' . $filterYear)->with('status', 'नोंद यशस्वीरित्या जतन केली!');
    }

    // getStrengthAjax
    public function getStrengthAjax()
    {
        $category = $this->request->getPost('category');
        if (empty($category)) {
            return $this->response->setJSON(['total' => 0]);
        }

        $model = new StudentStrengthModel();
        $strength = $model->where(['category' => $category, 'is_disable' => 0])->first();

        return $this->response->setJSON(['total' => $strength['total_students'] ?? 0]);
    }

    // calculate
    public function calculate()
    {
        $date = $this->request->getPost('date');
        $present = (int)$this->request->getPost('present');
        $category = $this->request->getPost('category');

        if (empty($date) || empty($category) || $present <= 0) {
            return $this->response->setJSON(['rates' => []]);
        }
        $ts = strtotime($date);
        if ($ts === false) {
            return $this->response->setJSON(['rates' => []]);
        }

        $rateModel = new \App\Models\RateModel();
        $rates = $rateModel->where([
            'category' => $category,
            'is_disable' => 0
        ])->findAll();

        $all_calculated = [];
        foreach ($rates as $rate) {
            // Calculate the theoretical amount for every item
            $all_calculated[$rate['item_id']] = number_format($present * $rate['per_student_qty'], 5, '.', '');
        }

        return $this->response->setJSON(['rates' => $all_calculated]);
    }

    // delete
    public function delete($id)
    {
        $db = \Config\Database::connect();

        $filterMonth = $this->request->getGet('month') ?? date('n');
        $filterYear = $this->request->getGet('year') ?? date('Y');

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

        return redirect()->to('/entries?month=' . $filterMonth . '&year=' . $filterYear)->with('status', 'नोंद यशस्वीरित्या हटवण्यात आली!');
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
        $itemCount = count($allItems);

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
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ]);

        // 3. ADD MONTHLY RUNTIME STOCK ROW (Row 2)
        $sheet->setCellValue('A2', 'शिल्लक साठा (Opening + In)');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getFont()->setBold(true);

        $col = 6;
        $monthlyAvailable = [];
        foreach ($allItems as $item) {
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

            $sheet->setCellValue($this->getColumnLetter($col) . '2', $available);
            $col++;
        }
        // Style the Stock Row
        $sheet->getStyle("A2:{$lastCol}2")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFF2CC');

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
                if ($qty > 0) {
                    $sheet->setCellValue($this->getColumnLetter($currentCol) . $rowNum, $qty);
                } else {
                    $sheet->setCellValue($this->getColumnLetter($currentCol) . $rowNum, '-');
                }
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
            $sheet->setCellValue($this->getColumnLetter($col) . $rowNum, $itemTotals[$it['id']] ?? 0);
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
            $sheet->setCellValue($this->getColumnLetter($col) . $rowNum, $remaining);
            $col++;
        }

        // --- APPLY FIVE DECIMAL FORMATTING TO ALL NUMERIC ITEM COLUMNS ---
        $firstItemColLetter = 'F';
        $sheet->getStyle("{$firstItemColLetter}2:{$lastCol}{$rowNum}")
            ->getNumberFormat()
            ->setFormatCode('0.00000');
        // ----------------------------------------------------------------------

        $sheet->getStyle("A$rowNum:{$lastCol}$rowNum")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'C00000']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9EAD3']]
        ]);

        // Final Auto-size
        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension($this->getColumnLetter($i))->setAutoSize(true);
        }

        $filename = 'दैनंदिन_पोषण_आहार_नोंद_' . $month . '_' . $year . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
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

    private function handleAutoBackup()
    {
        // 1. Set Timezone to India immediately
        date_default_timezone_set('Asia/Kolkata');

        $backupPath = WRITEPATH . 'backups/';
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        $latestFileTime = 0;
        $files = glob($backupPath . "*.sql");
        if ($files) {
            $latestFileTime = max(array_map('filemtime', $files));
        }

        // Check if 1 hour (3600 seconds) has passed
        if (time() - $latestFileTime > 3600) {
            $this->generatePHPBackup($backupPath);
        }
    }

    private function generatePHPBackup($path)
    {
        date_default_timezone_set('Asia/Kolkata');

        $db = \Config\Database::connect();
        $tables = $db->listTables();

        // Header for a standard SQL file
        $output = "-- Shaley Poshan Aahar Backup\n";
        $output .= "-- Generated: " . date('Y-m-d H:i:s') . " (IST)\n";
        $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // ADD DROP TABLE: This makes the file 'Import-Ready'
            $output .= "DROP TABLE IF EXISTS `$table`;\n";

            // Create Table Syntax
            $query = $db->query("SHOW CREATE TABLE `$table`")->getRowArray();
            $output .= "\n" . $query['Create Table'] . ";\n\n";

            // Fetch Data
            $rows = $db->table($table)->get()->getResultArray();
            if (!empty($rows)) {
                $output .= "-- Data for table `$table` --\n";
                foreach ($rows as $row) {
                    // Properly escape data for SQL injection prevention and special characters
                    $escapedValues = array_map(function ($value) use ($db) {
                        if ($value === null) return 'NULL';
                        return $db->escape($value);
                    }, $row);

                    $output .= "INSERT INTO `$table` (`" . implode("`, `", array_keys($row)) . "`) VALUES (" . implode(", ", array_values($escapedValues)) . ");\n";
                }
            }
            $output .= "\n-- --------------------------------------------------------\n";
        }

        $output .= "\nSET FOREIGN_KEY_CHECKS=1;";

        // Filename with readable date and time
        $filename = 'db_full_bk_' . date('Y_m_d_H_i') . '.sql';
        file_put_contents($path . $filename, $output);

        // REMOVED cleanupOldBackups($path) call to keep all files permanently
    }
}
