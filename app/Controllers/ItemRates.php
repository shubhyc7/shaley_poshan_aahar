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

        // Get Filter Values (Default to current if not provided)
        $filterMonth = $this->request->getGet('month') ?? date('n');
        $filterYear  = $this->request->getGet('year') ?? date('Y');
        // Build the query
        $query = $rateModel->select('item_rates.*, items.item_name, items.unit,items.item_type')
            ->join('items', 'items.id = item_rates.item_id')
            ->where('item_rates.is_disable', 0);

        if ($filterMonth) {
            $query->where('item_rates.month', $filterMonth);
        }
        if ($filterYear) {
            $query->where('item_rates.year', $filterYear);
        }

        $data['rates'] = $query->findAll();
        $data['items'] = $itemModel->where('is_disable', 0)->findAll();

        // Pass filter values back to view to keep them selected
        $data['filterMonth'] = $filterMonth;
        $data['filterYear']  = $filterYear;

        return view('item_rates_view', $data);
    }

    // store
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
            return redirect()->back()->withInput()->with('error', "निवडलेल्या इयत्ता आणि महिन्यासाठी या वस्तूचा दर आधीच अस्तित्वात आहे!");
        }

        $model->save([
            'item_id'         => $item_id,
            'category'        => $category,
            'per_student_qty' => $this->request->getPost('per_student_qty'),
            'month'           => $month,
            'year'            => $year,
            'is_disable'      => 0
        ]);

        return redirect()->to('/ItemRates')->with('status', 'वापर दर यशस्वीरित्या जतन केला!');
    }

    // edit
    public function edit($id)
    {
        $model = new RateModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    // update
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
            return redirect()->back()->with('error', "या सेटिंग्जसह आधीच एक रेकॉर्ड अस्तित्वात आहे!");
        }

        $model->update($id, [
            'item_id'         => $item_id,
            'category'        => $category,
            'per_student_qty' => $this->request->getPost('per_student_qty'),
            'month'           => $month,
            'year'            => $year,
        ]);

        return redirect()->to('/ItemRates')->with('status', 'दर यशस्वीरित्या अद्यतनित केला!');
    }

    // delete
    public function delete($id)
    {
        $model = new RateModel();
        // SOFT DELETE: Mark as disabled instead of removing
        $model->update($id, ['is_disable' => 1]);

        return redirect()->to('/ItemRates')->with('status', 'दर यशस्वीरित्या हटवला!');
    }

    // export
    public function export()
    {
        $rateModel = new RateModel();
        $rates = $rateModel->getRatesWithItems();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'क्रमांक');
        $sheet->setCellValue('B1', 'वस्तू');
        $sheet->setCellValue('C1', 'वस्तू प्रकार');
        $sheet->setCellValue('D1', 'इयत्ता');
        $sheet->setCellValue('E1', 'महिना');
        $sheet->setCellValue('F1', 'वर्ष');
        $sheet->setCellValue('G1', 'प्रति विद्यार्थी प्रमाण');
        $sheet->setCellValue('H1', 'एकक');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($rates as $rate) {
            $sheet->setCellValue('A' . $row, $rate['id']);
            $sheet->setCellValue('B' . $row, $rate['item_name']);
            $sheet->setCellValue('C' . $row, $rate['item_type'] == 'MAIN' ? 'मुख्य' : 'सहाय्यक');
            $sheet->setCellValue('D' . $row, 'इयत्ता ' . $rate['category']);
            $sheet->setCellValue('E' . $row, date("F", mktime(0, 0, 0, $rate['month'], 10)));
            $sheet->setCellValue('F' . $row, $rate['year']);
            $sheet->setCellValue('G' . $row, $rate['per_student_qty']);
            $sheet->setCellValue('H' . $row, $rate['unit']);
            $row++;
        }

        // --- NEW CODE: SET DECIMAL FORMAT FOR COLUMN G ---
        // '0.000' ensures exactly 3 decimal places are shown (0.1 becomes 0.100)
        $sheet->getStyle('G2:G' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('0.000');
        // ------------------------------------------------

        foreach (range('A', 'H') as $col) { // Fixed range to include H
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'वापर_दर_प्रति_विद्यार्थी_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
