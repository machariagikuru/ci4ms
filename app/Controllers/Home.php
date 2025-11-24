<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function search()
    {
        $keyword = trim($this->request->getPost('q') ?? '');
        if (empty($keyword)) {
            return redirect()->back()->with('error', 'Enter a search term.');
        }

        // Ensure your commonModel is available (from BaseController)
        if (!isset($this->commonModel)) {
            $this->commonModel = new \ci4commonmodel\Models\CommonModel();
        }

        // Escape the keyword for safety
        $keywordEscaped = $this->commonModel->db->escapeString($keyword);

        // Perform FULLTEXT search on blog table
        $builder = $this->commonModel->db->table('blog');
        $builder->select("id, title, seflink, COALESCE(LEFT(content, 300), '') as excerpt, created_at, MATCH(title, content) AGAINST('{$keywordEscaped}') AS relevance");
        $builder->where('isActive', 1);
        $builder->where("MATCH(title, content) AGAINST('{$keywordEscaped}' IN NATURAL LANGUAGE MODE)");
        $builder->orderBy('relevance', 'DESC');
        $blogs = $builder->get()->getResult();

        // Optional: search pages too (if you have a 'pages' table)
        $pages = [];
        if ($this->commonModel->db->tableExists('pages')) {
            $pageBuilder = $this->commonModel->db->table('pages');
            $pageBuilder->select("id, title, seflink, content, MATCH(title, content) AGAINST('{$keywordEscaped}') AS relevance");
            $pageBuilder->where('isActive', 1);
            $pageBuilder->where("MATCH(title, content) AGAINST('{$keywordEscaped}' IN NATURAL LANGUAGE MODE)");
            $pageBuilder->orderBy('relevance', 'DESC');
            $pages = $pageBuilder->get()->getResult();
        }

        return view('search/results', [
            'keyword' => $keyword,
            'blogs' => $blogs,
            'pages' => $pages,
        ]);
    }

    public function index()
{
    $data['testimonials'] = [
        [
            'name'    => 'Jane Muthoni',
            'role'    => 'Teacher, CBC Coordinator',
            'content' => 'StrandNotes has transformed how I share revision materials with my students. It\'s intuitive and saves hours every week!',
            'avatar'  => base_url('uploads/avatars/jane.jpg') // optional
        ],
        [
            'name'    => 'Kevin Omondi',
            'role'    => 'Grade 10 Student',
            'content' => 'I found past papers and notes for all my subjects in one place. My grades have improved dramatically!',
            // no avatar → will show initials
        ],
        // Add more as needed
    ];

    return view('home', $data);
}
}