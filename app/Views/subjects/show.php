<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8 col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?= esc($subject->name) ?></h2>
                <a href="<?= site_url('subjects') ?>" class="btn btn-secondary">← All Subjects</a>
            </div>

            <!-- Blog Posts -->
            <?php if (!empty($blogs)): ?>
                <div class="mb-5">
                    <h4>Blog Posts</h4>
                    <div class="list-group">
                        <?php foreach ($blogs as $blog): ?>
                            <a href="<?= site_url("blog/{$blog->seflink}") ?>" class="list-group-item list-group-item-action">
                                <?= esc($blog->title) ?>
                                <small class="text-muted d-block"><?= date('d M Y', strtotime($blog->created_at)) ?></small>
                            </a>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

            <!-- Exam Papers -->
            <?php if (!empty($examPapers)): ?>
                <div class="mb-5">
                    <h4>Exam Papers</h4>
                    <div class="row">
                        <?php foreach ($examPapers as $paper): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title"><?= esc($paper->title) ?></h6>
                                        <small class="text-muted"><?= date('d M Y', strtotime($paper->created_at)) ?></small>
                                    </div>
                                    <div class="card-footer">
                                        <a href="<?= site_url("exam-papers/download/{$paper->id}") ?>" class="btn btn-sm btn-primary">Download PDF</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

            <!-- Notes -->
            <?php if (!empty($notes)): ?>
                <div class="mb-5">
                    <h4>Notes</h4>
                    <div class="row">
                        <?php foreach ($notes as $note): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title"><?= esc($note->title) ?></h6>
                                        <small class="text-muted"><?= date('d M Y', strtotime($note->created_at)) ?></small>
                                    </div>
                                    <div class="card-footer">
                                        <a href="<?= site_url("notes/download/{$note->id}") ?>" class="btn btn-sm btn-primary">Download PDF</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

            <?php if (empty($blogs) && empty($examPapers) && empty($notes)): ?>
                <div class="alert alert-info">No content available for this subject yet.</div>
            <?php endif ?>
        </div>

        <!-- Sidebar (Right) -->
        <div class="col-md-4 col-lg-3 mb-4">
            <?php 
            $db = \Config\Database::connect();
            $allSubjects = $db->table('subjects')->orderBy('name', 'ASC')->get()->getResult();
            ?>
            <?= view('subjects/_sidebar', ['allSubjects' => $allSubjects, 'subject' => $subject]) ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>