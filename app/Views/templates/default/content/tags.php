<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tags</h2>
        <input type="text" id="tagSearch" class="form-control w-25" placeholder="Search tags...">
    </div>

    <div class="row" id="tagList">
        <?php if (!empty($tags)): ?>
            <?php foreach ($tags as $tag): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <a href="<?= site_url('tag/' . $tag->seflink) ?>" class="btn btn-outline-primary w-100 text-start">
                        <?= esc($tag->tag) ?>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">No tags found.</div>
        <?php endif; ?>
    </div>

    <?= $pager ?? '' ?>

    <script>
    document.getElementById('tagSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const items = document.querySelectorAll('#tagList .btn');
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(term) ? 'block' : 'none';
        });
    });
    </script>
</div>
<?= $this->endSection() ?>