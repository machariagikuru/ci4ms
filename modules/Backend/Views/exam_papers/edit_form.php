<?php helper(['form', 'url']); ?>
<?= $this->extend('Modules\Backend\Views\base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h2>Edit Exam Paper</h2>

    <?php if (isset($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= esc($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= form_open_multipart("backend/exam-papers/edit/{$paper->id}", ['id' => 'editExamPaperForm']) ?>

        <div class="form-group">
            <label for="title">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="<?= old('title', esc($paper->title)) ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', esc($paper->description ?? '')) ?></textarea>
        </div>

        <div class="form-group">
            <label for="subject_id">Subject <span class="text-danger">*</span></label>
            <select class="form-control" id="subject_id" name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject->id ?>" <?= set_select('subject_id', $subject->id, $subject->id == $paper->subject_id) ?>>
                        <?= esc($subject->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select class="form-control" id="category_id" name="category_id">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->id ?>" <?= set_select('category_id', $category->id, $category->id == ($paper->category_id ?? null)) ?>>
                        <?= esc($category->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="tag_id">Tag</label>
            <select class="form-control" id="tag_id" name="tag_id">
                <option value="">-- Select Tag --</option>
                <?php foreach ($tags as $tag): ?>
                    <option value="<?= $tag->id ?>" <?= set_select('tag_id', $tag->id, $tag->id == ($paper->tag_id ?? null)) ?>>
                        <?= esc($tag->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Current File</label>
            <div>
                <a href="<?= base_url($paper->file_path) ?>" target="_blank" class="btn btn-info btn-sm">
                    <i class="fas fa-file-pdf"></i> Preview PDF
                </a>
                <small class="d-block mt-1"><?= basename($paper->file_path) ?></small>
            </div>
        </div>

        <div class="form-group">
            <label for="pdf_file">Replace PDF (optional)</label>
            <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
            <div class="form-text">Leave empty to keep current file.</div>
        </div>

        <button type="submit" class="btn btn-primary">Update Exam Paper</button>
        <a href="<?= route_to('examPapers') ?>" class="btn btn-secondary">Cancel</a>

    <?= form_close() ?>
</div>

<?= $this->endSection() ?>