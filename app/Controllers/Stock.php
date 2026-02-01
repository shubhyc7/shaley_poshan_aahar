<?php

namespace App\Controllers;

use App\Models\StockModel;
use App\Models\ItemModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Stock extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Get inputs (validate month 1-12, year 2020-2030)
        $month = (int)($this->request->getGet('month') ?? date('n'));
        $year  = (int)($this->request->getGet('year') ?? date('Y'));
        $month = ($month < 1 || $month > 12) ? (int)date('n') : $month;
        $year  = ($year < 2020 || $year > 2030) ? (int)date('Y') : $year;
        $itemId = $this->request->getGet('item_id');
        $category = trim($this->request->getGet('category') ?? '');

        // Fetch items
        $itemModel = new ItemModel();
        $allItems = $itemModel->where('is_disable', 0)->findAll();

        // Define the start of the current filtered month
        $startDate = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";

        // 1. Calculate Opening Balances per (item_id, category)
        $openingBalances = [];
        foreach ($allItems as $item) {
            foreach (['1-5', '6-8'] as $cat) {
                if ($category && $category !== $cat) continue;
                $prev = $db->table('stock_transactions')
                    ->select("SUM(CASE 
                    WHEN UPPER(transaction_type) = 'OPENING' THEN quantity 
                    WHEN UPPER(transaction_type) = 'IN' THEN quantity 
                    WHEN UPPER(transaction_type) = 'OUT' THEN -quantity 
                    ELSE 0 END) as bal", false)
                    ->where('item_id', $item['id'])
                    ->where('category', $cat)
                    ->where('transaction_date <', $startDate)
                    ->where('is_disable', 0)
                    ->get()->getRow();

                $key = $item['id'] . '_' . $cat;
                $openingBalances[$key] = (float)($prev->bal ?? 0);
            }
        }

        // 2. Fetch Transactions for the selected month (category-wise)
        $builder = $db->table('stock_transactions as st')
            ->select('st.*, items.item_name, items.unit')
            ->join('items', 'items.id = st.item_id')
            ->where('MONTH(st.transaction_date)', $month)
            ->where('YEAR(st.transaction_date)', $year)
            ->where('st.is_disable', 0);

        if (!empty($category)) {
            $builder->where('st.category', $category);
        }
        if (!empty($itemId)) {
            $builder->where('st.item_id', $itemId);
        }

        $transactions = $builder->orderBy('st.category', 'ASC')
            ->orderBy('st.transaction_date', 'ASC')
            ->orderBy('st.id', 'ASC')
            ->get()->getResultArray();

        // 3. Calculate Running Balances per (item_id, category)
        $currentRunning = $openingBalances;

        foreach ($transactions as &$tr) {
            $key = $tr['item_id'] . '_' . $tr['category'];
            if (!isset($currentRunning[$key])) {
                $currentRunning[$key] = 0;
            }
            $tr['opening_bal'] = $currentRunning[$key];

            $type = strtoupper($tr['transaction_type']);
            if ($type == 'OUT') {
                $currentRunning[$key] -= (float)$tr['quantity'];
            } else {
                $currentRunning[$key] += (float)$tr['quantity'];
            }
            $tr['closing_bal'] = $currentRunning[$key];
        }

        return view('stock_view', [
            'transactions'     => $transactions,
            'items'            => $allItems,
            'month'            => $month,
            'year'             => $year,
            'selected_item'    => $itemId,
            'selected_category'=> $category,
            'opening_balances' => $openingBalances
        ]);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $itemId = $this->request->getPost('item_id');
        $type   = $this->request->getPost('transaction_type');
        $id     = $this->request->getPost('id');
        $date   = trim($this->request->getPost('transaction_date') ?? '');
        $quantity = (float)($this->request->getPost('quantity') ?? 0);
        $category = trim($this->request->getPost('category') ?? '');

        $f_month = $this->request->getPost('filter_month') ?? date('n');
        $f_year  = $this->request->getPost('filter_year') ?? date('Y');
        $f_item  = $this->request->getPost('filter_item_id') ?? '';
        $f_category = $this->request->getPost('filter_category') ?? '';

        // Validation
        if (empty($date)) {
            return redirect()->back()->withInput()->with('error', 'कृपया तारीख निवडा!');
        }
        if (empty($itemId)) {
            return redirect()->back()->withInput()->with('error', 'कृपया वस्तू निवडा!');
        }
        if (empty($category)) {
            return redirect()->back()->withInput()->with('error', 'कृपया इयत्ता निवडा!');
        }
        if ($quantity <= 0) {
            return redirect()->back()->withInput()->with('error', 'परिमाण ० पेक्षा जास्त असणे आवश्यक आहे!');
        }

        if ($type === 'OPENING') {
            $builder = $db->table('stock_transactions')->where(['item_id' => $itemId, 'category' => $category, 'transaction_type' => 'OPENING', 'is_disable' => 0]);
            if ($id) $builder->where('id !=', $id);
            if ($builder->countAllResults() > 0) {
                return redirect()->back()->withInput()->with('error', 'या इयत्तेसाठी ओपनिंग स्टॉक आधीच आहे.');
            }
        }

        $saveData = [
            'item_id'          => $itemId,
            'category'         => $category,
            'transaction_type' => $type,
            'transaction_date' => $date,
            'quantity'         => $quantity,
            'remarks'          => $this->request->getPost('remarks'),
            'is_disable'       => 0
        ];

        if ($id) {
            $db->table('stock_transactions')->where('id', $id)->update($saveData);
        } else {
            $db->table('stock_transactions')->insert($saveData);
        }

        $redirectUrl = "Stock?month=$f_month&year=$f_year";
        if (!empty($f_category)) $redirectUrl .= "&category=" . urlencode($f_category);
        if (!empty($f_item)) $redirectUrl .= "&item_id=$f_item";
        return redirect()->to(base_url($redirectUrl))->with('status', 'स्टॉक नोंद यशस्वीरित्या जतन केले!');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();

        $f_month = $this->request->getGet('month');
        $f_year  = $this->request->getGet('year');
        $f_item  = $this->request->getGet('item_id');
        $f_category = $this->request->getGet('category');

        $row = $db->table('stock_transactions')->where('id', $id)->get()->getRow();
        if (!$row) {
            return redirect()->back()->with('error', 'नोंद सापडली नाही.');
        }
        if ($row->transaction_type != 'OUT') {
            $db->table('stock_transactions')->where('id', $id)->update(['is_disable' => 1]);
            $redirectUrl = "Stock?month=$f_month&year=$f_year";
            if (!empty($f_category)) $redirectUrl .= "&category=" . urlencode($f_category);
            if (!empty($f_item)) $redirectUrl .= "&item_id=$f_item";
            return redirect()->to(base_url($redirectUrl))->with('status', 'स्टॉक नोंद यशस्वीरित्या हटवली!');
        }
        return redirect()->back()->with('error', 'खर्च (OUT) नोंद हटवता येत नाही.');
    }

    public function edit($id)
    {
        $model = new StockModel();
        return $this->response->setJSON($model->find($id));
    }


    public function export()
    {
        $db = \Config\Database::connect();

        // 1. Get Filters
        $month = $this->request->getGet('month') ?? date('n');
        $year  = $this->request->getGet('year') ?? date('Y');
        $itemId = $this->request->getGet('item_id');
        $category = trim($this->request->getGet('category') ?? '');

        // 2. Initial Setup & Opening Balance Calculation (category-wise)
        $itemModel = new \App\Models\ItemModel();
        $filterItems = $itemId ? $itemModel->where('id', $itemId)->findAll() : $itemModel->where('is_disable', 0)->findAll();

        $startDate = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";
        $openingBalances = [];
        $totalMonthOpening = 0;

        foreach ($filterItems as $item) {
            foreach (['1-5', '6-8'] as $cat) {
                if ($category && $category !== $cat) continue;
                $prev = $db->table('stock_transactions')
                    ->select("SUM(CASE 
                WHEN UPPER(transaction_type) IN ('OPENING', 'IN') THEN quantity 
                WHEN UPPER(transaction_type) = 'OUT' THEN -quantity 
                ELSE 0 END) as bal", false)
                    ->where('item_id', $item['id'])
                    ->where('category', $cat)
                    ->where('transaction_date <', $startDate)
                    ->where('is_disable', 0)
                    ->get()->getRow();

                $bal = (float)($prev->bal ?? 0);
                $key = $item['id'] . '_' . $cat;
                $openingBalances[$key] = $bal;
                $totalMonthOpening += $bal;
            }
        }

        // 3. Fetch Transactions for the Month (category-wise)
        $builder = $db->table('stock_transactions as st')
            ->select('st.*, items.item_name, items.unit')
            ->join('items', 'items.id = st.item_id')
            ->where('MONTH(st.transaction_date)', $month)
            ->where('YEAR(st.transaction_date)', $year)
            ->where('st.is_disable', 0);

        if ($category) $builder->where('st.category', $category);
        if ($itemId) $builder->where('st.item_id', $itemId);
        $transactions = $builder->orderBy('st.category', 'ASC')->orderBy('st.transaction_date', 'ASC')->orderBy('st.id', 'ASC')->get()->getResultArray();

        // 4. Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Title
        $sheet->setCellValue('A1', 'स्टॉक नोंद : ' . date("F", mktime(0, 0, 0, $month, 10)) . ' ' . $year . ($category ? ' (इयत्ता ' . $category . ')' : ''));
        $sheet->mergeCells('A1:H1');

        // Set Headers
        $headers = ['तारीख', 'इयत्ता', 'वस्तू (एकक)', 'प्रकार', 'प्रारंभिक (Opening)', 'आवक / खर्च (Qty)', 'शिल्लक (Closing)', 'शेरा'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '3', $h);
            $col++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet->getStyle('A3:H3')->applyFromArray($headerStyle);

        // 5. Fill Data with Running Balance Logic (category-wise)
        $row = 4;
        $tIn = 0;
        $tOut = 0;
        $currentRunning = $openingBalances;

        foreach ($transactions as $tr) {
            $key = $tr['item_id'] . '_' . $tr['category'];
            $open = $currentRunning[$key] ?? 0;

            $type = strtoupper($tr['transaction_type']);
            $qty = (float)$tr['quantity'];

            if ($type == 'OUT') {
                $currentRunning[$key] = ($currentRunning[$key] ?? 0) - $qty;
                $tOut += $qty;
                $sheet->setCellValue('F' . $row, $qty * -1);
            } else {
                $currentRunning[$key] = ($currentRunning[$key] ?? 0) + $qty;
                $tIn += $qty;
                $sheet->setCellValue('F' . $row, $qty);
            }

            $sheet->setCellValue('A' . $row, date('d-m-Y', strtotime($tr['transaction_date'])));
            $sheet->setCellValue('B' . $row, $tr['category']);
            $sheet->setCellValue('C' . $row, $tr['item_name'] . ' (' . $tr['unit'] . ')');
            $sheet->setCellValue('D' . $row, $tr['transaction_type']);
            $sheet->setCellValue('E' . $row, $open);
            $sheet->setCellValue('G' . $row, $currentRunning[$key] ?? 0);
            $sheet->setCellValue('H' . $row, $tr['remarks']);
            $row++;
        }

        // --- APPLY FIVE DECIMAL FORMATTING ---
        $sheet->getStyle("E4:G" . ($row - 1))->getNumberFormat()->setFormatCode('0.00000');
        $sheet->getStyle("F4:F" . ($row - 1))->getNumberFormat()->setFormatCode('[Red]-0.00000;0.00000');

        // 6. Add Summary Footer
        $sheet->setCellValue('A' . $row, 'एकूण सारांश');
        $sheet->mergeCells("A$row:D$row");

        $sheet->setCellValue('E' . $row, $totalMonthOpening);
        $sheet->setCellValue('F' . $row, "IN: +$tIn | OUT: -$tOut");
        $sheet->setCellValue('G' . $row, ($totalMonthOpening + $tIn - $tOut));

        // Format footer numbers
        $sheet->getStyle("E$row")->getNumberFormat()->setFormatCode('0.00000');
        $sheet->getStyle("G$row")->getNumberFormat()->setFormatCode('"Closing: "0.00000');

        // Styling
        $sheet->getStyle("A$row:H$row")->getFont()->setBold(true);
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Download
        $filename = 'स्टॉक_नोंद_' . $month . '_' . $year . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
        exit;
    }
}
