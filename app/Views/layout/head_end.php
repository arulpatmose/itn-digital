    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/js/plugins/sweetalert2/sweetalert2.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/js/plugins/ion-rangeslider/css/ion.rangeSlider.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/js/plugins/select2/css/select2.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/js/plugins/flatpickr/flatpickr.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/js/plugins/flatpickr/themes/dark.css'); ?>">

    <!-- OneUI framework -->
    <link rel="stylesheet" id="css-main" href="<?php echo base_url('assets/css/oneui.min.css'); ?>">

    <!-- Global CSS Code -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/global.css'); ?>">

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/amethyst.min.css"> -->
    <?php if ($config['theme']) { ?>
        <link rel="stylesheet" id="css-theme" href="<?php echo base_url('assets/css/themes/' . $config['theme'] . '.min.css'); ?>">
    <?php } ?>
    <!-- END Stylesheets -->
    </head>

    <body>