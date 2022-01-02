<!DOCTYPE html>
<html lang="pt-BR">
<?php require_once $root.'/views/componentes/head.php'; ?>
<body>

<!--TODO não em h1, e colocar links para a turma e para a disciplina
    TODO e também deixar num estilo mais breadcrumbs
    e fazer o mesmo na view turma.php-->
<div class="header">
    <h1><?=$view['turma_ano']?> / <?=$view['turma_nome']?> / <b><?=$view['disciplina_nome']?></b></h1>
</div>

<main class="container">
    <form class="form-criar-tarefa">
        <input type="hidden" name="disciplina" value="<?= $view['disciplina_id'] ?>" />
        <input type="hidden" name="professor" value="<?= $view['professor_id'] ?>" />
    </form>
</main>

</body>