<!-- Testimonial section -->
<div class="py-5" style="background-color: #074C87;">
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder" style="color: #e9ff4e;">What Learners Say</h2>
        </div>

        <?php if (!empty($testimonials) && is_array($testimonials)): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div id="testimonialCarousel" 
                         class="carousel slide" 
                         data-bs-ride="carousel" 
                         data-bs-interval="3000">
                        
                        <!-- Slides -->
                        <div class="carousel-inner rounded-3 shadow-sm">
                            <?php foreach ($testimonials as $index => $testimonial): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" style="background-color: white; padding: 2rem;">
                                    <div class="text-center">
                                        <p class="fs-5 fst-italic mb-4" style="color: #074C87; font-style: italic;">
                                            “<?= esc($testimonial['content']) ?>”
                                        </p>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <?php if (!empty($testimonial['avatar'] ?? null)): ?>
                                                <img class="rounded-circle me-3" 
                                                     src="<?= esc($testimonial['avatar']) ?>" 
                                                     alt="<?= esc($testimonial['name']) ?>" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px; background-color: #074C87; color: white; font-weight: bold;">
                                                    <?= strtoupper(substr($testimonial['name'] ?? 'U', 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="text-start">
                                                <div class="fw-bold" style="color: #074C87;"><?= esc($testimonial['name']) ?></div>
                                                <?php if (!empty($testimonial['role'])): ?>
                                                    <div style="color: #555; font-size: 0.9rem;"><?= esc($testimonial['role']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Controls -->
                        <?php if (count($testimonials) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center">
                <p style="color: #074C87; font-style: italic;">No testimonials available.</p>
            </div>
        <?php endif; ?>
    </div>
</div>