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

     <!-- Global JS Code -->
     <script src="<?php echo base_url('assets/js/custom/global.js?v=' . time()); ?>"></script>

     <!-- include script & Plugins -->
     <?= $this->renderSection('other-scripts') ?>