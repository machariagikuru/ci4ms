<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #074C87;">Tags</h2>
        <input type="text" 
               id="tagSearch" 
               class="form-control w-25" 
               placeholder="Search tags..."
               style="border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
    </div>

    <div class="row" id="tagList">
        <?php if (!empty($tags)): ?>
            <?php foreach ($tags as $tag): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <a href="<?= site_url('tag/' . esc($tag->seflink)) ?>" 
                       class="btn w-100 text-start"
                       style="background-color: #e6f0fa; color: #074C87; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.75rem 1rem; text-decoration: none; transition: background-color 0.15s;">
                        <?= esc($tag->tag) ?>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light border" role="alert" style="background-color: #f8fafc; color: #074C87; border-color: #e6f0fa;">
                    No tags found.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($pager) && $pager): ?>
        <div class="mt-4">
            <?= $pager ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('tagSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase().trim();
    const items = document.querySelectorAll('#tagList .col-md-6');
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(term) ? 'block' : 'none';
    });
});
</script>
<?= $this->endSection() ?>