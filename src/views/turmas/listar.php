<!DOCTYPE html>
<html lang="pt-BR">
<?php require $root.'/views/componentes/head.php'; ?>
<body>

    <header>
        <?php require_once $root.'/views/componentes/navbar-admin.php'; ?>
    </header>

    <main class="container">

        <!-- TODO em vez de listar turmas em tabela, usar o card group do bootstrap, 3 cards de turma por linha
             e criar uma classe CSS pra o card, no hover, parecer que sobe (sombra embaixo etc.) e ficar com cursor pointer
             aí o evento click redireciona pra /turmas/turma?id={} -->

        <div class="d-flex align-items-center my-3">
            <h1>Turmas</h1>

            <a href="criar" type="button" class="btn btn-success ms-auto" role="button">
                <i class="fas fa-plus-circle m-0"></i>
                Adicionar turma
            </a>
        </div>

        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ano</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($view['turmas'])): ?>
                <tr>
                    <td colspan="4" class="text-center">
                        Nenhuma turma encontrada
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($view['turmas'] as $turma): ?>
                    <tr class="align-middle">
                        <td><?=$turma->getId()?></td>
                        <td>
                            <a href="turma?id=<?=$turma->getId()?>" class="btn btn-link" role="button" >
                                <?=$turma->getNome()?>
                            </a>
                        </td>
                        <td><?=$turma->getAno()?></td>
                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>
            </tbody>
        </table>

    </main>

</body>
</html>

