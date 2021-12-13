<!DOCTYPE html>
<html lang="pt-BR">
<?php require $root.'/views/componentes/head.php'; ?>
<body>

    <header>
        <?php require_once $root.'/views/componentes/navbar-admin.php'; ?>
    </header>

    <main class="container">

        <h1 class="my-4"><?=$view['title'];?></h1>

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
            <tfoot>
                <tr>
                    <td colspan="5">
                        <div class="d-flex justify-content-end">
                            <a href="criar" type="button" class="btn btn-success ms-auto" role="button">
                                <i class="fas fa-plus-circle m-0"></i>
                                Adicionar turma
                            </a>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>

    </main>

</body>
</html>

