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

        $month = $this->request->getGet('month') ?? date('n');
        $year  = $this->request->getGet('year') ?? date('Y');

        $db = \Config\Database::connect();

        // 1. Get usage from Main Items
        $mainUsed = $db->table('daily_aahar_entries')
            ->select('item_id, SUM(qty) as total')
            ->where('month', $month)->where('year', $year)
            ->groupBy('item_id')->get()->getResultArray();

        // 2. Get usage from Support Items
        $supportUsed = $db->table('daily_aahar_entries_support_items')
            ->select('support_item_id as item_id, SUM(qty) as total')
            ->where('month(entry_date)', $month)
            ->where('year(entry_date)', $year)
            ->where('is_disable', '0')
            ->groupBy('support_item_id')->get()->getResultArray();

        $usedLookup = [];
        foreach (array_merge($mainUsed, $supportUsed) as $u) {
            $usedLookup[$u['item_id']] = ($usedLookup[$u['item_id']] ?? 0) + $u['total'];
        }

        $data['stock'] = $stockModel->getStockWithItems($month, $year);
        // Only show active items in the dropdown
        $data['items'] = $itemModel->where('is_disable', 0)->findAll();
        $data['month'] = $month;
        $data['year']  = $year;
        $data['used_lookup'] = $usedLookup;

        return view('stock/index', $data);
    }

    public function store()
    {
        $model = new StockModel();
        $itemId = $this->request->getPost('item_id');
        $month = $this->request->getPost('month');
        $year = $this->request->getPost('year');
        $stockId = $this->request->getPost('id'); // Pass ID if editing

        // DUPLICATE VALIDATION (Check if this item already has a stock entry for this period)
        $query = $model->where([
            'item_id'    => $itemId,
            'month'      => $month,
            'year'       => $year,
            'is_disable' => 0
        ]);

        if ($stockId) {
            $query->where('id !=', $stockId);
        }

        $existing = $query->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'या वस्तूचा या महिन्याचा स्टॉक आधीच नोंदवलेला आहे!');
        }

        $opening = (float)$this->request->getPost('opening_stock');
        $received = (float)$this->request->getPost('received_stock');
        $used = (float)$this->request->getPost('used_stock');

        $saveData = [
            'item_id'         => $itemId,
            'opening_stock'   => $opening,
            'received_stock'  => $received,
            'used_stock'      => $used,
            'remaining_stock' => ($opening + $received) - $used,
            'month'           => $month,
            'year'            => $year,
            'is_disable'      => 0
        ];

        if ($stockId) {
            $model->update($stockId, $saveData);
        } else {
            $model->save($saveData);
        }

        return redirect()->to(base_url("Stock?month=$month&year=$year"))->with('status', 'स्टॉक यशस्वीरित्या जतन केला!');
    }

    public function edit($id)
    {
        $model = new StockModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    // SOFT DELETE: Update is_disable to 1
    public function delete($id)
    {
        $model = new StockModel();
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');

        $model->update($id, ['is_disable' => 1]);

        return redirect()->to(base_url("Stock?month=$month&year=$year"))
            ->with('status', 'स्टॉक नोंद हटवण्यात आली (Archived)');
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
