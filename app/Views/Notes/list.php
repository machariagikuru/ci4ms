<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <h2 class="mb-4">Notes</h2>

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
                    <a href="<?= site_url('notes') ?>" class="btn btn-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <?php if (empty($notes)): ?>
        <div class="alert alert-info">No notes available.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($notes as $note): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($note->title) ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <?= esc($note->subject_name) ?>
                                    <?php if ($note->category_name): ?> • <?= esc($note->category_name) ?><?php endif ?>
                                    <?php if ($note->tag_name): ?> • <?= esc($note->tag_name) ?><?php endif ?>
                                </small><br>
                                <?= esc($note->description ?? '') ?>
                                <?php if ($note->content): ?>
                                    <div class="mt-2"><?= esc(substr($note->content, 0, 100)) ?>...</div>
                                <?php endif ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= site_url("notes/download/{$note->id}") ?>" class="btn btn-primary">Download PDF</a>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>

<?= $this->endSection() ?>