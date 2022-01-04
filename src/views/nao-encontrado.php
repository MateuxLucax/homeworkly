<!DOCTYPE html>
<html lang="en">
<?php require_once $root.'/views/componentes/head.php' ?>

<body>

    <main class="container">
        <div class="alert alert-danger text-center mt-5" role="alert">
            <h4 class="alert-heading">404 - Não encontrado</h4>
            <hr>
            <p class="mb-0"><?= $view['mensagem'] ?></p>
        </div>
    </main>
    
</body>

</html>