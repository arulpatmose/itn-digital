<?php

/**
 * backend/views/inc_footer.php
 *
 * Author: pixelcave
 *
 * The footer of each page (Backend pages)
 *
 */
?>

<!-- Footer -->
<footer id="page-footer" class="bg-body-light">
    <div class="content py-3">
        <div class="row fs-sm">
            <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
                Crafted with <i class="fa fa-heart text-danger"></i> by <a class="fw-semibold" href="https://arulpatmose.com"
                    target="_blank"><?php echo config('Template')->author; ?></a>
            </div>
            <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
                <a class="fw-semibold" href="<?php echo base_url(); ?>"
                    target="_blank"><?php echo config('Template')->site_title; ?></a> &copy;
                <span data-toggle="year-copy"></span>
            </div>
        </div>
    </div>
</footer>
<!-- END Footer -->