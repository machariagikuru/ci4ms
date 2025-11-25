<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #074C87;">Categories</h2>
        <input type="text" 
               id="categorySearch" 
               class="form-control w-25" 
               placeholder="Search categories..."
               style="border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem; background-color: #f8fafc;  color: #074C87; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
    </div>

    <div class="row" id="categoryList">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
                <div class="col-md-6 col-lg-4 mb-4" style="background-color: #e6f0fa; padding: 15px; border-radius: 0.5rem;   box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">       
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold" style="color: #074C87; font-size: 1.1rem; margin-bottom: 0.75rem;">
                                <?= esc($cat->title) ?>
                            </h5>
                            <?php if (!empty($cat->description)): ?>
                                <p class="card-text flex-grow-1" style="color: #555; line-height: 1.5; font-size: 0.95rem;">
                                    <?= esc($cat->description) ?>
                                </p>
                            <?php endif; ?>
                            <a href="<?= site_url('category/' . $cat->seflink) ?>" 
                               class="btn mt-auto"
                               style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.5rem 1rem; text-decoration: none; font-size: 0.95rem;">
                                View Posts
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light border" role="alert" style="background-color: #f8fafc; color: #074C87; border-color: #e6f0fa;">
                    No categories found.
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
document.getElementById('categorySearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase().trim();
    const items = document.querySelectorAll('#categoryList .col-md-6');
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(term) ? 'block' : 'none';
    });
});
</script>
<?= $this->endSection() ?>