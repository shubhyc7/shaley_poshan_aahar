<?php

namespace App\Controllers;

use App\Models\EntryModel;
use App\Models\ItemModel;
use App\Models\RateModel;
use App\Models\StudentStrengthModel;
use App\Models\SupportEntryModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Entries extends BaseController
{
    public function index()
    {
        $itemModel = new ItemModel();
        $entryModel = new EntryModel();

        $data['main_items'] = $itemModel->where(['item_type' => 'MAIN', 'is_disable' => 0])->findAll();
        $data['support_items'] = $itemModel->where(['item_type' => 'SUPPORT', 'is_disable' => 0])->findAll();

        // Fetch only active history entries
        $data['entries'] = $entryModel->select('daily_aahar_entries.*, items.item_name')
            ->join('items', 'items.id = daily_aahar_entries.item_id')
            ->where('daily_aahar_entries.is_disable', 0)
            ->orderBy('entry_date', 'DESC')
            ->findAll();

        return view('entries/index', $data);
    }

    public function create()
    {
        $itemModel = new ItemModel();
        $rateModel = new RateModel();
        $strengthModel = new StudentStrengthModel();

        $month = date('n');
        $year = date('Y');

        // Get total students registered for this month
        $strength = $strengthModel->where(['month' => $month, 'year' => $year])->first();

        $data['total_enrolled'] = $strength['total_students'] ?? 0;
        $data['main_items'] = $itemModel->where('item_type', 'MAIN')->findAll();

        // Get rates for calculation (Main items and Support items)
        $data['rates'] = json_encode($rateModel->where(['month' => $month, 'year' => $year])->findAll());

        return view('entries/create', $data);
    }

    public function store()
    {
        $entryModel = new EntryModel();
        $supportModel = new SupportEntryModel(); // Ensure this model points to daily_aahar_entries_support_items

        $date = $this->request->getPost('entry_date');
        $category = $this->request->getPost('category');

        // 1. DUPLICATE ENTRY VALIDATION
        // Prevent multiple entries for the same Class Category on the same Date
        $existing = $entryModel->where([
            'entry_date' => $date,
            'category'   => $category,
            'is_disable' => 0
        ])->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Class $category record for this date already exists!");
        }

        $main_item_ids = $this->request->getPost('main_item_id');
        $main_qtys = $this->request->getPost('main_item_qty');

        if (empty($main_item_ids)) {
            return redirect()->back()->withInput()->with('error', "Please select at least one Main Item.");
        }

        // Loop through selected Main Items (e.g., Rice and Dal)
        foreach ($main_item_ids as $idx => $m_id) {
            $mainData = [
                'entry_date'       => $date,
                'category'         => $category,
                'total_students'   => $this->request->getPost('student_strength'),
                'present_students' => $this->request->getPost('present_students'),
                'item_id'          => $m_id,
                'qty'              => $main_qtys[$idx],
                'month'            => date('n', strtotime($date)),
                'year'             => date('Y', strtotime($date)),
                'is_disable'       => 0
            ];

            $entryModel->insert($mainData);
            $lastMainId = $entryModel->getInsertID();

            // 2. Save Support Items (Oil, Salt, etc.) ONLY for the first main item loop
            // This prevents duplicating ingredient consumption if two main items are served.
            if ($idx === 0) {
                $s_ids = $this->request->getPost('support_item_id');
                $s_qtys = $this->request->getPost('support_qty');
                if ($s_ids) {
                    foreach ($s_ids as $s_idx => $sid) {
                        if ($s_qtys[$s_idx] > 0) {
                            $supportModel->save([
                                'main_entry_id'   => $lastMainId,
                                'entry_date'      => $date,
                                'support_item_id' => $sid,
                                'qty'             => $s_qtys[$s_idx],
                                'is_disable'      => 0
                            ]);
                        }
                    }
                }
            }
        }
        return redirect()->to('/entries')->with('status', 'Entries saved successfully!');
    }


    public function calculateAjax()
    {
        $date = $this->request->getPost('date');
        $present = (int)$this->request->getPost('present');
        $mainItemId = $this->request->getPost('main_item_id');
        $category = $this->request->getPost('category'); // '5-8' or '8-10'

        $rateModel = new RateModel();
        // Fetch rates specifically for the selected category
        $rates = $rateModel->where([
            'month'    => date('n', strtotime($date)),
            'year'     => date('Y', strtotime($date)),
            'category' => $category
        ])->findAll();

        // ... same calculation logic as before ...
    }

    public function getStrengthAjax()
    {
        $date = $this->request->getPost('date');
        $category = $this->request->getPost('category');
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));

        $model = new StudentStrengthModel();
        $strength = $model->where(['month' => $month, 'year' => $year, 'category' => $category])->first();

        return $this->response->setJSON(['total' => $strength['total_students'] ?? 0]);
    }

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

    // SOFT DELETE: Update is_disable to 1
    public function delete($id)
    {
        $entryModel = new EntryModel();
        $db = \Config\Database::connect();

        $entry = $entryModel->find($id);

        if ($entry) {
            // 1. Soft delete the support items using Query Builder 
            // This avoids the "No data to update" exception even if no support items exist
            $db->table('daily_aahar_entries_support_items')
                ->where('main_entry_id', $id)
                ->update(['is_disable' => 1]);

            // 2. Soft delete the main entry using the Model's primary key method
            $entryModel->update($id, ['is_disable' => 1]);

            return redirect()->to('/entries')->with('status', 'Entry archived successfully.');
        } else {
            return redirect()->to('/entries')->with('error', 'Record not found.');
        }
    }

    public function export()
    {
        $entryModel = new EntryModel();
        $itemModel = new ItemModel();
        $db = \Config\Database::connect();

        // Export only active entries
        $entries = $entryModel->select('daily_aahar_entries.*, items.item_name as main_item_name, items.unit as main_item_unit')
            ->join('items', 'items.id = daily_aahar_entries.item_id', 'left')
            ->where('daily_aahar_entries.is_disable', 0)
            ->orderBy('entry_date', 'DESC')
            ->findAll();

        $supportItems = $itemModel->where(['item_type' => 'SUPPORT', 'is_disable' => 0])->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $colIndex = 0;
        $headers = ['Date', 'Category', 'Total Students', 'Present Students', 'Main Item', 'Main Qty', 'Unit'];
        foreach ($headers as $header) {
            $col = $this->getColumnLetter($colIndex + 1);
            $sheet->setCellValue($col . '1', $header);
            $colIndex++;
        }

        // Add support item headers
        foreach ($supportItems as $si) {
            $col = $this->getColumnLetter($colIndex + 1);
            $sheet->setCellValue($col . '1', $si['item_name']);
            $colIndex++;
        }

        // Calculate last column (7 base columns + support items)
        $totalCols = $colIndex;
        $lastCol = $this->getColumnLetter($totalCols);
        $headerRange = 'A1:' . $lastCol . '1';

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle($headerRange)->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        foreach ($entries as $entry) {
            $colIndex = 0;
            $col = $this->getColumnLetter($colIndex + 1);
            $sheet->setCellValue($col . $row, date('d-M-Y', strtotime($entry['entry_date'])));
            $colIndex++;

            $col = $this->getColumnLetter($colIndex + 1);
            $sheet->setCellValue($col . $row, 'Class ' . $entry['category']);
            $colIndex++;

            $col = $this->getColumnLetter($colIndex + 1);
            $sheet->setCellValue($col . $row, $entry['total_students']);
            $colIndex++;

            $col = $this->getColumnLetter($colIndex + 1);
            $sheet->setCellValue($col . $row, $entry['present_students']);
            $colIndex++;

            $col = $this->getColumnLetter($colIndex + 1);
            $sheet->setCellValue($col . $row, $entry['main_item_name'] ?? 'N/A');
            $colIndex++;

            $col = $this->getColumnLetter($colIndex + 1);
            $sheet->setCellValue($col . $row, number_format($entry['qty'], 3));
            $colIndex++;

            $col = $this->getColumnLetter($colIndex + 1);
            $sheet->setCellValue($col . $row, $entry['main_item_unit'] ?? '');
            $colIndex++;

            // Add support items data
            foreach ($supportItems as $si) {
                $supportEntry = $db->table('daily_aahar_entries_support_items')
                    ->where(['main_entry_id' => $entry['id'], 'support_item_id' => $si['id'], 'is_disable' => '0'])
                    ->get()->getRow();
                $col = $this->getColumnLetter($colIndex + 1);
                $sheet->setCellValue($col . $row, $supportEntry ? number_format($supportEntry->qty, 3) : '0.000');
                $colIndex++;
            }
            $row++;
        }

        // Auto-size columns
        for ($i = 1; $i <= $totalCols; $i++) {
            $col = $this->getColumnLetter($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set response headers
        $filename = 'Daily_Entries_' . date('Y-m-d_His') . '.xlsx';

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
