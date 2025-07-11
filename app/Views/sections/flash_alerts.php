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