<?php

namespace App\Controllers;

use App\Models\StudentStrengthModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentStrength extends BaseController
{
    public function index()
    {
        $model = new StudentStrengthModel();
        $data['records'] = $model->orderBy('year', 'DESC')->orderBy('month', 'DESC')->findAll();
        return view('student_strength/index', $data);
    }

    public function store()
    {
        $model = new StudentStrengthModel();
        $model->save([
            'category'       => $this->request->getPost('category'),
            'total_students' => $this->request->getPost('total_students'),
            'month'          => $this->request->getPost('month'),
            'year'           => $this->request->getPost('year'),
        ]);
        return redirect()->to('/StudentStrength')->with('status', 'Strength Saved');
    }

    // Fetch single record for the Edit Modal
    public function edit($id)
    {
        $model = new StudentStrengthModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    // Save updated data
    public function update($id)
    {
        $model = new StudentStrengthModel();
        $model->update($id, [
            'category'       => $this->request->getPost('category'),
            'total_students' => $this->request->getPost('total_students'),
            'month'          => $this->request->getPost('month'),
            'year'           => $this->request->getPost('year'),
        ]);

        return redirect()->to('/StudentStrength')->with('status', 'Strength Updated Successfully');
    }
    public function delete($id)
    {
        $model = new StudentStrengthModel();
        $model->delete($id);
        return redirect()->to('/StudentStrength')->with('status', 'Record Deleted');
    }

    public function export()
    {
        $model = new StudentStrengthModel();
        $records = $model->orderBy('year', 'DESC')->orderBy('month', 'DESC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Category');
        $sheet->setCellValue('C1', 'Month');
        $sheet->setCellValue('D1', 'Year');
        $sheet->setCellValue('E1', 'Total Students');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A' . $row, $record['id']);
            $sheet->setCellValue('B' . $row, 'Class ' . $record['category']);
            $sheet->setCellValue('C' . $row, date("F", mktime(0, 0, 0, $record['month'], 10)));
            $sheet->setCellValue('D' . $row, $record['year']);
            $sheet->setCellValue('E' . $row, $record['total_students']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set response headers
        $filename = 'Student_Strength_' . date('Y-m-d_His') . '.xlsx';
        
        $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->setHeader('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $this->response->setHeader('Cache-Control', 'max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
