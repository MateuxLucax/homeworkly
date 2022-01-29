<?php

    require_once $root.'utils/SessionUtil.php';
    $usuario_header = SessionUtil::usuarioLogado();

?>

<header class="container-fluid my-4 px-4">
    <div class="d-grid gap-3 align-items-center" style="grid-template-columns: 1fr 2fr;">
        <select class="form-select" aria-label="Opção inicial">
            <option selected>1 ano - 2021</option>
        </select>

        <div class="d-flex flex-row-reverse">
            <a href="/sair" class="btn btn-outline-dark"><i class="fas fa-sign-out-alt me-2"></i>Desconectar</a>
            <div class="mx-2"></div>
            <a href="/<?= $usuario_header->getTipo() ?>/perfil" type="button" class="btn btn-outline-dark"><i class="far fa-user me-2"></i>Perfil</a>
        </div>
    </div>
</header>