<?php

namespace App\Controllers;

use App\Models\RateModel;
use App\Models\ItemModel;

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
}
