    <!-- jQuery (required for jQuery Validation plugin) -->
    <script src="<?php echo base_url('assets/js/lib/jquery.min.js'); ?>"></script>

    <script src="<?php echo base_url('assets/js/lib/URI.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/lib/moment.min.js'); ?>"></script>

    <!-- Page JS Plugins -->
    <script src="<?php echo base_url('assets/js/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/plugins/datatables-responsive/js/dataTables.responsive.min.js'); ?>">
    </script>
    <script
        src="<?php echo base_url('assets/js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js'); ?>">
    </script>
    <script src="<?php echo base_url('assets/js/plugins/datatables-buttons/dataTables.buttons.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js'); ?>">
    </script>
    <?php if (isset($controller) && isset($method) && ($controller === 'Schedules' || $controller === 'Accounts' || $controller === 'Schedule') && $method === 'index') { ?>
        <script src="<?php echo base_url('assets/js/plugins/datatables-buttons-jszip/jszip.min.js'); ?>">
        </script>
        </script>
        <script src="<?php echo base_url('assets/js/plugins/datatables-buttons/buttons.print.min.js'); ?>">
        </script>
        <script src="<?php echo base_url('assets/js/plugins/datatables-buttons/buttons.html5.min.js'); ?>">
        </script>
    <?php } ?>

    <script src="<?php echo base_url('assets/js/plugins/sweetalert2/sweetalert2.min.js'); ?>"></script>

    <script src="<?php echo base_url('assets/js/plugins/ion-rangeslider/js/ion.rangeSlider.min.js'); ?>"></script>

    <script src="<?php echo base_url('assets/js/plugins/select2/js/select2.full.min.js'); ?>"></script>

    <script src="<?php echo base_url('assets/js/plugins/flatpickr/flatpickr.min.js'); ?>"></script>

    <!-- Global JS Code -->
    <script src="<?php echo base_url('assets/js/custom/global.js?v=' . time()); ?>"></script>
    <script src="<?php echo base_url('assets/js/custom/datatables.js?v=' . time()); ?>"></script>
    <script src="<?php echo base_url('assets/js/custom/alerts.js?v=' . time()); ?>"></script>

    <!-- Get Session Flash Data & Show Dialog -->

    <?php if ($success = session()->getFlashdata('success')): ?>
        <script>
            <?php if (is_array($success)): ?>
                <?php foreach ($success as $message): ?>
                    jqNotify({
                        type: 'success',
                        icon: 'fa fa-check me-1',
                        message: '<?php echo esc($message); ?>'
                    });
                <?php endforeach; ?>
            <?php else: ?>
                toast.fire('Success', '<?php echo esc($success); ?>', 'success');
            <?php endif; ?>
        </script>
    <?php elseif ($error = session()->getFlashdata('error')): ?>
        <script>
            <?php if (is_array($error)): ?>
                <?php foreach ($error as $message): ?>
                    jqNotify({
                        type: 'danger',
                        icon: 'fa fa-times me-1',
                        message: '<?php echo esc($message); ?>'
                    });
                <?php endforeach; ?>
            <?php else: ?>
                toast.fire('Oops...', '<?php echo esc($error); ?>', 'error');
            <?php endif; ?>
        </script>
    <?php elseif ($info = session()->getFlashdata('info')): ?>
        <script>
            <?php if (is_array($info)): ?>
                <?php foreach ($info as $message): ?>
                    jqNotify({
                        type: 'info',
                        icon: 'fa fa-info-circle me-1',
                        message: '<?php echo esc($message); ?>'
                    });
                <?php endforeach; ?>
            <?php else: ?>
                toast.fire('Info', '<?php echo esc($info); ?>', 'info');
            <?php endif; ?>
        </script>
    <?php endif; ?>