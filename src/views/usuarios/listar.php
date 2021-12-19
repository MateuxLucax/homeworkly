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
                        <td><?=$usuario->getId()?></td>
                        <td><?=$usuario->getNome()?></td>
                        <td><?=ucfirst($usuario->getTipo())?></td>
                        <td><?=$usuario->getLogin()?></td>
                        <td style="width: 0;">
                            <div class="d-flex justify-content-end">
                                <button type="button"
                                        class="btn btn-primary btn-editar-usuario me-4"
                                        title="Editar usuário"
                                        data-id="<?=$usuario->getId()?>"
                                        data-nome="<?=$usuario->getNome()?>"
                                        data-login="<?=$usuario->getLogin()?>"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <span
                                    <?php if (!$usuario->podeExcluir()):
                                        $motivo = match ($usuario->getTipo()) {
                                            TipoUsuario::PROFESSOR     => 'é professor de alguma disciplina',
                                            TipoUsuario::ALUNO         => 'é aluno em alguma turma',
                                            TipoUsuario::ADMINISTRADOR => 'é um administrador do sistema'
                                        }; ?>
                                        data-bs-toggle="tooltip"
                                        title="O usuário não pode ser excluído porque <?= $motivo ?>"
                                    <?php endif; ?>
                                >
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-excluir-usuario"
                                        <?php if (!$usuario->podeExcluir()) echo 'disabled'; ?>
                                        data-id-usuario="<?=$usuario->getId()?>"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </span>
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
                                <i class="fas fa-user-plus"></i> Adicionar usuário
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
                        <div class="mb-3">
                            <button
                                id="btn-abrir-modal-alterar-senha"
                                type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-dismiss="modal"
                                data-bs-target="#modal-alterar-senha"
                            >
                                <i class="fas fa-lock"></i>
                                Alterar senha
                            </button>
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

    <div class="modal fade" id="modal-excluir-usuario">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <input type="hidden" name="id-usuario">
                    <p>Tem certeza que deseja excluir este usuário?</p>
                    <button class="btn btn-danger" id="btn-confirmar-exclusao">Excluir</button>
                    <button class="btn btn-secondary" id="btn-cancelar-exclusao">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-alterar-senha">
        <!-- TODO botão de voltar pro modal anterior (só fazer um data-bs-toggle=modal data-bs-dismiss=motal data-bs-target=modal-editar-usuario, acho)-->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Alterar senha</h5>
                </div>
                <form id="form-alterar-senha">
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Usuário</label>
                            <input readonly type="text" id="nome" name="nome" class="form-control form-control-static">
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Nova senha</label>
                            <!-- TODO usar aquele botão de mostrar/esconder senha aqui -->
                            <input type="password" class="form-control" name="senha">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Alterar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-cancelar-alterar-senha">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        for (const t of document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            new bootstrap.Tooltip(t);

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
                fetch('excluir', { method: 'DELETE', body: JSON.stringify({ id: idUsuario }) })
                .then(response => {
                    if (response.status == 200) {
                        agendarAlertaSwal({
                            icon: 'success',
                            text: 'O usuário foi excluído com sucesso'
                        });
                        window.location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: 'Não foi possível excluir o usuário'
                        });
                    }
                    response.text().then(console.log);
                });
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
                    agendarAlertaSwal({
                        icon: 'success',
                        text: 'O usuário foi criado com sucesso'
                    });
                    window.location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: 'Não foi possível criar o usuário'
                    });
                }
                response.text().then(console.log);
            });
            modalNovoUsuario.hide();
        });

        //
        // Editar usuário
        //

        const formEditar  = document.getElementById('form-editar-usuario');
        const modalEditar = new bootstrap.Modal(document.getElementById('modal-editar-usuario'));

        const formAlterarSenha = document.getElementById('form-alterar-senha');

        for (const btnEditar of document.getElementsByClassName('btn-editar-usuario')) {
            btnEditar.addEventListener('click', () => {
                formEditar.querySelector('[name=id]').value    = btnEditar.dataset.id;
                formEditar.querySelector('[name=nome').value   = btnEditar.dataset.nome;
                formEditar.querySelector('[name=login]').value = btnEditar.dataset.login;
                formAlterarSenha.querySelector('[name=id]').value = btnEditar.dataset.id;
                formAlterarSenha.querySelector('[name=nome]').value = btnEditar.dataset.nome;  // Só pra mostrar
                modalEditar.show();
            });
        }

        formEditar.addEventListener('submit', event => {
            event.preventDefault();
            const payload = {
                id:    formEditar.id.value,
                nome:  formEditar.nome.value,
                login: formEditar.login.value,
            };
            fetch('alterar', {method: 'PUT', body: JSON.stringify(payload)})
            .then(response => {
                if (response.status == 200) {
                    agendarAlertaSwal({
                        icon: 'success',
                        text: 'O usuário foi alterado com sucesso'
                    });
                    window.location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: 'Não foi possível alterar o usuário'
                    });
                }
                response.text().then(console.log);
            });
            modalEditar.hide();
        });

        //
        // Editar usuário >> Alterar senha
        //
        

        formAlterarSenha.onsubmit = event => {
            event.preventDefault();
            const body = {
                id:    formAlterarSenha.id.value,
                senha: formAlterarSenha.senha.value
            };
            // Como o modal foi aberto pelo data-bs-toggle, não dá pra fechar com .hide(), só pelo botão com data-bs-dismiss
            document.getElementById('btn-cancelar-alterar-senha').click();
            fetch('alterar-senha', {method: 'PUT', body: JSON.stringify(body)})
            .then(resp => {
                if (resp.status == 200) {
                    Swal.fire({
                        icon: 'success',
                        text: 'Senha alterada com sucesso'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: 'Não foi possível alterar a senha deste usuário'
                    });
                }
                return resp.text()
            }).then(console.log);
        }

    </script>


</body>
</html>