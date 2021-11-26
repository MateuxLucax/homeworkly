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
                    <th>Tipo</th>
                    <th>Login</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($view['usuarios'])): ?>
                <tr>
                    <td colspan="5" class="text-center">
                        Nenhum usuário encontrado
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($view['usuarios'] as $usuario): ?>
                    <tr class="align-middle">
                        <td><?=$usuario['id']?></td>
                        <td><?=$usuario['nome']?></td>
                        <td><?=ucfirst($usuario['tipo'])?></td>
                        <td><?=$usuario['login']?></td>
                        <td style="width: 0;">
                            <div class="d-flex justify-content-end">
                                <button type="button"
                                        class="btn btn-primary btn-editar-usuario me-4"
                                        title="Editar usuário"
                                        data-id="<?=$usuario['id']?>"
                                        data-nome="<?=$usuario['nome']?>"
                                        data-login="<?=$usuario['login']?>"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-danger btn-excluir-usuario"
                                        title="Remover usuário"
                                        data-id-usuario="<?=$usuario['id']?>"
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
                                    data-bs-target="#modal-novo-usuario"
                            >
                                <i class="bi bi-person-plus-fill"></i> Adicionar usuário
                            </button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>

    </main>

    <div class="modal fade" id="modal-novo-usuario">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo usuário</h5>
                </div>

                <form id="form-novo-usuario">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select name="tipo" id="tipo" class="form-control">
                                <option value="administrador">Administrador</option>
                                <option value="professor">Professor</option>
                                <option value="aluno">Aluno</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="login" class="form-label">Login</label>
                            <input type="text" name="login" id="login" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" name="senha" id="senha" class="form-control">
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

    <div class="modal fade" id="modal-editar-usuario">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar usuário</h5>
                </div>

                <form id="form-editar-usuario">

                    <input type="hidden" name="id">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="login" class="form-label">Login</label>
                            <input type="text" name="login" id="login" class="form-control">
                        </div>
                    </div>

                    <!-- TODO botão para alterar senha que abre um outro modal pra fazer isso
                         https://getbootstrap.com/docs/5.0/components/modal/#toggle-between-modals -->
                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Editar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-excluir-usuario">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" name="id-usuario">
                    <p>Tem certeza que deseja excluir este usuário?</p>
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
                'usuario-excluido-sucesso' => [
                    'tipo'   => 'success',
                    'titulo' => 'Excluído',
                    'texto'  => 'Usuário excluído com sucesso'
                ],
                'usuario-criado-sucesso' => [
                    'tipo'   => 'success',
                    'titulo' => 'Criado',
                    'texto'  => 'Usuário criado com sucesso'
                ],
                'usuario-editado-sucesso' => [
                    'tipo'   => 'success',
                    'titulo' => 'Editado',
                    'texto'  => 'Usuário editado com sucesso'
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
        // Deletar usuário
        //

        const elemModalExcluir    = document.querySelector('#modal-excluir-usuario');
        const modalExcluirUsuario = new bootstrap.Modal(elemModalExcluir);

        const excluirInputId   = elemModalExcluir.querySelector('[name=id-usuario]');

        for (const btnExcluir of document.querySelectorAll('.btn-excluir-usuario')) {
            btnExcluir.addEventListener('click', () => {
                excluirInputId.value   = btnExcluir.dataset.idUsuario;
                modalExcluirUsuario.show();
            });
        }

        document.querySelector('#btn-cancelar-exclusao').addEventListener('click', () => {
            modalExcluirUsuario.hide();
            excluirInputId.value   = null;
        });

        document.querySelector('#btn-confirmar-exclusao').addEventListener('click', () => {
            const idUsuario   = excluirInputId.value;
            if (idUsuario) {
                fetch('excluir', { method: 'POST', body: JSON.stringify({ id: idUsuario }) })
                .then(response => {
                    if (response.status == 200) {
                        window.location.assign('listar?alerta=usuario-excluido-sucesso');
                    } else {
                        Swal.fire(
                            'Erro',
                            `Não foi possível excluir o usuário`,
                            'error'
                        );
                    }
                    return response.json();
                }).then(console.log);
            }
            modalExcluirUsuario.hide();
            excluirInputId.value   = null;
        });

        //
        // Criar usuário
        //

        // TODO evento keydown no campo login (com debounce) pra verificar se o login já está o uso
        //   e, se for o caso, avisar o usuário e bloquear o botão "Criar"
        //   (nvdd o ideal seria deixar esse botão bloqueado e só habilitar quando os campos estiverem ok --
        //    login não está em uso, senha é forte o suficiente etc.)

        const formNovoUsuario = document.getElementById('form-novo-usuario');
        const modalNovoUsuario = new bootstrap.Modal(document.getElementById('modal-novo-usuario'));

        formNovoUsuario.addEventListener('submit', event => {
            event.preventDefault();
            const data = {
                nome:  formNovoUsuario.nome.value,
                tipo:  formNovoUsuario.tipo.value,
                login: formNovoUsuario.login.value,
                senha: formNovoUsuario.senha.value
            };
            fetch('criar', { method: 'POST', body: JSON.stringify(data) })
            .then(response => {
                if (response.status == 201) {
                    window.location.assign('listar?alerta=usuario-criado-sucesso');
                } else {
                    Swal.fire(
                        'Erro',
                        'Não foi possível criar o usuário',
                        'error'
                    );
                }
                return response.json();
            }).then(console.log);
            modalNovoUsuario.hide();
        });

        //
        // Editar usuário
        //

        const formEditarUsuario  = document.getElementById('form-editar-usuario');
        const modalEditarUsuario = new bootstrap.Modal(document.getElementById('modal-editar-usuario'));
        const editarInputId    = formEditarUsuario.querySelector('[name=id]');
        const editarInputNome  = formEditarUsuario.querySelector('[name=nome]');
        const editarInputLogin = formEditarUsuario.querySelector('[name=login]');

        for (const btnEditar of document.getElementsByClassName('btn-editar-usuario')) {
            btnEditar.addEventListener('click', () => {
                editarInputId.value    = btnEditar.dataset.id;
                editarInputNome.value  = btnEditar.dataset.nome;
                editarInputLogin.value = btnEditar.dataset.login;
                modalEditarUsuario.show();
            });
        }

        formEditarUsuario.addEventListener('submit', event => {
            event.preventDefault();
            const payload = {
                id:    formEditarUsuario.id.value,
                nome:  formEditarUsuario.nome.value,
                login: formEditarUsuario.login.value,
            };
            fetch('editar', {method: 'PUT', body: JSON.stringify(payload)})
            .then(response => {
                if (response.status == 200) {
                    window.location.assign('listar?alerta=usuario-editado-sucesso');
                } else {
                    Swal.fire(
                        'Erro',
                        'Não foi possível editar o usuário',
                        'error'
                    );
                }
                return response.json();
            }).then(console.log);
            modalEditarUsuario.hide();
        });
    </script>


</body>
</html>