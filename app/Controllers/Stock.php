<?php

namespace App\Controllers;

use App\Models\StockModel;
use App\Models\ItemModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Stock extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Get inputs
        $month = $this->request->getGet('month') ?? date('n');
        $year  = $this->request->getGet('year') ?? date('Y');
        $itemId = $this->request->getGet('item_id');

        // Fetch items
        $itemModel = new ItemModel();
        $allItems = $itemModel->where('is_disable', 0)->findAll();

        // Define the start of the current filtered month
        $startDate = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";

        // 1. Calculate Opening Balances (Cumulative sum of ALL history before $startDate)
        $openingBalances = [];
        foreach ($allItems as $item) {
            $prev = $db->table('stock_transactions')
                ->select("SUM(CASE 
                WHEN UPPER(transaction_type) = 'OPENING' THEN quantity 
                WHEN UPPER(transaction_type) = 'IN' THEN quantity 
                WHEN UPPER(transaction_type) = 'OUT' THEN -quantity 
                ELSE 0 END) as bal", false)
                ->where('item_id', $item['id'])
                ->where('transaction_date <', $startDate) // Everything before 1st of the month
                ->where('is_disable', 0)
                ->get()->getRow();

            $openingBalances[$item['id']] = (float)($prev->bal ?? 0);
        }

        // 2. Fetch Transactions for the selected month
        $builder = $db->table('stock_transactions as st')
            ->select('st.*, items.item_name, items.unit')
            ->join('items', 'items.id = st.item_id')
            ->where('MONTH(st.transaction_date)', $month)
            ->where('YEAR(st.transaction_date)', $year)
            ->where('st.is_disable', 0);

        if (!empty($itemId)) {
            $builder->where('st.item_id', $itemId);
        }

        // Crucial: Order by date and then ID to keep running balance consistent
        $transactions = $builder->orderBy('st.transaction_date', 'ASC')
            ->orderBy('st.id', 'ASC')
            ->get()->getResultArray();

        // 3. Calculate Running Balances for the table rows
        // Use a copy of opening balances so we don't destroy the master array
        $currentRunning = $openingBalances;

        foreach ($transactions as &$tr) {
            $id = $tr['item_id'];
            $tr['opening_bal'] = $currentRunning[$id];

            $type = strtoupper($tr['transaction_type']);
            if ($type == 'OUT') {
                $currentRunning[$id] -= (float)$tr['quantity'];
            } else {
                // Both 'IN' and 'OPENING' (if entered within this month) add to balance
                $currentRunning[$id] += (float)$tr['quantity'];
            }
            $tr['closing_bal'] = $currentRunning[$id];
        }

        return view('stock_view', [
            'transactions'     => $transactions,
            'items'            => $allItems,
            'month'            => $month,
            'year'             => $year,
            'selected_item'    => $itemId,
            'opening_balances' => $openingBalances
        ]);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $itemId = $this->request->getPost('item_id');
        $type   = $this->request->getPost('transaction_type');
        $id     = $this->request->getPost('id');
        $date   = $this->request->getPost('transaction_date');

        // Capture filters from hidden fields to preserve state after save
        $f_month = $this->request->getPost('filter_month');
        $f_year  = $this->request->getPost('filter_year');
        $f_item  = $this->request->getPost('filter_item_id');

        if ($type === 'OPENING') {
            $check = $db->table('stock_transactions')->where(['item_id' => $itemId, 'transaction_type' => 'OPENING', 'is_disable' => 0]);
            if ($id) $check->where('id !=', $id);
            if ($check->countAllResults() > 0) return redirect()->back()->with('error', 'ओपनिंग स्टॉक आधीच आहे.');
        }

        $saveData = [
            'item_id'          => $itemId,
            'transaction_type' => $type,
            'transaction_date' => $date,
            'quantity'         => $this->request->getPost('quantity'),
            'remarks'          => $this->request->getPost('remarks'),
            'is_disable'       => 0
        ];

        if ($id) {
            $db->table('stock_transactions')->where('id', $id)->update($saveData);
        } else {
            $db->table('stock_transactions')->insert($saveData);
        }

        return redirect()->to(base_url("Stock?month=$f_month&year=$f_year&item_id=$f_item"))->with('status', 'स्टॉक नोंद यशस्वीरित्या जतन केले!');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();

        // Capture filters from URL query string
        $f_month = $this->request->getGet('month');
        $f_year  = $this->request->getGet('year');
        $f_item  = $this->request->getGet('item_id');

        $row = $db->table('stock_transactions')->where('id', $id)->get()->getRow();
        if ($row && $row->transaction_type != 'OUT') {
            $db->table('stock_transactions')->where('id', $id)->update(['is_disable' => 1]);
            return redirect()->to(base_url("Stock?month=$f_month&year=$f_year&item_id=$f_item"))->with('status', 'स्टॉक नोंद यशस्वीरित्या हटवली!');
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

        // 2. Initial Setup & Opening Balance Calculation
        $itemModel = new \App\Models\ItemModel();
        $filterItems = $itemId ? $itemModel->where('id', $itemId)->findAll() : $itemModel->where('is_disable', 0)->findAll();

        $startDate = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";
        $openingBalances = [];
        $totalMonthOpening = 0;

        foreach ($filterItems as $item) {
            $prev = $db->table('stock_transactions')
                ->select("SUM(CASE 
                WHEN UPPER(transaction_type) IN ('OPENING', 'IN') THEN quantity 
                WHEN UPPER(transaction_type) = 'OUT' THEN -quantity 
                ELSE 0 END) as bal", false)
                ->where('item_id', $item['id'])
                ->where('transaction_date <', $startDate)
                ->where('is_disable', 0)
                ->get()->getRow();

            $bal = (float)($prev->bal ?? 0);
            $openingBalances[$item['id']] = $bal;
            $totalMonthOpening += $bal;
        }

        // 3. Fetch Transactions for the Month
        $builder = $db->table('stock_transactions as st')
            ->select('st.*, items.item_name, items.unit')
            ->join('items', 'items.id = st.item_id')
            ->where('MONTH(st.transaction_date)', $month)
            ->where('YEAR(st.transaction_date)', $year)
            ->where('st.is_disable', 0);

        if ($itemId) $builder->where('st.item_id', $itemId);
        $transactions = $builder->orderBy('st.transaction_date', 'ASC')->orderBy('st.id', 'ASC')->get()->getResultArray();

        // 4. Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Title
        $sheet->setCellValue('A1', 'Stock Ledger Report: ' . date("F", mktime(0, 0, 0, $month, 10)) . ' ' . $year);
        $sheet->mergeCells('A1:G1');

        // Set Headers
        $headers = ['Date', 'Item Name', 'Type', 'Opening Bal', 'Trans Qty', 'Closing Bal', 'Remarks'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '3', $h);
            $col++;
        }

        // 5. Fill Data with Running Balance Logic
        $row = 4;
        $tIn = 0;
        $tOut = 0;
        $currentRunning = $openingBalances;

        foreach ($transactions as $tr) {
            $id = $tr['item_id'];
            $open = $currentRunning[$id];

            $type = strtoupper($tr['transaction_type']);
            $qty = (float)$tr['quantity'];

            if ($type == 'OUT') {
                $currentRunning[$id] -= $qty;
                $tOut += $qty;
                $displayQty = "-" . number_format($qty, 3);
            } else {
                $currentRunning[$id] += $qty;
                $tIn += $qty;
                $displayQty = "+" . number_format($qty, 3);
            }

            $sheet->setCellValue('A' . $row, date('d-m-Y', strtotime($tr['transaction_date'])));
            $sheet->setCellValue('B' . $row, $tr['item_name'] . ' (' . $tr['unit'] . ')');
            $sheet->setCellValue('C' . $row, $tr['transaction_type']);
            $sheet->setCellValue('D' . $row, number_format($open, 3));
            $sheet->setCellValue('E' . $row, $displayQty);
            $sheet->setCellValue('F' . $row, number_format($currentRunning[$id], 3));
            $sheet->setCellValue('G' . $row, $tr['remarks']);
            $row++;
        }

        // 6. Add Summary Footer
        $sheet->setCellValue('A' . $row, 'TOTAL SUMMARY');
        $sheet->mergeCells("A$row:C$row");
        $sheet->setCellValue('D' . $row, number_format($totalMonthOpening, 3));
        $sheet->setCellValue('E' . $row, "IN: +$tIn | OUT: -$tOut");
        $sheet->setCellValue('F' . $row, number_format(($totalMonthOpening + $tIn - $tOut), 3));

        // Styling
        $sheet->getStyle("A3:G3")->getFont()->setBold(true);
        $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Download
        $filename = 'स्टॉक_नोंद_' . $month . '_' . $year . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }
}
