<?php

namespace Modules\Backend\Controllers;

use Modules\Backend\Controllers\BaseController;
use Modules\Backend\Models\ExamPaperModel;

class ExamPaperController extends BaseController
{
    protected $examPaperModel;

    public function __construct()
    {
        $this->examPaperModel = new ExamPaperModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $examPapers = $db->table('exam_papers')
            ->select('
                exam_papers.id,
                exam_papers.title,
                exam_papers.description,
                exam_papers.file_path,
                exam_papers.created_at,
                subjects.name as subject_name,
                categories.title as category_name,
                tags.tag as tag_name,
                CONCAT(users.firstname, " ", users.surname) as uploaded_by_name
            ')
            ->join('subjects', 'subjects.id = exam_papers.subject_id')
            ->join('users', 'users.id = exam_papers.uploaded_by')
            ->join('categories', 'categories.id = exam_papers.category_id', 'left')
            ->join('tags', 'tags.id = exam_papers.tag_id', 'left')
            ->orderBy('exam_papers.created_at', 'DESC')
            ->get()
            ->getResult();

        $this->defData['examPapers'] = $examPapers;
        return view('Modules\Backend\Views\exam_papers\list', $this->defData);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $subjects = $db->table('subjects')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
        $categories = $db->table('categories')->select('id, title as name')->orderBy('title', 'ASC')->get()->getResult();
        $tags = $db->table('tags')->select('id, tag as name')->orderBy('tag', 'ASC')->get()->getResult();

        $this->defData['subjects'] = $subjects;
        $this->defData['categories'] = $categories;
        $this->defData['tags'] = $tags;

        return view('Modules\Backend\Views\exam_papers\upload_form', $this->defData);
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
            $categories = $db->table('categories')->select('id, title as name')->orderBy('title', 'ASC')->get()->getResult();
            $tags = $db->table('tags')->select('id, tag as name')->orderBy('tag', 'ASC')->get()->getResult();

            $this->defData['subjects'] = $subjects;
            $this->defData['categories'] = $categories;
            $this->defData['tags'] = $tags;
            $this->defData['errors'] = $this->validator->getErrors();

            return view('Modules\Backend\Views\exam_papers\upload_form', $this->defData);
        }

        $file = $this->request->getFile('pdf_file');
        if (! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'Invalid file upload.');
        }

        $newName = $file->getRandomName();
        $uploadPath = 'uploads/exam_papers';

        if (! $file->move(FCPATH . $uploadPath, $newName)) {
            return redirect()->back()->with('error', 'Could not save file.');
        }

        $paperData = [
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'subject_id'   => $this->request->getPost('subject_id'),
            'category_id'  => $this->request->getPost('category_id') ?: null,
            'tag_id'       => $this->request->getPost('tag_id') ?: null,
            'file_path'    => $uploadPath . '/' . $newName,
            'uploaded_by'  => $this->logged_in_user->id,
        ];

        $paperId = $this->examPaperModel->insert($paperData);
        if (! $paperId) {
            unlink(FCPATH . $uploadPath . '/' . $newName);
            return redirect()->back()->with('error', 'Failed to save exam paper.');
        }

        return redirect()->to(site_url('backend/exam-papers'))
                        ->with('success', 'Exam paper uploaded successfully.');
    }

    public function edit($id)
    {
        $paper = $this->examPaperModel->find($id);
        if (! $paper) {
            return redirect()->to(site_url('backend/exam-papers'))->with('error', 'Exam paper not found.');
        }

        $db = \Config\Database::connect();
        $subjects = $db->table('subjects')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
        $categories = $db->table('categories')->select('id, title as name')->orderBy('title', 'ASC')->get()->getResult();
        $tags = $db->table('tags')->select('id, tag as name')->orderBy('tag', 'ASC')->get()->getResult();

        $this->defData['paper'] = $paper;
        $this->defData['subjects'] = $subjects;
        $this->defData['categories'] = $categories;
        $this->defData['tags'] = $tags;

        return view('Modules\Backend\Views\exam_papers\edit_form', $this->defData);
    }

    public function update($id)
    {
        $paper = $this->examPaperModel->find($id);
        if (! $paper) {
            return redirect()->to(site_url('backend/exam-papers'))->with('error', 'Exam paper not found.');
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
            $categories = $db->table('categories')->select('id, title as name')->orderBy('title', 'ASC')->get()->getResult();
            $tags = $db->table('tags')->select('id, tag as name')->orderBy('tag', 'ASC')->get()->getResult();

            $this->defData['paper'] = $paper;
            $this->defData['subjects'] = $subjects;
            $this->defData['categories'] = $categories;
            $this->defData['tags'] = $tags;
            $this->defData['errors'] = $this->validator->getErrors();

            return view('Modules\Backend\Views\exam_papers\edit_form', $this->defData);
        }

        $updateData = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'subject_id'  => $this->request->getPost('subject_id'),
            'category_id' => $this->request->getPost('category_id') ?: null,
            'tag_id'      => $this->request->getPost('tag_id') ?: null,
        ];

        if ($hasFile) {
            $file = $this->request->getFile('pdf_file');
            $newName = $file->getRandomName();
            $uploadPath = 'uploads/exam_papers';

            if (file_exists(FCPATH . $paper->file_path)) {
                unlink(FCPATH . $paper->file_path);
            }

            if (! $file->move(FCPATH . $uploadPath, $newName)) {
                return redirect()->back()->with('error', 'Could not save new file.');
            }

            $updateData['file_path'] = $uploadPath . '/' . $newName;
        }

        if (! $this->examPaperModel->update($id, $updateData)) {
            return redirect()->back()->with('error', 'Failed to update exam paper.');
        }

        return redirect()->to(site_url('backend/exam-papers'))
                        ->with('success', 'Exam paper updated successfully.');
    }

    public function delete($id)
    {
        $paper = $this->examPaperModel->find($id);
        if (! $paper) {
            return redirect()->to(site_url('backend/exam-papers'))->with('error', 'Exam paper not found.');
        }

        $filePath = FCPATH . $paper->file_path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if (! $this->examPaperModel->delete($id)) {
            return redirect()->to(site_url('backend/exam-papers'))->with('error', 'Failed to delete exam paper.');
        }

        return redirect()->to(site_url('backend/exam-papers'))
                        ->with('success', 'Exam paper deleted successfully.');
    }
}