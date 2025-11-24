<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SubjectsController extends Controller
{
    /**
     * List all subjects
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $subjects = $db->table('subjects')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResult();

        return view('subjects/list', [
            'subjects' => $subjects
        ]);
    }

    /**
     * Show a single subject and its content
     */
    public function show($subjectId)
    {
        $db = \Config\Database::connect();

        // Get subject
        $subject = $db->table('subjects')
            ->where('id', $subjectId)
            ->get()
            ->getRow();

        if (! $subject) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Get related blog posts
        $blogs = $db->table('blog')
            ->select('id, title, seflink, created_at')
            ->where('subject_id', $subjectId)
            ->where('isActive', 1)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResult();

        // Get related exam papers
        $examPapers = $db->table('exam_papers')
            ->select('id, title, file_path, created_at')
            ->where('subject_id', $subjectId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResult();

        // Get related notes
        $notes = $db->table('notes')
            ->select('id, title, file_path, created_at')
            ->where('subject_id', $subjectId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResult();

        return view('subjects/show', [
            'subject'     => $subject,
            'blogs'       => $blogs,
            'examPapers'  => $examPapers,
            'notes'       => $notes,
        ]);
    }
}