<!doctype html>
<html lang="en-US" class="<?= $config['html_classes'] ?? ''; ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title><?= $config['title'] ?  $config['title'] : ""; ?></title>

    <meta name="description" content="<?= $config['description'] ?  $config['description'] : ""; ?>">
    <meta name="author" content="<?= $config['author'] ? $config['author'] : ""; ?>">
    <meta name="robots" content="<?= $config['robots'] ?  $config['robots'] : "noindex, nofollow"; ?>">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="<?= $config['title'] ?  $config['title'] : ""; ?>">
    <meta property="og:site_name" content="ITN Digital - Portal">
    <meta property="og:description" content="<?= $config['description'] ?  $config['description'] : ""; ?>">
    <meta property="og:type" content="">
    <meta property="og:url" content="<?= $config['og_url_site'] ?  $config['og_url_site'] : ""; ?>">
    <meta property="og:image" content="">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="<?= base_url('assets/media/favicons/favicon.png'); ?>">
    <link rel="icon" type="image/png" sizes="192x192"
        href="<?= base_url('assets/media/favicons/favicon-192x192.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?= base_url('assets/media/favicons/apple-touch-icon-180x180.png'); ?>">
    <!-- END Icons -->

    <?= $this->include('default/styles') ?>
</head>

<body>
    <!-- Page Container -->
    <!--
        Available classes for #page-container:

    GENERIC

      'remember-theme'                            Remembers active color theme and dark mode between pages using localStorage when set through
                                                  - Theme helper buttons [data-toggle="theme"],
                                                  - Layout helper buttons [data-toggle="layout" data-action="dark_mode_[on/off/toggle]"]
                                                  - ..and/or One.layout('dark_mode_[on/off/toggle]')

    SIDEBAR & SIDE OVERLAY

      'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
      'sidebar-mini'                              Mini hoverable Sidebar (screen width > 991px)
      'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
      'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
      'sidebar-dark'                              Dark themed sidebar

      'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
      'side-overlay-o'                            Visible Side Overlay by default

      'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

      'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

    HEADER

      ''                                          Static Header if no class is added
      'page-header-fixed'                         Fixed Header

    HEADER STYLE

      ''                                          Light themed Header
      'page-header-dark'                          Dark themed Header

    MAIN CONTENT LAYOUT

      ''                                          Full width Main Content if no class is added
      'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
      'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)

    DARK MODE

      'sidebar-dark page-header-dark dark-mode'   Enable dark mode (light sidebar/header is not supported with dark mode)
    -->
    <div id="page-container" class="<?= $config['page_classes'] ?? ''; ?>">
        <?php if (isset($config['page_loader']) && $config['page_loader']) { ?>
            <!-- Page loader (functionality is initialized in Template._uiHandlePageLoader()) -->
            <!-- If #page-loader markup and also the "show" class is added, the loading screen will be enabled and auto hide once the page loads -->
            <div id="page-loader" class="show"></div>
        <?php } ?>

        <?php if (isset($config['inc_side_overlay']) && $config['inc_side_overlay']) {
            echo $this->include('default/partials/side_overlay');
        } ?>
        <?php if (isset($config['inc_sidebar']) && $config['inc_sidebar']) {
            echo $this->include('default/partials/sidebar');
        } ?>
        <?php if (isset($config['inc_header']) && $config['inc_header']) {
            echo $this->include('default/partials/header');
        } ?>
        <!-- Main Container -->
        <main id="main-container">
            <?php if (isset($config['inc_hero']) && $config['inc_hero']) {
                /* Trim Page or Controller Title */
                if (isset($config['title'])) {
                    $parts = explode("|", $config['title']);
                    $_title = trim($parts[0]);
                } else {
                    $_title = "ITN Digital";
                }
            ?>
                <!-- Hero -->
                <div class="bg-body-light">
                    <div class="content content-full">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                            <div class="flex-grow-1">
                                <h1 class="h3 fw-bold mb-2">
                                    <?php echo $_title; ?>
                                </h1>
                                <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                                    <?php if (isset($config['page_description'])) {
                                        echo $config['page_description'];
                                    } ?>
                                </h2>
                            </div>
                            <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-alt">
                                    <li class="breadcrumb-item">
                                        <a class="link-fx" href="javascript:void(0)">Home</a>
                                    </li>
                                    <li class="breadcrumb-item" aria-current="page">
                                        <?php
                                        if (isset($config['title'])) {
                                            // Split the title by either "|" or "-"
                                            $parts = preg_split("/[\|\-]/", $config['title']);

                                            // Trim and echo the first part
                                            echo trim($parts[0]);
                                        } else {
                                            echo "ITN Digital";
                                        } ?>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!-- END Hero -->

            <!-- Page Content -->
            <?= $this->renderSection('content') ?>
            <!-- END Page Content -->

        </main>
        <!-- END Main Container -->
        <?php if (isset($config['inc_footer']) && $config['inc_footer']) {
            echo $this->include('default/partials/footer');
        } ?>
    </div>
    <!-- END Page Container -->

    <?= $this->include('default/scripts') ?>
</body>

</html>