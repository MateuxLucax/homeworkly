<?php

    require_once $root . 'utils/SessionUtil.php';
    require_once $root . 'models/TipoUsuario.php';
    require_once $root . 'dao/TurmaDAO.php';
    $usuario_header = SessionUtil::usuarioLogado();
    $isAluno = $usuario_header->getTipo() == TipoUsuario::ALUNO;

    $turmas = match ($usuario_header->getTipo()) {
        TipoUsuario::ALUNO     => array(TurmaDAO::turmaAtualDeAluno($usuario_header->getId())),
        TipoUsuario::PROFESSOR => []
    };

?>

<header class="container-fluid my-4 px-4">
    <div class="d-grid gap-3 align-items-center" style="grid-template-columns: 1fr 2fr;">
        <?php foreach ($turmas as $turma): ?>
            <select class="form-select" aria-label="Opção inicial">
                <option <?= $isAluno ? 'selected disabled' : '' ?> ><?=  $turma->getNome() ?></option>
            </select>
        <?php endforeach; ?>

        <div class="d-flex flex-row-reverse">
            <a href="/sair" class="btn btn-outline-dark"><i class="fas fa-sign-out-alt me-2"></i>Desconectar</a>
            <div class="mx-2"></div>
            <a href="/<?= $usuario_header->getTipo() ?>/perfil" type="button" class="btn btn-outline-dark"><i class="far fa-user me-2"></i>Perfil</a>
        </div>
    </div>
</header>