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
                subjects.name as subject_name,
                categories.title as category_name,
                tags.tag as tag_name
            ')
            ->join('subjects', 'subjects.id = exam_papers.subject_id')
            ->join('categories', 'categories.id = exam_papers.category_id', 'left')
            ->join('tags', 'tags.id = exam_papers.tag_id', 'left')
            ->orderBy('exam_papers.created_at', 'DESC');

        // Apply filters
        if ($subjectId && is_numeric($subjectId)) {
            $builder->where('exam_papers.subject_id', $subjectId);
        }
        if ($categoryId && is_numeric($categoryId)) {
            $builder->where('exam_papers.category_id', $categoryId);
        }
        if ($tagId && is_numeric($tagId)) {
            $builder->where('exam_papers.tag_id', $tagId);
        }

        $examPapers = $builder->get()->getResult();

        // Load filter options
        $subjects = $db->table('subjects')->orderBy('name', 'ASC')->get()->getResult();
        $categories = $db->table('categories')->select('id, title as name')->orderBy('title', 'ASC')->get()->getResult();
        $tags = $db->table('tags')->select('id, tag as name')->orderBy('tag', 'ASC')->get()->getResult();

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