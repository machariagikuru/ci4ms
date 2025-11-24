<?php

namespace Modules\Backend\Controllers;

use Modules\Backend\Controllers\BaseController;
use Modules\Backend\Models\NoteModel;

class NotesController extends BaseController
{
    protected $noteModel;

    public function __construct()
    {
        $this->noteModel = new NoteModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $notes = $db->table('notes')
            ->select('
                notes.id,
                notes.title,
                notes.description,
                notes.file_path,
                notes.created_at,
                subjects.name as subject_name,
                note_categories.name as category_name,
                note_tags.name as tag_name,
                CONCAT(users.firstname, " ", users.surname) as uploaded_by_name
            ')
            ->join('subjects', 'subjects.id = notes.subject_id')
            ->join('users', 'users.id = notes.uploaded_by')
            ->join('note_categories', 'note_categories.id = notes.category_id', 'left')
            ->join('note_tags', 'note_tags.id = notes.tag_id', 'left')
            ->orderBy('notes.created_at', 'DESC')
            ->get()
            ->getResult();

        $this->defData['notes'] = $notes;
        return view('Modules\Backend\Views\notes\list', $this->defData);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $subjects = $db->table('subjects')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
        $categories = $db->table('note_categories')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
        $tags = $db->table('note_tags')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();

        $this->defData['subjects'] = $subjects;
        $this->defData['categories'] = $categories;
        $this->defData['tags'] = $tags;

        return view('Modules\Backend\Views\notes\upload_form', $this->defData);
    }

    public function store()
    {
        $rules = [
            'title'        => 'required|max_length[255]',
            'subject_id'   => 'required|is_natural_no_zero',
            'pdf_file'     => 'uploaded[pdf_file]|max_size[pdf_file,10240]|mime_in[pdf_file,application/pdf]|ext_in[pdf_file,pdf]',
        ];

        if (! $this->validate($rules)) {
            $db = \Config\Database::connect();
            $subjects = $db->table('subjects')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
            $categories = $db->table('note_categories')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
            $tags = $db->table('note_tags')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();

            $this->defData['subjects'] = $subjects;
            $this->defData['categories'] = $categories;
            $this->defData['tags'] = $tags;
            $this->defData['errors'] = $this->validator->getErrors();

            return view('Modules\Backend\Views\notes\upload_form', $this->defData);
        }

        $file = $this->request->getFile('pdf_file');
        if (! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'Invalid file upload.');
        }

        $newName = $file->getRandomName();
        $uploadPath = 'uploads/notes';

        if (! $file->move(FCPATH . $uploadPath, $newName)) {
            return redirect()->back()->with('error', 'Could not save file.');
        }

        $noteData = [
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'content'      => $this->request->getPost('content'),
            'subject_id'   => $this->request->getPost('subject_id'),
            'category_id'  => $this->request->getPost('category_id') ?: null,
            'tag_id'       => $this->request->getPost('tag_id') ?: null,
            'file_path'    => $uploadPath . '/' . $newName,
            'uploaded_by'  => $this->logged_in_user->id,
        ];

        $noteId = $this->noteModel->insert($noteData);
        if (! $noteId) {
            unlink(FCPATH . $uploadPath . '/' . $newName);
            return redirect()->back()->with('error', 'Failed to save note.');
        }

        return redirect()->to(site_url('backend/notes'))
                        ->with('success', 'Note uploaded successfully.');
    }

    public function edit($id)
    {
        $note = $this->noteModel->find($id);
        if (! $note) {
            return redirect()->to(site_url('backend/notes'))->with('error', 'Note not found.');
        }

        $db = \Config\Database::connect();
        $subjects = $db->table('subjects')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
        $categories = $db->table('note_categories')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
        $tags = $db->table('note_tags')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();

        $this->defData['note'] = $note;
        $this->defData['subjects'] = $subjects;
        $this->defData['categories'] = $categories;
        $this->defData['tags'] = $tags;

        return view('Modules\Backend\Views\notes\edit_form', $this->defData);
    }

    public function update($id)
    {
        $note = $this->noteModel->find($id);
        if (! $note) {
            return redirect()->to(site_url('backend/notes'))->with('error', 'Note not found.');
        }

        $rules = [
            'title'        => 'required|max_length[255]',
            'subject_id'   => 'required|is_natural_no_zero',
        ];

        $hasFile = $this->request->getFile('pdf_file')->isValid();
        if ($hasFile) {
            $rules['pdf_file'] = 'uploaded[pdf_file]|max_size[pdf_file,10240]|mime_in[pdf_file,application/pdf]|ext_in[pdf_file,pdf]';
        }

        if (! $this->validate($rules)) {
            $db = \Config\Database::connect();
            $subjects = $db->table('subjects')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
            $categories = $db->table('note_categories')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
            $tags = $db->table('note_tags')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();

            $this->defData['note'] = $note;
            $this->defData['subjects'] = $subjects;
            $this->defData['categories'] = $categories;
            $this->defData['tags'] = $tags;
            $this->defData['errors'] = $this->validator->getErrors();

            return view('Modules\Backend\Views\notes\edit_form', $this->defData);
        }

        $updateData = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'content'     => $this->request->getPost('content'),
            'subject_id'  => $this->request->getPost('subject_id'),
            'category_id' => $this->request->getPost('category_id') ?: null,
            'tag_id'      => $this->request->getPost('tag_id') ?: null,
        ];

        if ($hasFile) {
            $file = $this->request->getFile('pdf_file');
            $newName = $file->getRandomName();
            $uploadPath = 'uploads/notes';

            if (file_exists(FCPATH . $note->file_path)) {
                unlink(FCPATH . $note->file_path);
            }

            if (! $file->move(FCPATH . $uploadPath, $newName)) {
                return redirect()->back()->with('error', 'Could not save new file.');
            }

            $updateData['file_path'] = $uploadPath . '/' . $newName;
        }

        if (! $this->noteModel->update($id, $updateData)) {
            return redirect()->back()->with('error', 'Failed to update note.');
        }

        return redirect()->to(site_url('backend/notes'))
                        ->with('success', 'Note updated successfully.');
    }

    public function delete($id)
    {
        $note = $this->noteModel->find($id);
        if (! $note) {
            return redirect()->to(site_url('backend/notes'))->with('error', 'Note not found.');
        }

        $filePath = FCPATH . $note->file_path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if (! $this->noteModel->delete($id)) {
            return redirect()->to(site_url('backend/notes'))->with('error', 'Failed to delete note.');
        }

        return redirect()->to(site_url('backend/notes'))
                        ->with('success', 'Note deleted successfully.');
    }
}