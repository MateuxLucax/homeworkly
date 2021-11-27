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
                        <td><?=$turma['id']?></td>
                        <td><?=$turma['nome']?></td>
                        <td><?=$turma['ano']?></td>
                        <td style="width: 0;">
                            <div class="d-flex justify-content-end">
                                <button type="button"
                                        class="btn btn-primary btn-editar-turma me-4"
                                        title="Editar turma"
                                        data-id="<?=$turma['id']?>"
                                        data-nome="<?=$turma['nome']?>"
                                        data-ano="<?=$turma['ano']?>"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-danger btn-excluir-turma"
                                        title="Remover turma"
                                        data-id="<?=$turma['id']?>"
                                >
                                    <i class="bi bi-trash-fill"></i>
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
                            <button type="button"
                                    class="btn btn-success ms-auto"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-nova-turma"
                            >
                                <i class="bi bi-plus-square"></i> Adicionar turma
                            </button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>

    </main>

    <div class="modal fade" id="modal-nova-turma">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova turma</h5>
                </div>

                <form id="form-nova-turma">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="ano" class="form-label">Ano</label>
                            <input type="number" name="ano" id="ano" class="form-control" value="<?=date('Y')?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Criar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-editar-turma">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar turma</h5>
                </div>

                <form id="form-editar-turma">

                    <input type="hidden" name="id">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="ano" class="form-label">Ano</label>
                            <input type="text" name="ano" id="ano" class="form-control">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Editar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
        // Alertas
        //

        <?php
            $alertas = [
                'turma-excluida-sucesso' => [
                    'tipo'   => 'success',
                    'titulo' => 'Excluída',
                    'texto'  => 'Turma excluída com sucesso'
                ],
                'turma-criada-sucesso' => [
                    'tipo'   => 'success',
                    'titulo' => 'Criada',
                    'texto'  => 'Turma criada com sucesso'
                ],
                'turma-editada-sucesso' => [
                    'tipo'   => 'success',
                    'titulo' => 'Editada',
                    'texto'  => 'Turma editada com sucesso'
                ]
            ];
        ?>

        <?php if (isset($_GET['alerta'])): ?>
            Swal.fire(
                "<?=$alertas[$_GET['alerta']]['titulo']?>",
                "<?=$alertas[$_GET['alerta']]['texto']?>",
                "<?=$alertas[$_GET['alerta']]['tipo']?>"
            );
        <?php endif; ?>

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
                        window.location.assign('listar?alerta=turma-excluida-sucesso');
                    } else {
                        Swal.fire(
                            'Erro',
                            'Não foi possível excluir a turma',
                            'error'
                        );
                    }
                    response.text().then(console.log);
                });
            }
            modalExcluir.hide();
            inputIdExcluir.value = null;
        });

        //
        // Criar turma
        //

        const formNova = document.getElementById('form-nova-turma');
        const modalNova = new bootstrap.Modal(document.getElementById('modal-nova-turma'));

        formNova.addEventListener('submit', event => {
            event.preventDefault();
            const payload = {
                nome: formNova.nome.value,
                ano:  formNova.ano.value,
            };
            fetch('criar', { method: 'POST', body: JSON.stringify(payload) })
            .then(response => {
                if (response.status == 201) {
                    window.location.assign('listar?alerta=turma-criada-sucesso')
                } else {
                    Swal.fire(
                        'Erro',
                        'Não foi possível criar a turma',
                        'error'
                    );
                }
                response.text().then(console.log);
            });
            modalNova.hide();
        });

        //
        // Editar turma
        //

        const formEditar = document.getElementById('form-editar-turma');
        const modalEditar = new bootstrap.Modal(document.getElementById('modal-editar-turma'));

        const inputIdEditar   = formEditar.querySelector('[name=id]');
        const inputNomeEditar = formEditar.querySelector('[name=nome]');
        const inputAnoEditar  = formEditar.querySelector('[name=ano]');

        for (const btnEditar of document.querySelectorAll('.btn-editar-turma')) {
            btnEditar.addEventListener('click', () => {
                console.log('ok');
                inputIdEditar.value   = btnEditar.dataset.id;
                inputNomeEditar.value = btnEditar.dataset.nome;
                inputAnoEditar.value  = btnEditar.dataset.ano;
                modalEditar.show();
            });
        }

        formEditar.addEventListener('submit', event => {
            event.preventDefault();
            const payload = {
                id:   formEditar.id.value,
                nome: formEditar.nome.value,
                ano:  formEditar.ano.value
            };
            fetch('editar', { method: 'POST', body: JSON.stringify(payload) })
            .then(response => {
                if (response.status == 200) {
                    window.location.assign('listar?alerta=turma-editada-sucesso');
                } else {
                    Swal.fire(
                        'Erro',
                        'Não foi possível editar a turma',
                        'error'
                    );
                }
                response.text().then(console.log);
            });
            modalEditar.hide();
        });

    </script>


</body>
</html>

