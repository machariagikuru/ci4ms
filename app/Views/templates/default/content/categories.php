<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Categories</h2>
        <input type="text" id="categorySearch" class="form-control w-25" placeholder="Search categories...">
    </div>

    <div class="row" id="categoryList">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($cat->title) ?></h5>
                            <?php if (!empty($cat->description)): ?>
                                <p class="card-text"><?= esc($cat->description) ?></p>
                            <?php endif; ?>
                            <a href="<?= site_url('category/' . $cat->seflink) ?>" class="btn btn-primary">View Posts</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">No categories found.</div>
        <?php endif; ?>
    </div>

    <?= $pager ?? '' ?>

    <script>
    document.getElementById('categorySearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const items = document.querySelectorAll('#categoryList .card');
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(term) ? 'block' : 'none';
        });
    });
    </script>
</div>
<?= $this->endSection() ?>