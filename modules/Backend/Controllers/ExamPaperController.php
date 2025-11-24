<?php

namespace Modules\Backend\Controllers;

// ✅ Use Backend's BaseController, NOT App's
use Modules\Backend\Controllers\BaseController;
use Modules\Backend\Models\ExamPaperModel;

class ExamPaperController extends BaseController
{
    protected $examPaperModel;

    public function __construct()
    {
        $this->examPaperModel = new ExamPaperModel();
    }

    /**
     * List all exam papers
     */
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
                CONCAT(users.firstname, " ", users.surname) as uploaded_by_name
            ')
            ->join('subjects', 'subjects.id = exam_papers.subject_id')
            ->join('users', 'users.id = exam_papers.uploaded_by')
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
        $categories = $db->table('exam_categories')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
        $tags = $db->table('exam_tags')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();

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
            $categories = $db->table('exam_categories')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
            $tags = $db->table('exam_tags')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();

            $this->defData['subjects'] = $subjects;
            $this->defData['categories'] = $categories;
            $this->defData['tags'] = $tags;
            $this->defData['errors'] = $this->validator->getErrors();

            return view('Modules\Backend\Views\exam_papers\upload_form', $this->defData);
        }

        // Handle file upload
        $file = $this->request->getFile('pdf_file');
        if (! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'Invalid file upload.');
        }

        $newName = $file->getRandomName();
        $uploadPath = 'uploads/exam_papers';

        if (! $file->move(FCPATH . $uploadPath, $newName)) {
            return redirect()->back()->with('error', 'Could not save file.');
        }

        // Save exam paper
        $paperData = [
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'subject_id'   => $this->request->getPost('subject_id'),
            'file_path'    => $uploadPath . '/' . $newName,
            'uploaded_by'  => $this->logged_in_user->id,
        ];

        $paperId = $this->examPaperModel->insert($paperData);
        if (! $paperId) {
            unlink(FCPATH . $uploadPath . '/' . $newName);
            return redirect()->back()->with('error', 'Failed to save exam paper.');
        }

        // Save categories
        $categories = $this->request->getPost('categories');
        if (! empty($categories)) {
            $db = \Config\Database::connect();
            $categoryData = [];
            foreach ($categories as $catId) {
                $categoryData[] = [
                    'exam_paper_id' => $paperId,
                    'category_id'   => $catId
                ];
            }
            $db->table('exam_paper_categories')->insertBatch($categoryData);
        }

        // Save tags
        $tags = $this->request->getPost('tags');
        if (! empty($tags)) {
            $db = \Config\Database::connect();
            $tagData = [];
            foreach ($tags as $tagId) {
                $tagData[] = [
                    'exam_paper_id' => $paperId,
                    'tag_id'        => $tagId
                ];
            }
            $db->table('exam_paper_tags')->insertBatch($tagData);
        }

        return redirect()->to(site_url('backend/exam-papers'))
                        ->with('success', 'Exam paper uploaded successfully.');
    }
        /**
     * Update an exam paper
     */
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
            $categories = $db->table('exam_categories')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();
            $tags = $db->table('exam_tags')->select('id, name')->orderBy('name', 'ASC')->get()->getResult();

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

        // Update categories: delete old, insert new
        $db = \Config\Database::connect();
        $db->table('exam_paper_categories')->where('exam_paper_id', $id)->delete();

        $categories = $this->request->getPost('categories');
        if (! empty($categories)) {
            $categoryData = [];
            foreach ($categories as $catId) {
                $categoryData[] = [
                    'exam_paper_id' => $id,
                    'category_id'   => $catId
                ];
            }
            $db->table('exam_paper_categories')->insertBatch($categoryData);
        }

        // Update tags: delete old, insert new
        $db->table('exam_paper_tags')->where('exam_paper_id', $id)->delete();

        $tags = $this->request->getPost('tags');
        if (! empty($tags)) {
            $tagData = [];
            foreach ($tags as $tagId) {
                $tagData[] = [
                    'exam_paper_id' => $id,
                    'tag_id'        => $tagId
                ];
            }
            $db->table('exam_paper_tags')->insertBatch($tagData);
        }

        return redirect()->to(site_url('backend/exam-papers'))
                        ->with('success', 'Exam paper updated successfully.');
    }

    /**
     * Delete an exam paper
     */
    public function delete($id)
    {
        $paper = $this->examPaperModel->find($id);
        if (! $paper) {
            return redirect()->to(site_url('backend/exam-papers'))->with('error', 'Exam paper not found.');
        }

        // Delete file from disk
        $filePath = FCPATH . $paper->file_path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        if (! $this->examPaperModel->delete($id)) {
            return redirect()->to(site_url('backend/exam-papers'))->with('error', 'Failed to delete exam paper.');
        }

        return redirect()->to(site_url('backend/exam-papers'))
                        ->with('success', 'Exam paper deleted successfully.');
    }
}