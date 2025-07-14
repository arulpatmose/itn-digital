<!doctype html>
<html lang="en-US">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title><?= $config['title'] ?  $config['title'] : ""; ?></title>

  <meta name="description" content="<?= $config['description'] ?  $config['description'] : ""; ?>">
  <meta name="author" content="<?= $config['author'] ? $config['author'] : ""; ?>">
  <meta name="robots" content="<?= $config['robots'] ? $config['robots'] : "noindex, nofollow"; ?>">

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

  <?= $this->include('blank/styles') ?>
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
  <div id="page-container" class="<?php echo $config['page_classes']; ?>">
    <?php if (isset($config['page_loader']) && $config['page_loader']) { ?>
      <!-- Page loader (functionality is initialized in Template._uiHandlePageLoader()) -->
      <!-- If #page-loader markup and also the "show" class is added, the loading screen will be enabled and auto hide once the page loads -->
      <div id="page-loader" class="show"></div>
    <?php } ?>
    <!-- Main Container -->
    <main id="main-container">
      <!-- Page Content -->
      <?= $this->renderSection('content') ?>
      <!-- END Page Content -->
    </main>
  </div>
  <!-- END Page Container -->

  <?= $this->include('blank/scripts') ?>
</body>

</html>