<!-- Categories widget -->
<div class="card mb-4 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
    <div class="card-header" style="background-color: #074C87; color: white; font-weight: 600; padding: 0.75rem 1rem; border-radius: 0.5rem 0.5rem 0 0;">
        Categories
    </div>
    <div class="card-body">
        <div class="row">
            <?php 
            $chunks = array_chunk($categories ?? [], 3); // Split into groups of 3
            foreach ($chunks as $chunk): 
            ?>
                <div class="col-sm-6">
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($chunk as $category): ?>
                            <li class="mb-2">
                                <a href="<?= site_url('category/' . esc($category->seflink)) ?>" 
                                   class="text-decoration-none" 
                                   style="color: #074C87; font-weight: 500; transition: color 0.15s;">
                                    <?= esc($category->title) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>