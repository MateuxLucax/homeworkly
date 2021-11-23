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
                        <td>
                            <div class="d-flex justify-content-end">
                                <button type="button"
                                        class="btn btn-danger btn-excluir-usuario"
                                        title="Remover usuário"
                                        data-id-usuario="<?=$usuario['id']?>"
                                        data-nome-usuario="<?=$usuario['nome']?>"
                                >
                                    <i class="bi bi-person-dash-fill"></i>
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
                                    class="btn btn-primary ms-auto"
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

    <div class="modal fade" id="modal-excluir-usuario">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" name="id-usuario">
                    <input type="hidden" name="nome-usuario">
                    <p>Tem certeza que deseja excluir este usuário?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="btn-confirmar-exclusao">Excluir</button>
                    <button class="btn btn-secondary" id="btn-cancelar-exclusao">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-absolute p-3 bottom-0 end-0" id="toast-container">
        <div id="toast-excluir-falhou" class="toast bg-danger text-white" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    Não foi possível excluir o usuário <span id="toast-excluir-falhou-nome"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <div id="toast-criar-falhou" class="toast bg-danger text-white" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    Não foi possível criar o usuário
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>



    <script type="text/javascript">

        //
        // Deletar usuário
        //

        // TODO investigar como alterar as exceções do xdebug
        // atualmente qdo acontece uma PDOException ele transorma em html e fica impossível de pegar o código e mostrar uma mensagem apropriada
        // tipo quando se tenta deletar um usuário que é professor de uma turma e dá uma violação de foreign key
        // seria melhor se fosse possível inspecionar o código do erro da exceção retornada como json
        // e mostrar uma mensagem tipo "Não foi possível deletar o usuário porque ..."

        const elemModalExcluir = document.querySelector('#modal-excluir-usuario');
        const modalExcluir     = new bootstrap.Modal(elemModalExcluir);

        const inputIdUsuario   = elemModalExcluir.querySelector('[name=id-usuario]');
        const inputNomeUsuario = elemModalExcluir.querySelector('[name=nome-usuario]');

        for (const btnExcluir of document.querySelectorAll('.btn-excluir-usuario')) {
            btnExcluir.addEventListener('click', () => {
                inputIdUsuario.value   = btnExcluir.dataset.idUsuario;
                inputNomeUsuario.value = btnExcluir.dataset.nomeUsuario;
                modalExcluir.show();
            });
        }

        document.querySelector('#btn-cancelar-exclusao').addEventListener('click', () => {
            modalExcluir.hide();
            inputIdUsuario.value = null;
        });

        document.querySelector('#btn-confirmar-exclusao').addEventListener('click', () => {
            const idUsuario = inputIdUsuario.value;
            if (idUsuario) {
                fetch('excluir', {method: 'POST', body: JSON.stringify({id: idUsuario})})
                .then(response => {
                    if (response.status == 200) {
                        // TODO toast com mensagem 'Usuário excluído com sucesso' na página recarregada
                        window.location.reload();
                    } else {
                        document.getElementById('toast-excluir-falhou-nome').innerText = inputNomeUsuario.value;
                        (new bootstrap.Toast(document.getElementById('toast-excluir-falhou'))).show();
                    }
                    return response.json();
                }).then(console.log);
            }
            modalExcluir.hide();
            inputIdUsuario.value = null;
        });

        //
        // Criar usuário
        //

        // TODO evento keydown no campo login (com debounce) pra verificar se o login já está o uso
        //   e, se for o caso, avisar o usuário e bloquear o botão "Criar"
        //   (nvdd o ideal seria deixar esse botão bloqueado e só habilitar quando os campos estiverem ok --
        //    login não está em uso, senha é forte o suficiente etc.)

        const formUsuario = document.getElementById('form-novo-usuario');
        const modalNovo = new bootstrap.Modal(document.getElementById('modal-novo-usuario'));

        formUsuario.addEventListener('submit', event => {
            event.preventDefault();
            const data = {
                nome:  formUsuario.nome.value,
                tipo:  formUsuario.tipo.value,
                login: formUsuario.login.value,
                senha: formUsuario.senha.value
            };
            fetch('criar', {method: 'POST', body: JSON.stringify(data)})
            .then(response => {
                if (response.status == 201) {
                    // TODO toast com mensagem 'Usuário criado com sucesso' na página recarregada
                    window.location.reload();
                } else {
                    modalNovo.hide();
                    (new bootstrap.Toast(document.getElementById('toast-criar-falhou'))).show();
                }
                return response.json();
            }).then(console.log);
        });
    </script>


</body>
</html>