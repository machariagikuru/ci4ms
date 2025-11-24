<?php helper(['url']); ?>
<?= $this->extend('Modules\Backend\Views\base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Exam Papers</h2>
        <a href="<?= route_to('examPaperUpload') ?>" class="btn btn-primary">Upload New</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif ?>

    <?php if (empty($examPapers)): ?>
        <div class="alert alert-info">No exam papers uploaded yet.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Uploaded By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($examPapers as $paper): ?>
                            <tr>
                                <td><?= esc($paper->title) ?></td>
                                <td><?= esc($paper->subject_name) ?></td>
                                <td><?= esc($paper->uploaded_by_name) ?></td>
                                <td><?= date('d M Y', strtotime($paper->created_at)) ?></td>
                                <td>
                                <a href="<?= base_url($paper->file_path) ?>" target="_blank" class="btn btn-sm btn-info" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= route_to('examPaperEdit', $paper->id) ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= route_to('examPaperDelete', $paper->id) ?>" 
                                class="btn btn-sm btn-danger" 
                                title="Delete"
                                onclick="return confirm('Are you sure you want to delete this exam paper?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif ?>
</div>

<?= $this->endSection() ?>