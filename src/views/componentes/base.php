<!DOCTYPE html>
<html lang="pt-BR">
<?php require $root . '/views/componentes/head.php'; ?>
<body>
<main class="min-vh-100">
    <div class="container-fluid">
        <div class="min-vh-100">
            <div class="col-3 col-xxl-2 bg-light fixed-top p-2">
                <?php require $root . '/views/componentes/sidebar.php'; ?>
            </div>
            <div class="col-9 offset-3 col-xxl-10 offset-xxl-2">
                <div class="container-xl">
                    <div class="row">
                        <?php require $root . '/views/componentes/header.php'; ?>
                    </div>
                    <div class="row px-4">
                        <?php require $root . $view['content_path'] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($view['criar_tarefa_modal'])) { require $root . $view['criar_tarefa_modal']; } ?>
</main>
</body>
</html>