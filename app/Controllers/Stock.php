<?php

namespace App\Controllers;

use App\Models\StockModel;
use App\Models\ItemModel;
use App\Models\EntryModel;

class Stock extends BaseController
{
    public function index()
    {
        $stockModel = new StockModel();
        $itemModel  = new ItemModel();
        $entryModel = new EntryModel();

        $month = $this->request->getGet('month') ?? date('n');
        $year  = $this->request->getGet('year') ?? date('Y');

        // Logic: Calculate total used from daily entries for this month
        $usedData = $entryModel->select('item_id, SUM(qty) as total_used')
            ->where('month', $month)->where('year', $year)
            ->groupBy('item_id')->findAll();

        $data['stock'] = $stockModel->getStockWithItems($month, $year);
        $data['items'] = $itemModel->findAll();
        $data['month'] = $month;
        $data['year']  = $year;
        $data['used_lookup'] = array_column($usedData, 'total_used', 'item_id');

        return view('stock/index', $data);
    }

    public function store()
    {

        $model = new StockModel();
        $itemId = $this->request->getPost('item_id');
        $opening = $this->request->getPost('opening_stock');
        $received = $this->request->getPost('received_stock');
        $used = $this->request->getPost('used_stock') ?? 0;

        $model->save([
            'item_id'         => $itemId,
            'opening_stock'   => $opening,
            'received_stock'  => $received,
            'used_stock'      => $used,
            'remaining_stock' => ($opening + $received) - $used,
            'month'           => $this->request->getPost('month'),
            'year'            => $this->request->getPost('year'),
        ]);


        return redirect()->to('/stock')->with('status', 'Stock Updated Successfully');
    }
}
