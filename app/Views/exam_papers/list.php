<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <h2 class="mb-4">Exam Papers</h2>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="subject" class="form-label">Subject</label>
                    <select class="form-select" id="subject" name="subject">
                        <option value="">All Subjects</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= $subject->id ?>" <?= $filters['subject'] == $subject->id ? 'selected' : '' ?>>
                                <?= esc($subject->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category->id ?>" <?= $filters['category'] == $category->id ? 'selected' : '' ?>>
                                <?= esc($category->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tag" class="form-label">Tag</label>
                    <select class="form-select" id="tag" name="tag">
                        <option value="">All Tags</option>
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?= $tag->id ?>" <?= $filters['tag'] == $tag->id ? 'selected' : '' ?>>
                                <?= esc($tag->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="<?= site_url('exam-papers') ?>" class="btn btn-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <?php if (empty($examPapers)): ?>
        <div class="alert alert-info">No exam papers found.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($examPapers as $paper): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($paper->title) ?></h5>
                            <p class="card-text">
                                <small class="text-muted">Subject: <?= esc($paper->subject_name) ?></small><br>
                                <?= esc($paper->description ?? '') ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= site_url("exam-papers/download/{$paper->id}") ?>" class="btn btn-primary">Download PDF</a>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>

<?= $this->endSection() ?>