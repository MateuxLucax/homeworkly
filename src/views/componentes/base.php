<!DOCTYPE html>
<html lang="pt-BR">
<?php require $root . '/views/componentes/head.php'; ?>
<body>
<main class="vh-100 container-fluid">
    <div class="row">
        <div class="col-3 bg-light">
            <?php require $root . '/views/componentes/sidebar.php'; ?>
        </div>
        <div class="col-9">
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