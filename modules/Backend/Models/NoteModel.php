<?php

namespace Modules\Backend\Models;

use CodeIgniter\Model;

class NoteModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'notes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'title',
        'description',
        'content',
        'subject_id',
        'file_path',
        'uploaded_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title'        => 'required|max_length[255]',
        'subject_id'   => 'required|is_natural_no_zero',
        'file_path'    => 'required|max_length[255]',
        'uploaded_by'  => 'required|is_natural_no_zero',
    ];

    protected $skipValidation = false;
}