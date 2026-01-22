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
        $stockModel = new StockModel();
        $itemModel  = new ItemModel();

        // Get selected date from URL or default to today
        $stockDate = $this->request->getGet('stock_date') ?? date('Y-m-d');

        $db = \Config\Database::connect();

        // 1. Get usage from Main Items for THIS date
        $mainUsed = $db->table('daily_aahar_entries')
            ->select('item_id, SUM(qty) as total')
            ->where(['entry_date' => $stockDate, 'is_disable' => 0])
            ->groupBy('item_id')->get()->getResultArray();

        // 2. Get usage from Support Items for THIS date
        $supportUsed = $db->table('daily_aahar_entries_support_items')
            ->select('support_item_id as item_id, SUM(qty) as total')
            ->where(['entry_date' => $stockDate, 'is_disable' => 0])
            ->groupBy('support_item_id')->get()->getResultArray();

        $usedLookup = [];
        foreach (array_merge($mainUsed, $supportUsed) as $u) {
            $usedLookup[$u['item_id']] = ($usedLookup[$u['item_id']] ?? 0) + $u['total'];
        }

        $data['stock'] = $stockModel->getStockWithItems($stockDate);
        $data['items'] = $itemModel->where('is_disable', 0)->findAll();
        $data['stockDate'] = $stockDate;
        $data['used_lookup'] = $usedLookup;

        return view('stock/index', $data);
    }

    public function store()
    {
        $model = new StockModel();
        $itemId = $this->request->getPost('item_id');
        $stockDate = $this->request->getPost('stock_date'); // The new or existing date
        $stockId = $this->request->getPost('id');

        // DUPLICATE VALIDATION
        // Check if another record (different ID) already exists for this ITEM + DATE
        $query = $model->where([
            'item_id'    => $itemId,
            'stock_date' => $stockDate,
            'is_disable' => 0
        ]);

        if ($stockId) {
            $query->where('id !=', $stockId);
        }

        if ($query->first()) {
            return redirect()->back()->withInput()->with('error', 'या तारखेसाठी या वस्तूचा स्टॉक आधीच नोंदवलेला आहे!');
        }

        $opening = (float)$this->request->getPost('opening_stock');
        $received = (float)$this->request->getPost('received_stock');
        $used = (float)$this->request->getPost('used_stock');

        $saveData = [
            'item_id'         => $itemId,
            'stock_date'      => $stockDate,
            'opening_stock'   => $opening,
            'received_stock'  => $received,
            'used_stock'      => $used,
            'remaining_stock' => ($opening + $received) - $used,
            'is_disable'      => 0
        ];

        if ($stockId) {
            $model->update($stockId, $saveData);
            $msg = "स्टॉक यशस्वीरित्या अपडेट केला!";
        } else {
            $model->save($saveData);
            $msg = "स्टॉक यशस्वीरित्या जतन केला!";
        }

        // Redirect to the date of the record that was just saved
        return redirect()->to(base_url("Stock?stock_date=$stockDate"))->with('status', $msg);
    }

    public function edit($id)
    {
        $model = new StockModel();
        return $this->response->setJSON($model->find($id));
    }

    public function delete($id)
    {
        $model = new StockModel();
        $date = $this->request->getGet('stock_date');
        $model->update($id, ['is_disable' => 1]);
        return redirect()->to(base_url("Stock?stock_date=$date"))->with('status', 'नोंद हटवण्यात आली');
    }

    public function export()
    {
        $stockModel = new StockModel();
        $month = $this->request->getGet('month') ?? date('n');
        $year  = $this->request->getGet('year') ?? date('Y');

        $stock = $stockModel->getStockWithItems($month, $year);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Stock Report - ' . date("F", mktime(0, 0, 0, $month, 10)) . ' ' . $year);
        $sheet->mergeCells('A1:F1');

        // Headers
        $headers = ['Item Name', 'Opening Stock', 'Received Stock', 'Used Stock', 'Remaining Stock', 'Unit'];
        $column = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($column . '3', $h);
            $column++;
        }

        $row = 4;
        foreach ($stock as $rowData) {
            $sheet->setCellValue('A' . $row, $rowData['item_name']);
            $sheet->setCellValue('B' . $row, $rowData['opening_stock']);
            $sheet->setCellValue('C' . $row, $rowData['received_stock']);
            $sheet->setCellValue('D' . $row, $rowData['used_stock']);
            $sheet->setCellValue('E' . $row, $rowData['remaining_stock']);
            $sheet->setCellValue('F' . $row, $rowData['unit']);
            $row++;
        }

        $filename = 'Stock_Report_' . $month . '_' . $year . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
