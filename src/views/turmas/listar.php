<!DOCTYPE html>
<html lang="pt-BR">
<?php require $root.'/views/componentes/head.php'; ?>
<body>

    <main class="container">

        <h1 class="my-4"><?=$view['title'];?></h1>

        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ano</th>
                    <th>Ações</th>
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
                        <td style="width: 0;">
                            <div class="d-flex justify-content-end">
                                <!-- TODO Mudar para um link para /admin/turmas/alterar?id quando essa página existir -->
                                <!-- <button type="button"
                                        class="btn btn-primary btn-editar-turma me-4"
                                        title="Editar turma"
                                        data-id="<?=$turma->getId()?>"
                                        data-nome="<?=$turma->getNome()?>"
                                        data-ano="<?=$turma->getAno()?>"
                                >
                                    <i class="fas fa-edit"></i>
                                </button> -->
                                <button type="button"
                                        class="btn btn-danger btn-excluir-turma"
                                        title="Remover turma"
                                        data-id="<?=$turma->getId()?>"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
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

    <div class="modal fade" id="modal-excluir-turma">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <p>Tem certeza que deseja excluir esta turma?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="btn-confirmar-exclusao">Excluir</button>
                    <button class="btn btn-secondary" id="btn-cancelar-exclusao">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        //
        // Excluir turma
        //

        const elemModalExcluir = document.querySelector('#modal-excluir-turma');
        const modalExcluir     = new bootstrap.Modal(elemModalExcluir);
        const inputIdExcluir   = elemModalExcluir.querySelector('[name=id]');

        for (const btnExcluir of document.querySelectorAll('.btn-excluir-turma')) {
            btnExcluir.addEventListener('click', () => {
                inputIdExcluir.value = btnExcluir.dataset.id;
                modalExcluir.show();
            });
        }

        document.querySelector('#btn-cancelar-exclusao').addEventListener('click', () => {
            modalExcluir.hide();
            inputIdExcluir.value = null;
        });

        document.querySelector('#btn-confirmar-exclusao').addEventListener('click', () => {
            const id = inputIdExcluir.value;
            if (id) {
                fetch('excluir', { method: 'POST', body: JSON.stringify({ id }) })
                .then(response => {
                    if (response.status == 200) {
                        agendarAlertaSwal({
                            icon: 'success',
                            text: 'Turma excluída com sucesso'
                        });
                        window.location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: 'Não foi possível excluir a turma'
                        });
                    }
                    response.text().then(console.log);
                });
            }
            modalExcluir.hide();
            inputIdExcluir.value = null;
        });
    </script>


</body>
</html>

