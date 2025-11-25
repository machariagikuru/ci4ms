<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <h2 class="mb-4 fw-bold" style="color: #074C87;">Exam Papers</h2>

    <!-- Filter Form -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="subject" class="form-label fw-medium" style="color: #074C87;">Subject</label>
                    <select class="form-select" id="subject" name="subject" style="border-color: #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem;">
                        <option value="">All Subjects</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= $subject->id ?>" <?= $filters['subject'] == $subject->id ? 'selected' : '' ?>>
                                <?= esc($subject->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="category" class="form-label fw-medium" style="color: #074C87;">Category</label>
                    <select class="form-select" id="category" name="category" style="border-color: #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem;">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category->id ?>" <?= $filters['category'] == $category->id ? 'selected' : '' ?>>
                                <?= esc($category->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tag" class="form-label fw-medium" style="color: #074C87;">Tag</label>
                    <select class="form-select" id="tag" name="tag" style="border-color: #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem;">
                        <option value="">All Tags</option>
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?= $tag->id ?>" <?= $filters['tag'] == $tag->id ? 'selected' : '' ?>>
                                <?= esc($tag->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" 
                            class="btn"
                            style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.5rem 1.5rem;">
                        Filter
                    </button>
                    <a href="<?= site_url('exam-papers') ?>" 
                       class="btn"
                       style="background-color: #e6f0fa; color: #074C87; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.5rem 1.5rem; text-decoration: none;">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <?php if (empty($examPapers)): ?>
        <div class="alert alert-light border" role="alert" style="background-color: #f8fafc; color: #074C87; border-color: #e6f0fa;">
            No exam papers found.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($examPapers as $paper): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold" style="color: #074C87; font-size: 1.1rem; margin-bottom: 0.75rem;">
                                <?= esc($paper->title) ?>
                            </h5>
                            <p class="card-text" style="color: #555; line-height: 1.5; font-size: 0.95rem;">
                                <small class="text-muted" style="color: #6c757d !important;">
                                    <?= esc($paper->subject_name) ?>
                                    <?php if (!empty($paper->category_name)): ?> • <?= esc($paper->category_name) ?><?php endif; ?>
                                    <?php if (!empty($paper->tag_name)): ?> • <?= esc($paper->tag_name) ?><?php endif; ?>
                                </small><br>
                                <?= esc($paper->description ?? '') ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 p-3">
                            <a href="<?= site_url("exam-papers/download/{$paper->id}") ?>" 
                               class="btn w-100"
                               style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>