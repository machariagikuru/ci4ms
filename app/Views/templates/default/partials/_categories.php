<!-- Categories Section -->
<section class="py-5">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Browse by Category</h2>
            <p class="lead text-muted">Explore our content organized by topic</p>
        </div>
        <div class="row gx-4">
            <?php if (!empty($categories) && is_array($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= esc($cat->title) ?></h5>
                                <?php if (!empty($cat->description)): ?>
                                    <p class="card-text flex-grow-1"><?= esc($cat->description) ?></p>
                                <?php endif; ?>
                                <a href="<?= site_url('category/' . $cat->seflink) ?>" class="btn btn-outline-primary mt-auto">
                                    View Posts
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">No categories available.</div>
            <?php endif; ?>
        </div>
    </div>
</section>