<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>
<?= $this->include('templates/default/partials/_hero') ?>
<?= $this->include('templates/default/partials/_features') ?>
<?= $this->include('templates/default/partials/_latest_posts') ?>

<div class="text-center mb-4">
    <a href="<?= site_url('categories') ?>" class="btn btn-outline-primary me-2">Browse Categories</a>
    <a href="<?= site_url('tags') ?>" class="btn btn-outline-primary">Browse Tags</a>
</div>
<?= $this->include('templates/default/partials/_categories') ?>
<?= $this->include('templates/default/partials/_tags') ?>
<?= $this->include('templates/default/partials/_testimonials') ?>
<?= $this->endSection() ?>