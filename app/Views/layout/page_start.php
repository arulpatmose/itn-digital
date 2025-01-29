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

      <?php if (isset($config['inc_side_overlay']) && $config['inc_side_overlay']) {
        echo $this->include('layout/backend/side_overlay');
      } ?>
      <?php if (isset($config['inc_sidebar']) && $config['inc_sidebar']) {
        echo $this->include('layout/backend/sidebar');
      } ?>
      <?php if (isset($config['inc_header']) && $config['inc_header']) {
        echo $this->include('layout/backend/header');
      } ?>
      <!-- Main Container -->
      <main id="main-container">
        <?php if (isset($config['inc_hero']) && $config['inc_hero']) {
          /* Trim Page or Controller Title */
          if (isset($config['title'])) {
            $parts = explode("-", $config['title']);
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
                      <?php echo $_title; ?>
                    </li>
                  </ol>
                </nav>
              </div>
            </div>
          </div>
        <?php } ?>
        <!-- END Hero -->