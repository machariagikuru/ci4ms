<?php helper(['form', 'url']); ?>
<?= $this->extend('Modules\Backend\Views\base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h2>Edit Exam Paper</h2>

    <?php if (isset($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= esc($error) ?></p>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?= form_open_multipart("backend/exam-papers/edit/{$paper->id}", ['id' => 'editExamPaperForm']) ?>

        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="<?= old('title', esc($paper->title)) ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', esc($paper->description ?? '')) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
            <select class="form-select" id="subject_id" name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject->id ?>" <?= set_select('subject_id', $subject->id, $subject->id == $paper->subject_id) ?>>
                        <?= esc($subject->name) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Current File</label>
            <div>
                <a href="<?= base_url($paper->file_path) ?>" target="_blank" class="btn btn-info btn-sm">
                    <i class="fas fa-file-pdf"></i> Preview PDF
                </a>
                <small class="d-block mt-1"><?= basename($paper->file_path) ?></small>
            </div>
        </div>

        <div class="mb-3">
            <label for="pdf_file" class="form-label">Replace PDF (optional)</label>
            <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
            <div class="form-text">Leave empty to keep current file.</div>
        </div>

        <button type="submit" class="btn btn-primary">Update Exam Paper</button>
        <a href="<?= route_to('examPapers') ?>" class="btn btn-secondary">Cancel</a>

    <?= form_close() ?>
</div>

<?= $this->endSection() ?>