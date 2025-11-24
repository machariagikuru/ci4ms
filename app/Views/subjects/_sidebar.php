<div class="card mb-4">
    <div class="card-header">
        <h5>Search Subjects</h5>
    </div>
    <div class="card-body">
        <div class="form-group mb-3">
            <input type="text" id="subjectSearch" class="form-control" placeholder="Search subjects...">
        </div>
        <div class="list-group" id="subjectList">
            <?php foreach ($allSubjects as $s): ?>
                <a href="<?= site_url("subject/{$s->id}") ?>" class="list-group-item list-group-item-action<?= (isset($subject) && $subject->id == $s->id) ? ' active' : '' ?>">
                    <?= esc($s->name) ?>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</div>

<script>
document.getElementById('subjectSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const items = document.querySelectorAll('#subjectList a');
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(term) ? 'block' : 'none';
    });
});
</script>