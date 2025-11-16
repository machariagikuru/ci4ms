<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <?= $this->renderSection('metatags') ?>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="/templates/default/assets/node_modules/startbootstrap-modern-business/dist/assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="/templates/default/assets/node_modules/startbootstrap-modern-business/dist/css/styles.css" rel="stylesheet" />
    <link href="/templates/default/assets/ci4ms.css" rel="stylesheet" />
    <link href="/be-assets/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
    <?= $this->renderSection('head') ?>
    <?= (!empty($schema)) ? $schema : '' ?>
</head>

<body class="d-flex flex-column h-100">
    <main class="flex-shrink-0">
        <!-- Navigation-->
        <?= $this->include('templates/default/partials/_navbar') ?>
        <?= $this->renderSection('content') ?>
    </main>
    <!-- Footer-->
    <?= $this->include('templates/default/partials/_footer') ?>

    <div class="modal fade modal-search" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="text" id="product-search" class="form-control" placeholder="Type to search...">
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JS-->
    <script src="/templates/default/assets/node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="/templates/default/assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="<?= base_url("templates/default/assets/node_modules/jquery/dist/jquery.js") ?>"></script>
    <script src="/be-assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?= base_url("templates/default/assets/ci4ms.js") ?>"></script>
    <?= $this->renderSection('javascript') ?>

    <script>
    // Remember sidebar state
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('userSidebar');
        const isExpanded = localStorage.getItem('userSidebarExpanded') === 'true';
        if (sidebar && isExpanded) {
            sidebar.classList.add('show');
        }

        // Toggle on mobile
        const toggleBtn = document.querySelector('[data-bs-target="#userSidebar"]');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const expanded = sidebar.classList.contains('show');
                localStorage.setItem('userSidebarExpanded', !expanded);
            });
        }
    });
    </script>
</body>

</html>