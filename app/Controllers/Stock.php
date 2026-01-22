<?php

namespace App\Controllers;

use App\Models\StockModel;
use App\Models\ItemModel;
use App\Models\EntryModel;
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
        $entryModel = new EntryModel();

        $month = $this->request->getGet('month') ?? date('n');
        $year  = $this->request->getGet('year') ?? date('Y');

        // Logic: Calculate total used from daily entries for this month
        $usedData = $entryModel->select('item_id, SUM(qty) as total_used')
            ->where('month', $month)->where('year', $year)
            ->groupBy('item_id')->findAll();

        $data['stock'] = $stockModel->getStockWithItems($month, $year);
        $data['items'] = $itemModel->findAll();
        $data['month'] = $month;
        $data['year']  = $year;
        $data['used_lookup'] = array_column($usedData, 'total_used', 'item_id');

        return view('stock/index', $data);
    }

    public function store()
    {

        $model = new StockModel();
        $itemId = $this->request->getPost('item_id');
        $opening = $this->request->getPost('opening_stock');
        $received = $this->request->getPost('received_stock');
        $used = $this->request->getPost('used_stock') ?? 0;

        $model->save([
            'item_id'         => $itemId,
            'opening_stock'   => $opening,
            'received_stock'  => $received,
            'used_stock'      => $used,
            'remaining_stock' => ($opening + $received) - $used,
            'month'           => $this->request->getPost('month'),
            'year'            => $this->request->getPost('year'),
        ]);


        return redirect()->to('/stock')->with('status', 'Stock Updated Successfully');
    }

    public function export()
    {
        $stockModel = new StockModel();
        $month = $this->request->getGet('month') ?? date('n');
        $year  = $this->request->getGet('year') ?? date('Y');

        $stock = $stockModel->getStockWithItems($month, $year);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'Stock Report - ' . date("F", mktime(0, 0, 0, $month, 10)) . ' ' . $year);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $sheet->setCellValue('A3', 'Item Name');
        $sheet->setCellValue('B3', 'Opening Stock');
        $sheet->setCellValue('C3', 'Received Stock');
        $sheet->setCellValue('D3', 'Used Stock');
        $sheet->setCellValue('E3', 'Remaining Stock');
        $sheet->setCellValue('F3', 'Unit');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A3:F3')->applyFromArray($headerStyle);

        // Add data
        $row = 4;
        foreach ($stock as $rowData) {
            $sheet->setCellValue('A' . $row, $rowData['item_name']);
            $sheet->setCellValue('B' . $row, number_format($rowData['opening_stock'], 3));
            $sheet->setCellValue('C' . $row, number_format($rowData['received_stock'], 3));
            $sheet->setCellValue('D' . $row, number_format($rowData['used_stock'], 3));
            $sheet->setCellValue('E' . $row, number_format($rowData['remaining_stock'], 3));
            $sheet->setCellValue('F' . $row, $rowData['unit']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set response headers
        $filename = 'Stock_Report_' . date("F", mktime(0, 0, 0, $month, 10)) . '_' . $year . '_' . date('Y-m-d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
