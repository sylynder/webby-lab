<?php if (has_session('error_message')) : ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <strong><?= error_message() ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (has_session('success_message')) : ?>
    <div class="alert alert-success alert-dismissible fade show">
        <strong><?= success_message() ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (has_session('info_message')) : ?>
    <div class="alert alert-info alert-dismissible fade show">
        <strong><?= info_message() ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (has_session('warn_message')) : ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <strong><?= warn_message() ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
