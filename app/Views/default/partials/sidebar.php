<?php

/**
 * backend/views/inc_sidebar.php
 *
 * Author: pixelcave
 *
 * The sidebar of each page (Backend pages)
 *
 */
?>

<!-- Sidebar -->
<!--
    Sidebar Mini Mode - Display Helper classes

    Adding 'smini-hide' class to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
    Adding 'smini-show' class to an element will make it visible (opacity: 1) when the sidebar is in mini mode
        If you would like to disable the transition animation, make sure to also add the 'no-transition' class to your element

    Adding 'smini-hidden' to an element will hide it when the sidebar is in mini mode
    Adding 'smini-visible' to an element will show it (display: inline-block) only when the sidebar is in mini mode
    Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
-->
<nav id="sidebar" aria-label="Main Navigation">
    <!-- Side Header -->
    <div class="content-header">
        <!-- Logo -->
        <a class="fw-semibold text-dual" href="<?php echo base_url(); ?>">
            <span class="smini-visible">
                <i class="fa fa-circle-notch text-danger"></i>
            </span>
            <span class="smini-hide fs-5 tracking-wider">
                <img class="site-logo dark-logo" src="<?php echo base_url('assets/media/logos/dark_logo.png'); ?>"
                    alt="ITN Digital">
                <img class="site-logo light-logo" src="<?php echo base_url('assets/media/logos/light_logo.png'); ?>"
                    alt="ITN Digital">
            </span>
        </a>
        <!-- END Logo -->
        <!-- Extra -->
        <!-- Close Sidebar, Visible only on mobile screens -->
        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
        <div class="d-flex align-items-center gap-1">
            <!-- Dark Mode -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-alt-secondary" id="sidebar-dark-mode-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="far fa-fw fa-moon" data-dark-mode-icon></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end smini-hide border-0"
                    aria-labelledby="sidebar-dark-mode-dropdown">
                    <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout"
                        data-action="dark_mode_off" data-dark-mode="off">
                        <i class="far fa-sun fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">Light</span>
                    </button>
                    <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout"
                        data-action="dark_mode_on" data-dark-mode="on">
                        <i class="far fa-moon fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">Dark</span>
                    </button>
                    <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout"
                        data-action="dark_mode_system" data-dark-mode="system">
                        <i class="fa fa-desktop fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">System</span>
                    </button>
                </div>
            </div>
            <!-- END Dark Mode -->
            <a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close"
                href="javascript:void(0)">
                <i class="fa fa-fw fa-times"></i>
            </a>
        </div>
        <!-- END Close Sidebar -->
        <!-- END Extra -->
    </div>
    <!-- END Side Header -->

    <!-- Sidebar Scrolling -->
    <div class="js-sidebar-scroll">
        <!-- Side Navigation -->
        <div class="content-side">
            <ul class="nav-main">
                <?php if (isset($config['main_nav'])) {
                    echo $config['main_nav'];
                } ?>
            </ul>
        </div>
        <!-- END Side Navigation -->
    </div>
    <!-- END Sidebar Scrolling -->
</nav>
<!-- END Sidebar -->