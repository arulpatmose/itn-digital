     <!--
        OneUI JS

        Core libraries and functionality
        webpack is putting everything together at assets/_js/main/app.js
    -->
     <script src="<?php echo base_url('assets/js/oneui.app.min.js'); ?>"></script>

     <!-- jQuery (required for jQuery Validation plugin) -->
     <script src="<?php echo base_url('assets/js/lib/jquery.min.js'); ?>"></script>

     <script src="<?php echo base_url('assets/js/lib/URI.min.js'); ?>"></script>
     <script src="<?php echo base_url('assets/js/lib/moment.min.js'); ?>"></script>

     <!-- Page JS Plugins -->
     <script src="<?php echo base_url('assets/js/plugins/datatables/dataTables.min.js'); ?>"></script>
     <script src="<?php echo base_url('assets/js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js'); ?>"></script>
     <script src="<?php echo base_url('assets/js/plugins/datatables-responsive/js/dataTables.responsive.min.js'); ?>"></script>
     <script src="<?php echo base_url('assets/js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js'); ?>"></script>
     </script>
     <script
         src="<?php echo base_url('assets/js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js'); ?>">
     </script>
     <script src="<?php echo base_url('assets/js/plugins/datatables-buttons/dataTables.buttons.min.js'); ?>"></script>
     <script src="<?php echo base_url('assets/js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js'); ?>">
     </script>
     <script src="<?php echo base_url('assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js'); ?>">
     </script>
     <script src="<?php echo base_url('assets/js/plugins/sweetalert2/sweetalert2.min.js'); ?>"></script>
     <script src="<?php echo base_url('assets/js/plugins/ion-rangeslider/js/ion.rangeSlider.min.js'); ?>"></script>
     <script src="<?php echo base_url('assets/js/plugins/select2/js/select2.full.min.js'); ?>"></script>
     <script src="<?php echo base_url('assets/js/plugins/flatpickr/flatpickr.min.js'); ?>"></script>

     <script>
         One.helpersOnLoad(['jq-notify']);
     </script>

     <!-- Global JS Code -->
     <script src="<?php echo base_url('assets/js/custom/global.js?v=' . time()); ?>"></script>
     <script src="<?php echo base_url('assets/js/custom/datatables.js?v=' . time()); ?>"></script>
     <script src="<?php echo base_url('assets/js/custom/alerts.js?v=' . time()); ?>"></script>

     <!-- Include Custom Scripts & Plugins -->
     <?= $this->renderSection('other-scripts') ?>

     <?= $this->include('sections/flash_alerts') ?>