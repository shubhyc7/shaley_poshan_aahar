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
        $data['items'] = $itemModel->where('is_active', 1)->findAll();

        return view('item_rates/index', $data);
    }

    public function store()
    {
        $model = new RateModel();
        $model->save([
            'item_id'         => $this->request->getPost('item_id'),
            'category'        => $this->request->getPost('category'),
            'per_student_qty' => $this->request->getPost('per_student_qty'),
            'month'           => $this->request->getPost('month'),
            'year'            => $this->request->getPost('year'),
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
        $model->update($id, [
            'item_id'         => $this->request->getPost('item_id'),
            'category'        => $this->request->getPost('category'),
            'per_student_qty' => $this->request->getPost('per_student_qty'),
            'month'           => $this->request->getPost('month'),
            'year'            => $this->request->getPost('year'),
        ]);
        return redirect()->to('/ItemRates')->with('status', 'Rate Updated Successfully');
    }

    public function delete($id)
    {
        (new RateModel())->delete($id);
        return redirect()->to('/ItemRates')->with('status', 'Rate Deleted');
    }

    public function export()
    {
        $rateModel = new RateModel();
        $rates = $rateModel->getRatesWithItems();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Item Name');
        $sheet->setCellValue('C1', 'Category');
        $sheet->setCellValue('D1', 'Month');
        $sheet->setCellValue('E1', 'Year');
        $sheet->setCellValue('F1', 'Qty Per Student');
        $sheet->setCellValue('G1', 'Unit');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Add data
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

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set response headers
        $filename = 'Consumption_Rates_' . date('Y-m-d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
