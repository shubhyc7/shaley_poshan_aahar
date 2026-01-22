<?php

namespace App\Controllers;

use App\Models\EntryModel;
use App\Models\ItemModel;
use App\Models\RateModel;
use App\Models\StudentStrengthModel;
use App\Models\SupportEntryModel;

class Entries extends BaseController
{
    public function index()
    {
        $itemModel = new ItemModel();
        $entryModel = new EntryModel();

        $month = date('n');
        $year = date('Y');

        $data['main_items'] = $itemModel->where('item_type', 'MAIN')->findAll();
        $data['support_items'] = $itemModel->where('item_type', 'SUPPORT')->findAll();

        // Fetch history with a complex join to get support items in one view if needed, 
        // but for simplicity, we use the model helper.
        $data['entries'] = $entryModel->select('daily_aahar_entries.*, items.item_name')
            ->join('items', 'items.id = daily_aahar_entries.item_id')
            ->orderBy('entry_date', 'DESC')->findAll();

        return view('entries/index', $data);
    }

    public function create()
    {
        $itemModel = new ItemModel();
        $rateModel = new RateModel();
        $strengthModel = new StudentStrengthModel();

        $month = date('n');
        $year = date('Y');

        // Get total students registered for this month
        $strength = $strengthModel->where(['month' => $month, 'year' => $year])->first();

        $data['total_enrolled'] = $strength['total_students'] ?? 0;
        $data['main_items'] = $itemModel->where('item_type', 'MAIN')->findAll();

        // Get rates for calculation (Main items and Support items)
        $data['rates'] = json_encode($rateModel->where(['month' => $month, 'year' => $year])->findAll());

        return view('entries/create', $data);
    }

    public function store()
    {
        $entryModel = new EntryModel();
        $supportModel = new SupportEntryModel();
        $date = $this->request->getPost('entry_date');
        $category = $this->request->getPost('category');

        // 1. Save Header Entry (Attendance)
        // We store item_id as 0 or NULL if multiple items are selected, 
        // but better to save one row per main item to keep your history logic working.

        $main_item_ids = $this->request->getPost('main_item_id'); // Now an array
        $main_qtys = $this->request->getPost('main_item_qty'); // Now an array

        foreach ($main_item_ids as $idx => $m_id) {
            $mainData = [
                'entry_date'       => $date,
                'category'         => $category,
                'total_students'   => $this->request->getPost('student_strength'),
                'present_students' => $this->request->getPost('present_students'),
                'item_id'          => $m_id,
                'qty'              => $main_qtys[$idx],
                'month'            => date('n', strtotime($date)),
                'year'             => date('Y', strtotime($date)),
            ];
            $entryModel->insert($mainData);
            $lastMainId = $entryModel->getInsertID();

            // 2. Save Support Items ONLY for the first main item 
            // (to avoid doubling salt/oil if two main items are served)
            if ($idx === 0) {
                $s_ids = $this->request->getPost('support_item_id');
                $s_qtys = $this->request->getPost('support_qty');
                if ($s_ids) {
                    foreach ($s_ids as $s_idx => $sid) {
                        if ($s_qtys[$s_idx] > 0) {
                            $supportModel->save([
                                'main_entry_id'   => $lastMainId,
                                'entry_date'      => $date,
                                'support_item_id' => $sid,
                                'qty'             => $s_qtys[$s_idx]
                            ]);
                        }
                    }
                }
            }
        }
        return redirect()->to('/entries')->with('status', 'Multiple items saved successfully!');
    }


    public function calculateAjax()
    {
        $date = $this->request->getPost('date');
        $present = (int)$this->request->getPost('present');
        $mainItemId = $this->request->getPost('main_item_id');
        $category = $this->request->getPost('category'); // '5-8' or '8-10'

        $rateModel = new RateModel();
        // Fetch rates specifically for the selected category
        $rates = $rateModel->where([
            'month'    => date('n', strtotime($date)),
            'year'     => date('Y', strtotime($date)),
            'category' => $category
        ])->findAll();

        // ... same calculation logic as before ...
    }

    public function getStrengthAjax()
    {
        $date = $this->request->getPost('date');
        $category = $this->request->getPost('category');
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));

        $model = new StudentStrengthModel();
        $strength = $model->where(['month' => $month, 'year' => $year, 'category' => $category])->first();

        return $this->response->setJSON(['total' => $strength['total_students'] ?? 0]);
    }

    public function calculate()
    {
        $date = $this->request->getPost('date');
        $present = (int)$this->request->getPost('present');
        $category = $this->request->getPost('category');

        $rateModel = new \App\Models\RateModel();
        $rates = $rateModel->where([
            'month'    => date('n', strtotime($date)),
            'year'     => date('Y', strtotime($date)),
            'category' => $category
        ])->findAll();

        $all_calculated = [];
        foreach ($rates as $rate) {
            // Calculate the theoretical amount for every item
            $all_calculated[$rate['item_id']] = number_format($present * $rate['per_student_qty'], 3, '.', '');
        }

        return $this->response->setJSON(['rates' => $all_calculated]);
    }

    public function delete($id)
    {
        $entryModel = new \App\Models\EntryModel();
        $supportModel = new \App\Models\SupportEntryModel();

        // 1. Check if the record exists
        $entry = $entryModel->find($id);

        if ($entry) {
            // 2. Delete linked support items first (using the Foreign Key)
            $supportModel->where('main_entry_id', $id)->delete();

            // 3. Delete the main entry
            $entryModel->delete($id);

            return redirect()->to('/entries')->with('status', 'Entry and linked support items deleted successfully.');
        } else {
            return redirect()->to('/entries')->with('error', 'Entry not found.');
        }
    }
}
