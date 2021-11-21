<html lang="pt-BR">
<?php require $root.'/views/componentes/head.php'; ?>

<body>

    <main class="container">

        <table class="table">
            <?php if ($view['pode-modificar-usuarios']): ?>
                <tr colspan="4" class="text-center">
                    <button type="button" class="btn btn-success"
                            data-bs-toggle="modal" data-bs-target="#modal-novo-usuario">Novo usuário</button>
                </tr>
            <?php endif; ?>
        <?php if (count($view['usuarios']) == 0): ?>
            <tr colspan="4" class="text-center">
                Nenhum usuário encontrado
            </tr>
        <?php else: ?>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Login</th>
                    <th></th>
                </tr>
                <?php foreach ($view['usuarios'] as $usuario): ?>
                    <tr>
                        <td><?=$usuario['id']?></td>
                        <td><?=$usuario['nome']?></td>
                        <td><?=ucfirst($usuario['tipo'])?></td>
                        <td><?=$usuario['login']?></td>
                        <td><button type="button" class="btn btn-link btn-excluir-usuario"
                                    data-id-usuario="<?=$usuario['id']?>">Excluir</button></td>
                    </tr>
                <?php endforeach; ?>
        <?php endif; ?>
        </table>

    </main>

    <div class="modal fade" id="modal-novo-usuario">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo usuário</h5>
                </div>

                <form id="form-novo-usuario" action="criar" method="POST">
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

        // TODO refatorar
        // - criar modal com createDocument pra poder ser criado um para cada clique
        // - colocar dentro de uma função

        const elemModalExcluir = document.querySelector('#modal-excluir-usuario');
        const modalExcluir     = new bootstrap.Modal(elemModalExcluir);
        const inputIdUsuario   = document.querySelector('[name=id-usuario]');

        for (const btnExcluir of document.querySelectorAll('.btn-excluir-usuario')) {
            btnExcluir.addEventListener('click', () => {
                inputIdUsuario.value = btnExcluir.dataset.idUsuario;
                modalExcluir.show();
            });
        }

        const btnCancelarExclusao  = document.querySelector('#btn-cancelar-exclusao');
        const btnConfirmarExclusao = document.querySelector('#btn-confirmar-exclusao');

        document.querySelector('#btn-cancelar-exclusao').addEventListener('click', () => {
            modalExcluir.hide();
            inputIdUsuario.value = null;
        });

        document.querySelector('#btn-confirmar-exclusao').addEventListener('click', () => {
            const idUsuario = elemModalExcluir.querySelector('[name=id-usuario]').value;
            if (idUsuario) {
                const promExcluir = fetch('excluir', {method: 'POST', body: JSON.stringify({id: idUsuario})});
                const promBody = promExcluir.then(response => {
                    if (response.status == 200) {
                        window.location.reload();
                        return null;
                    } else {
                        return response.json();
                    }
                });
                promBody?.then(console.log);
            }
            modalExcluir.hide();
            inputIdUsuario.value = null;
        });





        /*
        const formUsuario = document.getElementById('form-novo-usuario');

        formUsuario.addEventListener('submit', event => {
            event.preventDefault();
            let data = {
                nome: formUsuario.nome.value,
                tipo: formUsuario.tipo.value,
                login: formUsuario.login.value,
                senha: formUsuario.senha.value,
            };
            fetch('http://localhost/usuarios/criar', {method: 'POST', body: data})
                .then(response => console.log(response));
        });
        */
    </script>


</body>
</html>