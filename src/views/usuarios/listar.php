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
                </tr>
                <?php foreach ($view['usuarios'] as $usuario): ?>
                    <tr>
                        <td><?=$usuario['id']?></td>
                        <td><?=$usuario['nome']?></td>
                        <td><?=ucfirst($usuario['tipo'])?></td>
                        <td><?=$usuario['login']?></td>
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

                <!-- TODO usar fetch em vez do comportamento padrão do form 
                     aí o criar.php retorna uma responsta em json,
                     talvez até com uma mensagem tipo "usuário criado com sucesso" ou "erro ao criar usuário" -- nesse segundo caso sem recarregar a página -->

                <form action="criar" method="POST">
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
                        <button type="button" class="btn btn-secondary" data-bs-dmismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>