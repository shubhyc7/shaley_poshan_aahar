<?php

namespace App\Controllers;

use App\Models\ItemModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Items extends BaseController
{
    public function index()
    {
        $model = new ItemModel();
        $data['items'] = $model->findAll();
        return view('items/index', $data);
    }

    public function store()
    {
        $model = new ItemModel();
        $model->save([
            'item_name' => $this->request->getPost('item_name'),
            'item_type' => $this->request->getPost('item_type'),
            'unit'      => $this->request->getPost('unit'),
            'is_active' => 1
        ]);
        return redirect()->to('/items')->with('status', 'Item Added Successfully');
    }

    // Fetch single item data for the modal
    public function edit($id)
    {
        $model = new ItemModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    // Process the update
    public function update($id)
    {
        $model = new ItemModel();
        $model->update($id, [
            'item_name' => $this->request->getPost('item_name'),
            'item_type' => $this->request->getPost('item_type'),
            'unit'      => $this->request->getPost('unit'),
        ]);
        return redirect()->to('/items')->with('status', 'Item Updated Successfully');
    }

    public function delete($id)
    {
        $model = new ItemModel();
        $model->delete($id);
        return redirect()->to('/items')->with('status', 'Item Deleted');
    }

    public function export()
    {
        $model = new ItemModel();
        $items = $model->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Item Name');
        $sheet->setCellValue('C1', 'Category');
        $sheet->setCellValue('D1', 'Unit');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['item_name']);
            $sheet->setCellValue('C' . $row, $item['item_type']);
            $sheet->setCellValue('D' . $row, $item['unit']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set response headers
        $filename = 'Items_' . date('Y-m-d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
