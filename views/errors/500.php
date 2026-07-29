<?php $title='Error'; ob_start(); ?><section class="page-head"><div class="container"><p class="eyebrow">500</p><h1>Algo salió mal.</h1><p class="lead"><?= e($message ?? 'Ocurrió un error inesperado.') ?></p><a class="button" href="<?= url() ?>">Volver al inicio</a></div></section><?php $content=ob_get_clean(); require __DIR__.'/../website/layouts/main.php'; ?>

