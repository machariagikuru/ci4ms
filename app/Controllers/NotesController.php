<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class NotesController extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get filters
        $subjectId   = $this->request->getGet('subject');
        $categoryId  = $this->request->getGet('category');
        $tagId       = $this->request->getGet('tag');

        // Base query
        $builder = $db->table('notes')
            ->select('
                notes.id,
                notes.title,
                notes.description,
                notes.content,
                notes.file_path,
                notes.created_at,
                subjects.name as subject_name,
                note_categories.name as category_name,
                note_tags.name as tag_name
            ')
            ->join('subjects', 'subjects.id = notes.subject_id')
            ->join('note_categories', 'note_categories.id = notes.category_id', 'left')
            ->join('note_tags', 'note_tags.id = notes.tag_id', 'left')
            ->orderBy('notes.created_at', 'DESC');

        // Apply filters
        if ($subjectId && is_numeric($subjectId)) {
            $builder->where('notes.subject_id', $subjectId);
        }
        if ($categoryId && is_numeric($categoryId)) {
            $builder->where('notes.category_id', $categoryId);
        }
        if ($tagId && is_numeric($tagId)) {
            $builder->where('notes.tag_id', $tagId);
        }

        $notes = $builder->get()->getResult();

        // Load filter options
        $subjects = $db->table('subjects')->orderBy('name', 'ASC')->get()->getResult();
        $categories = $db->table('note_categories')->orderBy('name', 'ASC')->get()->getResult();
        $tags = $db->table('note_tags')->orderBy('name', 'ASC')->get()->getResult();

        return view('notes/list', [
            'notes'      => $notes,
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
        $note = $db->table('notes')
            ->select('file_path, title')
            ->where('id', $id)
            ->get()
            ->getRow();

        if (! $note || ! file_exists(FCPATH . $note->file_path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download(FCPATH . $note->file_path, null);
    }
}