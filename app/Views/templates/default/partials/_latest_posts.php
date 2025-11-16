<!-- Latest Blog Posts -->
<section class="py-5">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Latest Posts</h2>
            <p class="lead text-muted">From our blog</p>
        </div>
        <div class="row gx-5">
            <?php if (!empty($latestBlogs) && is_array($latestBlogs)): ?>
                <?php foreach (array_slice($latestBlogs, 0, 3) as $blog): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($blog->seo->coverImage)): ?>
                                <img src="<?= esc($blog->seo->coverImage) ?>" class="card-img-top" alt="<?= esc($blog->title) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($blog->title) ?></h5>
                                <p class="card-text"><?= esc(substr(strip_tags($blog->content), 0, 100)) ?>...</p>
                                <a href="<?= site_url('blog/' . $blog->seflink) ?>" class="btn btn-outline-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">No blog posts available.</div>
            <?php endif; ?>
        </div>
    </div>
</section>