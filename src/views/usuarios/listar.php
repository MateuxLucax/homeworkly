<html>
<?php require $root.'/views/componentes/head.php'; ?>

<body>

    <main class="container">

        <?php if (count($view['usuarios']) == 0): ?>
            <h2>Nenhum usuário encontrado</h2>
        <?php else: ?>
            <table class="table">
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
            </table>
        <?php endif; ?>

    </main>

</body>
</html>