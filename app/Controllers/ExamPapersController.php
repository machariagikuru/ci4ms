<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ExamPapersController extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get filter inputs
        $subjectId   = $this->request->getGet('subject');
        $categoryId  = $this->request->getGet('category');
        $tagId       = $this->request->getGet('tag');

        // Build base query
        $builder = $db->table('exam_papers')
            ->select('
                exam_papers.id,
                exam_papers.title,
                exam_papers.description,
                exam_papers.file_path,
                exam_papers.created_at,
                subjects.name as subject_name
            ')
            ->join('subjects', 'subjects.id = exam_papers.subject_id')
            ->orderBy('exam_papers.created_at', 'DESC');

        // Apply subject filter
        if ($subjectId && is_numeric($subjectId)) {
            $builder->where('exam_papers.subject_id', $subjectId);
        }

        // Apply category filter
        if ($categoryId && is_numeric($categoryId)) {
            $builder->join('exam_paper_categories', 'exam_paper_categories.exam_paper_id = exam_papers.id')
                    ->where('exam_paper_categories.category_id', $categoryId);
        }

        // Apply tag filter
        if ($tagId && is_numeric($tagId)) {
            $builder->join('exam_paper_tags', 'exam_paper_tags.exam_paper_id = exam_papers.id')
                    ->where('exam_paper_tags.tag_id', $tagId);
        }

        $examPapers = $builder->get()->getResult();

        // Load filter options for dropdowns
        $subjects = $db->table('subjects')->orderBy('name', 'ASC')->get()->getResult();
        $categories = $db->table('exam_categories')->orderBy('name', 'ASC')->get()->getResult();
        $tags = $db->table('exam_tags')->orderBy('name', 'ASC')->get()->getResult();

        return view('exam_papers/list', [
            'examPapers' => $examPapers,
            'subjects'   => $subjects,
            'categories' => $categories,
            'tags'       => $tags,
            'filters'    => [
                'subject'  => $subjectId,
                'category' => $categoryId,
                'tag'      => $tagId,
            ]
        ]);
    }

    public function download($id)
    {
        $db = \Config\Database::connect();
        $paper = $db->table('exam_papers')
            ->select('file_path, title')
            ->where('id', $id)
            ->get()
            ->getRow();

        if (! $paper || ! file_exists(FCPATH . $paper->file_path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download(FCPATH . $paper->file_path, null);
    }
}