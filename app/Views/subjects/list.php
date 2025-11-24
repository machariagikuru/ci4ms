<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8 col-lg-9">
            <h2 class="mb-4">Subjects</h2>

            <?php if (empty($subjects)): ?>
                <div class="alert alert-info">No subjects available.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($subjects as $subject): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= esc($subject->name) ?></h5>
                                </div>
                                <div class="card-footer">
                                    <a href="<?= site_url("subject/{$subject->id}") ?>" class="btn btn-primary">View Content</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>

        <!-- Sidebar (Right) -->
        <div class="col-md-4 col-lg-3 mb-4">
            <?= view('subjects/_sidebar', ['allSubjects' => $subjects]) ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>