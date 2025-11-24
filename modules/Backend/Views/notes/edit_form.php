<?php helper(['form', 'url']); ?>
<?= $this->extend('Modules\Backend\Views\base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h2>Edit Note</h2>

    <?php if (isset($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= esc($error) ?></p>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?= form_open_multipart("backend/notes/edit/{$note->id}", ['id' => 'editNoteForm']) ?>

        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="<?= old('title', esc($note->title)) ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="2"><?= old('description', esc($note->description ?? '')) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content (Optional)</label>
            <textarea class="form-control" id="content" name="content" rows="4"><?= old('content', esc($note->content ?? '')) ?></textarea>
            <div class="form-text">Additional notes or summary text.</div>
        </div>

        <div class="mb-3">
            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
            <select class="form-select" id="subject_id" name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject->id ?>" <?= set_select('subject_id', $subject->id, $subject->id == $note->subject_id) ?>>
                        <?= esc($subject->name) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="categories" class="form-label">Categories</label>
            <select class="form-select" id="categories" name="categories[]" multiple>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->id ?>" 
                        <?= in_array($category->id, $selectedCategoryIds ?? []) ? 'selected' : '' ?>>
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
                    <option value="<?= $tag->id ?>" 
                        <?= in_array($tag->id, $selectedTagIds ?? []) ? 'selected' : '' ?>>
                        <?= esc($tag->name) ?>
                    </option>
                <?php endforeach ?>
            </select>
            <div class="form-text">Hold Ctrl (or Cmd) to select multiple.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Current File</label>
            <div>
                <a href="<?= base_url($note->file_path) ?>" target="_blank" class="btn btn-info btn-sm">
                    <i class="fas fa-file-pdf"></i> Preview PDF
                </a>
                <small class="d-block mt-1"><?= basename($note->file_path) ?></small>
            </div>
        </div>

        <div class="mb-3">
            <label for="pdf_file" class="form-label">Replace PDF (optional)</label>
            <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
            <div class="form-text">Leave empty to keep current file.</div>
        </div>

        <button type="submit" class="btn btn-primary">Update Note</button>
        <a href="<?= route_to('notes') ?>" class="btn btn-secondary">Cancel</a>

    <?= form_close() ?>
</div>

<?= $this->endSection() ?>