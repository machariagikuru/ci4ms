<!-- Categories Section -->
<section class="py-5" style="background-color: #fff;">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder" style="color: #074C87;">Browse by Category</h2>
            <p class="lead" style="color: #074C87; opacity: 0.8;">Explore our content organized by topic</p>
        </div>
        <div class="row gx-4">
            <?php if (!empty($categories) && is_array($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: white;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title" style="color: #074C87; font-weight: 600;"><?= esc($cat->title) ?></h5>
                                <?php if (!empty($cat->description)): ?>
                                    <p class="card-text flex-grow-1" style="color: #495057;"><?= esc($cat->description) ?></p>
                                <?php endif; ?>
                                <a href="<?= site_url('category/' . $cat->seflink) ?>" 
                                   class="btn mt-auto"
                                   style="background-color: #074C87; color: white; font-weight: 500; border: none; padding: 0.5rem 1rem;">
                                    View Posts
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p style="color: #074C87; font-style: italic;">No categories available.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>