<?php

namespace App\Controllers;

use App\Models\ItemModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Items extends BaseController
{
    // items_view
    public function index()
    {
        $model = new ItemModel();
        // Only show active items
        $data['items'] = $model->getActive()->findAll();
        return view('items_view', $data);
    }

    // store
    public function store()
    {
        $model = new ItemModel();
        $itemName = trim($this->request->getPost('item_name'));

        // DUPLICATE VALIDATION
        $existing = $model->where([
            'item_name'  => $itemName,
            'is_disable' => 0
        ])->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Item '$itemName' already exists in the master list!");
        }

        $model->save([
            'item_name' => $itemName,
            'item_type' => $this->request->getPost('item_type'),
            'unit'      => $this->request->getPost('unit'),
            'is_disable' => 0
        ]);

        return redirect()->to('/items')->with('status', 'Item Added Successfully');
    }

    // edit
    public function edit($id)
    {
        $model = new ItemModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    // update
    public function update($id)
    {
        $model = new ItemModel();
        $itemName = trim($this->request->getPost('item_name'));

        // DUPLICATE VALIDATION (Exclude current record)
        $existing = $model->where([
            'item_name'  => $itemName,
            'is_disable' => 0
        ])->where('id !=', $id)->first();

        if ($existing) {
            return redirect()->back()->with('error', "Another active item with the name '$itemName' already exists!");
        }

        $model->update($id, [
            'item_name' => $itemName,
            'item_type' => $this->request->getPost('item_type'),
            'unit'      => $this->request->getPost('unit'),
        ]);

        return redirect()->to('/items')->with('status', 'Item Updated Successfully');
    }

    // SOFT DELETE Logic
    public function delete($id)
    {
        $model = new ItemModel();

        // Update is_disable instead of deleting the row
        $model->update($id, ['is_disable' => 1]);

        return redirect()->to('/items')->with('status', 'Item Deleted Successfully (Archived)');
    }

    // export
    public function export()
    {
        $model = new ItemModel();
        // Export only active items
        $items = $model->getActive()->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Item Name');
        $sheet->setCellValue('C1', 'Category');
        $sheet->setCellValue('D1', 'Unit');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['item_name']);
            $sheet->setCellValue('C' . $row, $item['item_type']);
            $sheet->setCellValue('D' . $row, $item['unit']);
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Items_Master_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
