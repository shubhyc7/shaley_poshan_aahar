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
        $itemName = trim($this->request->getPost('item_name') ?? '');
        $itemType = $this->request->getPost('item_type');
        $unit = $this->request->getPost('unit');

        // Validation
        if (empty($itemName)) {
            return redirect()->back()->withInput()->with('error', 'कृपया वस्तूचे नाव प्रविष्ट करा!');
        }
        if (!in_array($itemType, ['MAIN', 'SUPPORT'])) {
            return redirect()->back()->withInput()->with('error', 'कृपया वैध वस्तू प्रकार निवडा!');
        }
        if (empty($unit)) {
            return redirect()->back()->withInput()->with('error', 'कृपया एकक निवडा!');
        }

        // DUPLICATE VALIDATION
        $existing = $model->where([
            'item_name'  => $itemName,
            'is_disable' => 0
        ])->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', "वस्तू '$itemName' मास्टर सूचीमध्ये आधीच अस्तित्वात आहे!");
        }

        $model->save([
            'item_name'  => $itemName,
            'item_type'  => $itemType,
            'unit'       => $unit,
            'is_disable' => 0
        ]);

        return redirect()->to('/items')->with('status', 'वस्तू यशस्वीरित्या जोडली गेली!');
    }

    // edit
    public function edit($id)
    {
        $model = new ItemModel();
        $data = $model->find($id);
        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Record not found']);
        }
        return $this->response->setJSON($data);
    }

    // update
    public function update($id)
    {
        $model = new ItemModel();
        $itemName = trim($this->request->getPost('item_name') ?? '');
        $itemType = $this->request->getPost('item_type');
        $unit = $this->request->getPost('unit');

        // Validation
        if (empty($itemName)) {
            return redirect()->back()->withInput()->with('error', 'कृपया वस्तूचे नाव प्रविष्ट करा!');
        }
        if (!in_array($itemType, ['MAIN', 'SUPPORT'])) {
            return redirect()->back()->withInput()->with('error', 'कृपया वैध वस्तू प्रकार निवडा!');
        }
        if (empty($unit)) {
            return redirect()->back()->withInput()->with('error', 'कृपया एकक निवडा!');
        }

        // DUPLICATE VALIDATION (Exclude current record)
        $existing = $model->where([
            'item_name'  => $itemName,
            'is_disable' => 0
        ])->where('id !=', $id)->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', "या नावाने असलेली दुसरी सक्रिय वस्तू '$itemName' आधीच अस्तित्वात आहे!");
        }

        $model->update($id, [
            'item_name' => $itemName,
            'item_type' => $itemType,
            'unit'      => $unit,
        ]);

        return redirect()->to('/items')->with('status', 'वस्तू यशस्वीरित्या अद्यतनित झाली!');
    }

    // SOFT DELETE Logic
    public function delete($id)
    {
        $model = new ItemModel();
        $item = $model->find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'वस्तू सापडली नाही.');
        }
        $model->update($id, ['is_disable' => 1]);
        return redirect()->to('/items')->with('status', 'वस्तू यशस्वीरित्या हटवली!');
    }

    // export
    public function export()
    {
        $model = new ItemModel();
        // Export only active items
        $items = $model->getActive()->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'क्रमांक');
        $sheet->setCellValue('B1', 'वस्तूचे नाव');
        $sheet->setCellValue('C1', 'वस्तूचा प्रकार');
        $sheet->setCellValue('D1', 'एकक');

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
            $sheet->setCellValue('C' . $row, $item['item_type'] == 'MAIN' ? 'मुख्य' : 'सहाय्यक');
            $sheet->setCellValue('D' . $row, $item['unit']);
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'वस्तू_यादी_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
