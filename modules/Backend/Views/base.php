<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noimageindex, nofollow, nosnippet">

    <title>StrandNotes| <?= $this->renderSection('title') ?> - Admin </title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <?= link_tag("be-assets/plugins/fontawesome-free/css/all.min.css") ?>
    <?= link_tag('be-assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>
    <?= link_tag("be-assets/css/adminlte.min.css") ?>
    <?= link_tag("be-assets/custom.css") ?>
    <?= csrf_meta() ?>
    <?= $this->renderSection('head') ?>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-kun-cms border-0">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-light-olive elevation-1">
            <a href="<?= base_url('backend') ?>" class="brand-link navbar-kun-cms text-center">
                <img src="/be-assets/img/logo-w.png" alt="" class="img-responsive" height="25">
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image d-flex align-items-center">
                        <img src="/be-assets/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info w-100">
                        <button class="btn btn-light w-100" type="button" data-toggle="collapse"
                            data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <?= $logged_in_user->firstname . ' ' . $logged_in_user->surname ?> <br>
                            <small class="text-success font-weight-bold">{ <?= $logged_in_user->name ?> }</small>
                        </button>
                    </div>
                </div>

                <div class="collapse mb-2 border-bottom" id="collapseExample">
                    <div class="card card-body">
                        <span><i class="fas fa-user"></i> <a class="link-black" href="<?= route_to('profile') ?>">Profile</a></span>
                        <div class="dropdown-divider"></div>
                        <span><i class="fas fa-sign-out-alt"></i> <a class="link-black"
                                href="<?= route_to('logout') ?>">Log Out</a></span>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-child-indent"
                        data-widget="treeview" role="menu" data-accordion="false">

                        <?php
                        $rendered = [];
                        function navigation($navigation, $uri, $child = null, &$rendered)
                        {
                            foreach ($navigation as $nav) :
                                if (in_array($nav->id, $rendered)) continue;
                                if ($nav->parent_pk == $child) :
                                    $rendered[] = $nav->id;
                                    $p = null;
                                    foreach ($navigation as $item) {
                                        if ($item->sefLink != 'profile' && $item->sefLink === $uri) {
                                            $p = $item;
                                            break;
                                        }
                                    }
                        ?>
                                    <li class="nav-item <?= (!empty($p) && $p->parent_pk == $nav->id) ? 'menu-is-opening menu-open' : '' ?>">
                                        <a href="<?php
                                                    $u = explode('/', $nav->sefLink);
                                                    if (empty($u[1])) echo route_to($u[0]);
                                                    else echo route_to($u[0], $u[1]); ?>"
                                            class="nav-link <?php if (!empty($p)) {
                                                                if ($nav->sefLink == $uri || $p->parent_pk == $nav->id) echo 'active';
                                                                else echo '';
                                                            } ?>">
                                            <i class="nav-icon <?= $nav->symbol ?>"></i>
                                            <p><?= lang($nav->pagename) ?><?= ($nav->hasChild == true) ? '<i class="right fas fa-angle-left"></i>' : '' ?></p>
                                        </a>
                                        <?php if ($nav->hasChild == true): ?>
                                            <ul class="nav nav-treeview">
                                                <?php navigation($navigation, $uri, $nav->id, $rendered); ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                        <?php
                                endif;
                            endforeach;
                        }

                        navigation($navigation, $uri, null, $rendered);
                        ?>

                        <!-- Notes Module -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-sticky-note"></i>
                                <p>
                                    Notes
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= route_to('noteUpload') ?>" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Upload Note</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= route_to('noteList') ?>" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manage Notes</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Exams Module -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>
                                    Exams
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= route_to('examPaperUpload') ?>" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Upload Paper</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= route_to('examPapersList') ?>" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manage Papers</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Subjects Module -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Subjects
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= route_to('subjectCreate') ?>" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Create Subject</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                        <a href="<?= route_to('subjectList') ?>" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manage Subjects</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <?= $this->renderSection('content') ?>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> <?= getGitVersion() ?>
            </div>
            <strong>Copyright &copy; <?= date('Y') ?>.</strong> All rights reserved.
        </footer>

        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    <?= script_tag("be-assets/plugins/jquery/jquery.min.js") ?>
    <?= script_tag("be-assets/plugins/bootstrap/js/bootstrap.bundle.min.js") ?>
    <?= script_tag("be-assets/js/adminlte.min.js") ?>
    <?= script_tag("be-assets/js/demo.js") ?>
    <?= script_tag("be-assets/plugins/sweetalert2/sweetalert2.min.js") ?>
    <?= $this->renderSection('javascript') ?>
</body>

</html>
