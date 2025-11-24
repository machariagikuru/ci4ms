<!-- Latest Blog Posts -->
<section class="py-5" style="background-color: #e6f0fa;">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder" style="color: #074C87;">Latest Posts</h2>
            <p class="lead" style="color: #074C87; opacity: 0.8;">From our blog</p>
        </div>
        <div class="row gx-5">
            <?php if (!empty($latestBlogs) && is_array($latestBlogs)): ?>
                <?php foreach (array_slice($latestBlogs, 0, 3) as $blog): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: white;">
                            <?php if (!empty($blog->seo->coverImage)): ?>
                                <img src="<?= esc($blog->seo->coverImage) ?>" class="card-img-top" alt="<?= esc($blog->title) ?>" style="object-fit: cover; height: 160px;">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title" style="color: #074C87; font-weight: 600;"><?= esc($blog->title) ?></h5>
                                <p class="card-text flex-grow-1" style="color: #495057;"><?= esc(substr(strip_tags($blog->content), 0, 100)) ?>...</p>
                                <a href="<?= site_url('blog/' . $blog->seflink) ?>" 
                                   class="btn mt-auto"
                                   style="background-color: #074C87; color: white; font-weight: 500; border: none; padding: 0.5rem 1rem; border-radius: 0.375rem;">
                                    Read More
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p style="color: #074C87; font-style: italic;">No blog posts available.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>