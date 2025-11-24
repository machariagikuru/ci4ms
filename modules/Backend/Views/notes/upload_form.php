<?php helper(['form', 'url']); ?>
<?= $this->extend('Modules\Backend\Views\base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h2>Upload Note</h2>

    <?php if (isset($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= esc($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= form_open_multipart('backend/notes/upload', ['id' => 'uploadNoteForm']) ?>

        <div class="form-group">
            <label for="title">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="2"><?= old('description') ?></textarea>
        </div>

        <div class="form-group">
            <label for="content">Content (Optional)</label>
            <textarea class="form-control" id="content" name="content" rows="4"><?= old('content') ?></textarea>
            <span class="text-muted">Additional notes or summary text.</span>
        </div>

        <div class="form-group">
            <label for="subject_id">Subject <span class="text-danger">*</span></label>
            <select class="form-control" id="subject_id" name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject->id ?>" <?= set_select('subject_id', $subject->id) ?>>
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
                    <option value="<?= $category->id ?>" <?= set_select('category_id', $category->id) ?>>
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
                    <option value="<?= $tag->id ?>" <?= set_select('tag_id', $tag->id) ?>>
                        <?= esc($tag->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="pdf_file">Note File (PDF) <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload Note</button>
        <a href="<?= route_to('notes') ?>" class="btn btn-secondary">Cancel</a>

    <?= form_close() ?>
</div>

<?= $this->endSection() ?>