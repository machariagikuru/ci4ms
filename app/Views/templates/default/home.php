<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>
<?= $this->include('templates/default/partials/_hero') ?>
<?= $this->include('templates/default/partials/_features') ?>
<div class="py-4 mb-4" style="background-color: #074C87; border-radius: 0.5rem;">
    <div class="text-center">
        <a href="<?= site_url('subjects') ?>" 
           class="btn"
           style="background-color: #e9ff4e; color: #074C87; font-weight: 500; border: none; padding: 0.5rem 1.25rem; border-radius: 0.375rem;">
            Subjects
        </a>
        <a href="<?= site_url('notes') ?>" 
           class="btn"
           style="background-color: #e9ff4e; color: #074C87; font-weight: 500; border: none; padding: 0.5rem 1.25rem; border-radius: 0.375rem;">
            Notes
        </a>
        <a href="<?= site_url('exam-papers') ?>" 
           class="btn"
           style="background-color: #e9ff4e; color: #074C87; font-weight: 500; border: none; padding: 0.5rem 1.25rem; border-radius: 0.375rem;">
            Exam Papers
        </a>
        <a href="<?= site_url('categories') ?>" 
           class="btn me-2"
           style="background-color: #e9ff4e; color: #074C87; font-weight: 500; border: none; padding: 0.5rem 1.25rem; border-radius: 0.375rem;">
            Categories
        </a>
        <a href="<?= site_url('tags') ?>" 
           class="btn"
           style="background-color: #e9ff4e; color: #074C87; font-weight: 500; border: none; padding: 0.5rem 1.25rem; border-radius: 0.375rem;">
            Tags
        </a>
    </div>
</div>
<?= $this->include('templates/default/partials/_latest_posts') ?>
<?= $this->include('templates/default/partials/_categories') ?>
<?= $this->include('templates/default/partials/_tags') ?>
<?= $this->include('templates/default/partials/_testimonials', ['testimonials' => $testimonials ?? []]) ?>
<?= $this->endSection() ?>