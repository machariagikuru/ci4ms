<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8 col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: #074C87;"><?= esc($subject->name) ?></h2>
                <a href="<?= site_url('subjects') ?>" 
                   class="btn"
                   style="background-color: #e6f0fa; color: #074C87; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.4rem 1rem; text-decoration: none;">
                    ← All Subjects
                </a>
            </div>

            <?php if (empty($blogs) && empty($examPapers) && empty($notes)): ?>
                <div class="alert alert-light border" role="alert" style="background-color: #f8fafc; color: #074C87; border-color: #e6f0fa;">
                    No content available for this subject yet.
                </div>
            <?php else: ?>

                <!-- Blog Posts -->
                <?php if (!empty($blogs)): ?>
                    <div class="mb-5" style="background-color: #e6f0fa; padding: 15px; border-radius: 0.5rem;   box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                        <h4 class="fw-bold mb-3" style="color: #074C87;">Blog Posts</h4>
                        <div class="list-group">
                            <?php foreach ($blogs as $blog): ?>
                                <a href="<?= site_url("blog/{$blog->seflink}") ?>" 
                                   class="list-group-item list-group-item-action py-3 px-3"
                                   style="border: 1px solid #e6f0fa; border-radius: 0.375rem; margin-bottom: 0.5rem; color: #074C87; text-decoration: none;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong><?= esc($blog->title) ?></strong>
                                        <small class="text-muted" style="color: #555 !important;"><?= date('d M Y', strtotime($blog->created_at)) ?></small>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Exam Papers -->
                <?php if (!empty($examPapers)): ?>
                    <div class="mb-5">
                        <h4 class="fw-bold mb-3" style="color: #074C87;">Exam Papers</h4>
                        <div class="row">
                            <?php foreach ($examPapers as $paper): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 0.5rem;">
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold" style="color: #074C87; font-size: 1.05rem;"><?= esc($paper->title) ?></h6>
                                            <small class="text-muted" style="color: #555;"><?= date('d M Y', strtotime($paper->created_at)) ?></small>
                                        </div>
                                        <div class="card-footer bg-white border-0 p-3">
                                            <a href="<?= site_url("exam-papers/download/{$paper->id}") ?>" 
                                               class="btn w-100"
                                               style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.9rem;">
                                                Download PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Notes -->
                <?php if (!empty($notes)): ?>
                    <div class="mb-5">
                        <h4 class="fw-bold mb-3" style="color: #074C87;">Notes</h4>
                        <div class="row">
                            <?php foreach ($notes as $note): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 0.5rem;">
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold" style="color: #074C87; font-size: 1.05rem;"><?= esc($note->title) ?></h6>
                                            <small class="text-muted" style="color: #555;"><?= date('d M Y', strtotime($note->created_at)) ?></small>
                                        </div>
                                        <div class="card-footer bg-white border-0 p-3">
                                            <a href="<?= site_url("notes/download/{$note->id}") ?>" 
                                               class="btn w-100"
                                               style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.9rem;">
                                                Download PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4 col-lg-3">
            <div class="sticky-top" style="top: 90px;">
                <?php 
                $db = \Config\Database::connect();
                $allSubjects = $db->table('subjects')->orderBy('name', 'ASC')->get()->getResult();
                ?>
                <?= view('subjects/_sidebar', ['allSubjects' => $allSubjects, 'subject' => $subject]) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>