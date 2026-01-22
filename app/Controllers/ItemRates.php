<?php

namespace App\Controllers;

use App\Models\RateModel;
use App\Models\ItemModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ItemRates extends BaseController
{
    public function index()
    {
        $rateModel = new RateModel();
        $itemModel = new ItemModel();

        $data['rates'] = $rateModel->getRatesWithItems();
        // Fetch only active items for the dropdown
        $data['items'] = $itemModel->where('is_disable', 0)->findAll();

        return view('item_rates/index', $data);
    }

    public function store()
    {
        $model = new RateModel();

        $item_id  = $this->request->getPost('item_id');
        $category = $this->request->getPost('category');
        $month    = $this->request->getPost('month');
        $year     = $this->request->getPost('year');

        // DUPLICATE VALIDATION
        $existing = $model->where([
            'item_id'    => $item_id,
            'category'   => $category,
            'month'      => $month,
            'year'       => $year,
            'is_disable' => 0
        ])->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', "A rate for this item already exists for the selected category and month!");
        }

        $model->save([
            'item_id'         => $item_id,
            'category'        => $category,
            'per_student_qty' => $this->request->getPost('per_student_qty'),
            'month'           => $month,
            'year'            => $year,
            'is_disable'      => 0
        ]);

        return redirect()->to('/ItemRates')->with('status', 'Consumption Rate Saved');
    }

    public function edit($id)
    {
        $model = new RateModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    public function update($id)
    {
        $model = new RateModel();

        $item_id  = $this->request->getPost('item_id');
        $category = $this->request->getPost('category');
        $month    = $this->request->getPost('month');
        $year     = $this->request->getPost('year');

        // DUPLICATE VALIDATION (Exclude current ID)
        $existing = $model->where([
            'item_id'    => $item_id,
            'category'   => $category,
            'month'      => $month,
            'year'       => $year,
            'is_disable' => 0
        ])->where('id !=', $id)->first();

        if ($existing) {
            return redirect()->back()->with('error', "Another record already exists with these settings!");
        }

        $model->update($id, [
            'item_id'         => $item_id,
            'category'        => $category,
            'per_student_qty' => $this->request->getPost('per_student_qty'),
            'month'           => $month,
            'year'            => $year,
        ]);

        return redirect()->to('/ItemRates')->with('status', 'Rate Updated Successfully');
    }

    public function delete($id)
    {
        $model = new RateModel();
        // SOFT DELETE: Mark as disabled instead of removing
        $model->update($id, ['is_disable' => 1]);

        return redirect()->to('/ItemRates')->with('status', 'Rate Deleted Successfully');
    }

    public function export()
    {
        $rateModel = new RateModel();
        // getRatesWithItems already filters by is_disable = 0
        $rates = $rateModel->getRatesWithItems();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Item Name');
        $sheet->setCellValue('C1', 'Category');
        $sheet->setCellValue('D1', 'Month');
        $sheet->setCellValue('E1', 'Year');
        $sheet->setCellValue('F1', 'Qty Per Student');
        $sheet->setCellValue('G1', 'Unit');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($rates as $rate) {
            $sheet->setCellValue('A' . $row, $rate['id']);
            $sheet->setCellValue('B' . $row, $rate['item_name']);
            $sheet->setCellValue('C' . $row, 'Class ' . $rate['category']);
            $sheet->setCellValue('D' . $row, date("F", mktime(0, 0, 0, $rate['month'], 10)));
            $sheet->setCellValue('E' . $row, $rate['year']);
            $sheet->setCellValue('F' . $row, number_format($rate['per_student_qty'], 3));
            $sheet->setCellValue('G' . $row, $rate['unit']);
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Consumption_Rates_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
