<?php helper(['form', 'url']); ?>
<?= $this->extend('Modules\Backend\Views\base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h2>Upload Exam Paper</h2>

    <?php if (isset($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= esc($error) ?></p>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?= form_open_multipart('backend/exam-papers/upload', ['id' => 'uploadExamPaperForm']) ?>

        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
            <select class="form-select" id="subject_id" name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject->id ?>" <?= set_select('subject_id', $subject->id) ?>>
                        <?= esc($subject->name) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
         <div class="mb-3">
    <label for="categories" class="form-label">Categories</label>
    <select class="form-select" id="categories" name="categories[]" multiple>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category->id ?>" <?= set_select('categories[]', $category->id) ?>>
                <?= esc($category->name) ?>
            </option>
        <?php endforeach ?>
    </select>
    <div class="form-text">Hold Ctrl (or Cmd) to select multiple.</div>
</div>
    <div class="mb-3">
        <label for="tags" class="form-label">Tags</label>
        <select class="form-select" id="tags" name="tags[]" multiple>
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag->id ?>" <?= set_select('tags[]', $tag->id) ?>>
                    <?= esc($tag->name) ?>
                </option>
            <?php endforeach ?>
        </select>
        <div class="form-text">Hold Ctrl (or Cmd) to select multiple.</div>
    </div>           
        <div class="mb-3">
            <label for="pdf_file" class="form-label">Exam Paper (PDF) <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload Exam Paper</button>
        <a href="<?= route_to('backend') ?>" class="btn btn-secondary">Cancel</a>

    <?= form_close() ?>
</div>

<?= $this->endSection() ?>