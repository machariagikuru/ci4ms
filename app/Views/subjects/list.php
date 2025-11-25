<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8 col-lg-9" style="background-color: #e6f0fa; padding: 20px; border-radius: 0.5rem;  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
            <h2 class="mb-4 fw-bold" style="color: #074C87;">Subjects</h2>

            <?php if (empty($subjects)): ?>
                <div class="alert alert-light border" role="alert" style="background-color: #f8fafc; color: #074C87; border-color: #e6f0fa;">
                    No subjects available.
                </div>
            <?php else: ?>
                <div class="row" style="background-color: #e6f0fa">
                    <?php foreach ($subjects as $subject): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius: 0.5rem;">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold" style="color: #074C87; font-size: 1.1rem;">
                                        <?= esc($subject->name) ?>
                                    </h5>
                                </div>
                                <div class="card-footer bg-white border-0 p-3">
                                    <a href="<?= site_url("subject/{$subject->id}") ?>" 
                                       class="btn w-100"
                                       style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                                        View Content
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar (Right) -->
        <div class="col-md-4 col-lg-3">
            <div class="sticky-top" style="top: 90px;">
                <?= view('subjects/_sidebar', ['allSubjects' => $subjects]) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>