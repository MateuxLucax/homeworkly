<!DOCTYPE html>
<html lang="pt-BR">
<?php require $root . '/views/componentes/head.php'; ?>
<body>
<main class="vh-100">
    <div class="row h-100">
        <div class="col-3 col-xxl-2 bg-light fixed-top">
            <?php require $root . '/views/componentes/sidebar.php'; ?>
        </div>
        <div class="col-9 offset-3 col-xxl-10 offset-xxl-2">
            <div class="row">
                <?php require $root . '/views/componentes/header.php'; ?>
            </div>
            <div class="row">
                <?php require $root .'/views/' . $view['content_path'] ?>
            </div>
        </div>
    </div>
</main>
</body>
</html>