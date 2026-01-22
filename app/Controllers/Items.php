<?php

namespace App\Controllers;

use App\Models\ItemModel;

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
}
