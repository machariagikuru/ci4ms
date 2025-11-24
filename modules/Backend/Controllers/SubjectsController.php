<?php

namespace Modules\Backend\Controllers;

use Modules\Backend\Controllers\BaseController;

class SubjectsController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $subjects = $db->table('subjects')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResult();

        $this->defData['subjects'] = $subjects;
        return view('Modules\Backend\Views\subjects\list', $this->defData);
    }

    public function create()
    {
        return view('Modules\Backend\Views\subjects\create', $this->defData);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[255]|is_unique[subjects.name]',
        ];

        if (! $this->validate($rules)) {
            $this->defData['errors'] = $this->validator->getErrors();
            return view('Modules\Backend\Views\subjects\create', $this->defData);
        }

        $db = \Config\Database::connect();
        $db->table('subjects')->insert([
            'name' => $this->request->getPost('name'),
            'slug' => url_title($this->request->getPost('name'), '-', true),
        ]);

        return redirect()->to(site_url('backend/subjects'))
                        ->with('success', 'Subject created successfully.');
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();
        $subject = $db->table('subjects')
            ->where('id', $id)
            ->get()
            ->getRow();

        if (! $subject) {
            return redirect()->to(site_url('backend/subjects'))->with('error', 'Subject not found.');
        }

        $this->defData['subject'] = $subject;
        return view('Modules\Backend\Views\subjects\edit', $this->defData);
    }

    public function update($id)
    {
        $rules = [
            'name' => "required|max_length[255]|is_unique[subjects.name,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            $this->defData['errors'] = $this->validator->getErrors();
            return view('Modules\Backend\Views\subjects\edit', $this->defData);
        }

        $db = \Config\Database::connect();
        $db->table('subjects')->where('id', $id)->update([
            'name' => $this->request->getPost('name'),
            'slug' => url_title($this->request->getPost('name'), '-', true),
        ]);

        return redirect()->to(site_url('backend/subjects'))
                        ->with('success', 'Subject updated successfully.');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $db->table('subjects')->where('id', $id)->delete();
        return redirect()->to(site_url('backend/subjects'))
                        ->with('success', 'Subject deleted successfully.');
    }
}