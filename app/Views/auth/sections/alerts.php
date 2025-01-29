<?php if (session('error') !== null) : ?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <p class="mb-0">
        <?= session('error') ?>!
    </p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php elseif (session('errors') !== null) : ?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <?php if (is_array(session('errors'))) : ?>
    <?php foreach (session('errors') as $error) : ?>
    <?= $error ?>
    <br>
    <?php endforeach ?>
    <?php else : ?>
    <?= session('errors') ?>
    <?php endif ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif ?>

<?php if (session('message') !== null) : ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <p class="mb-0"><?= session('message') ?></p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif ?>