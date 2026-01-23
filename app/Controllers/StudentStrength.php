<?php

namespace App\Controllers;

use App\Models\StudentStrengthModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentStrength extends BaseController
{
    // student_strength_view
    public function index()
    {
        $model = new StudentStrengthModel();
        // Only show records where is_disable = 0
        $data['records'] = $model->getActive()
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->findAll();
        return view('student_strength_view', $data);
    }

    // store
    public function store()
    {
        $model = new StudentStrengthModel();

        $category = $this->request->getPost('category');
        $month    = $this->request->getPost('month');
        $year     = $this->request->getPost('year');

        // DUPLICATE VALIDATION
        $existing = $model->where([
            'category'   => $category,
            'month'      => $month,
            'year'       => $year,
            'is_disable' => 0
        ])->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Data already exists for $category in this Month/Year!");
        }

        $model->save([
            'category'       => $category,
            'total_students' => $this->request->getPost('total_students'),
            'month'          => $month,
            'year'           => $year,
            'is_disable'     => 0
        ]);

        return redirect()->to('/StudentStrength')->with('status', 'Strength Saved');
    }

    // edit
    public function edit($id)
    {
        $model = new StudentStrengthModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    // update
    public function update($id)
    {
        $model = new StudentStrengthModel();

        $category = $this->request->getPost('category');
        $month    = $this->request->getPost('month');
        $year     = $this->request->getPost('year');

        // DUPLICATE VALIDATION (Exclude current ID)
        $existing = $model->where([
            'category'   => $category,
            'month'      => $month,
            'year'       => $year,
            'is_disable' => 0
        ])->where('id !=', $id)->first();

        if ($existing) {
            return redirect()->back()->with('error', "Another record already exists for $category in this Month/Year!");
        }

        $model->update($id, [
            'category'       => $category,
            'total_students' => $this->request->getPost('total_students'),
            'month'          => $month,
            'year'           => $year,
        ]);

        return redirect()->to('/StudentStrength')->with('status', 'Strength Updated Successfully');
    }

    // SOFT DELETE Logic
    public function delete($id)
    {
        $model = new StudentStrengthModel();

        // Instead of true delete, we update is_disable to 1
        $model->update($id, ['is_disable' => 1]);

        return redirect()->to('/StudentStrength')->with('status', 'Record Deleted Successfully');
    }

    // export
    public function export()
    {
        $model = new StudentStrengthModel();
        // Export only active records
        $records = $model->getActive()->orderBy('year', 'DESC')->orderBy('month', 'DESC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Category');
        $sheet->setCellValue('C1', 'Month');
        $sheet->setCellValue('D1', 'Year');
        $sheet->setCellValue('E1', 'Total Students');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A' . $row, $record['id']);
            $sheet->setCellValue('B' . $row, 'Class ' . $record['category']);
            $sheet->setCellValue('C' . $row, date("F", mktime(0, 0, 0, $record['month'], 10)));
            $sheet->setCellValue('D' . $row, $record['year']);
            $sheet->setCellValue('E' . $row, $record['total_students']);
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Student_Strength_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
