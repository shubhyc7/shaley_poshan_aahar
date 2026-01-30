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

        // Get Filter Values from GET request
        $filterCategory = $this->request->getGet('category') ?? '';

        // Start building the query
        $query = $model->where('is_disable', 0);

        // Apply filters if they are set
        if ($filterCategory) {
            $query->where('category', $filterCategory);
        }

        $data['records'] = $query->orderBy('category', 'ASC')
            ->findAll();

        // Pass filters back to view to maintain selection
        $data['filterCategory'] = $filterCategory;

        return view('student_strength_view', $data);
    }

    // store
    public function store()
    {
        $model = new StudentStrengthModel();

        $category = $this->request->getPost('category');

        // DUPLICATE VALIDATION
        $existing = $model->where([
            'category'   => $category,
            'is_disable' => 0
        ])->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', "या $category चा विद्यार्थी संख्या आधीच अस्तित्वात आहे!");
        }

        $model->save([
            'category'       => $category,
            'total_students' => $this->request->getPost('total_students'),
            'is_disable'     => 0
        ]);

        return redirect()->to('/StudentStrength?category=' . $category)->with('status', 'विद्यार्थी संख्या यशस्वीरित्या जोडली गेली!');
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

        // DUPLICATE VALIDATION (Exclude current ID)
        $existing = $model->where([
            'category'   => $category,
            'is_disable' => 0
        ])->where('id !=', $id)->first();

        if ($existing) {
            return redirect()->back()->with('error', "या महिना/वर्षात $category साठी आधीच एक दुसरा रेकॉर्ड अस्तित्वात आहे!");
        }

        $model->update($id, [
            'category'       => $category,
            'total_students' => $this->request->getPost('total_students'),
        ]);

        return redirect()->to('/StudentStrength?category=' . $category)->with('status', 'विद्यार्थी संख्या यशस्वीरित्या अद्यतनित केली!');
    }

    // SOFT DELETE Logic
    public function delete($id)
    {
        $filterCategory = $this->request->getGet('category') ?? '';

        $model = new StudentStrengthModel();

        // Instead of true delete, we update is_disable to 1
        $model->update($id, ['is_disable' => 1]);

        return redirect()->to('/StudentStrength?category=' . $filterCategory)->with('status', 'विद्यार्थी संख्या यशस्वीरित्या हटवली!');
    }

    // export
    public function export()
    {
        $filterCategory = $this->request->getGet('category') ?? '';


        $model = new StudentStrengthModel();
        // Export only active records
        // १. आधी क्विरी बिल्डर तयार करा (findAll() नका वापरू)
        $query = $model->getActive()->orderBy('category', 'ASC');

        // २. जर फिल्टर असेल, तर तो क्विरीमध्ये ॲड करा
        if ($filterCategory) {
            $query->where('category', $filterCategory);
        }

        // ३. शेवटी रिझल्ट्स मिळवण्यासाठी findAll() वापरा
        $records = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'क्रमांक');
        $sheet->setCellValue('B1', 'इयत्ता');
        $sheet->setCellValue('C1', 'एकूण विद्यार्थी');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A' . $row, $record['id']);
            $sheet->setCellValue('B' . $row, 'इयत्ता ' . $record['category']);
            $sheet->setCellValue('C' . $row, $record['total_students']);
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'विद्यार्थी_संख्या_यादी_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
