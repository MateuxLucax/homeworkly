<!DOCTYPE html>
<html lang="en">
<?php require $root.'views/componentes/head.php' ?>

<body>

    <!-- TODO botões pro administrador poder editar -- adicionar alunos, disciplinas (criar tb) etc. -->

    <main class="container">
    
        <!-- TODO layout mais bonito, não só um <form> -->
        <div class="card mt-3">
            <div class="card-header">
                <h4>Turma</h4>
            </div>
            <div class="card-body pb-0">
                <form>
                    <div class="mb-3 row">
                        <label for="turma-id" class="col-form-label col-sm-2">ID</label>
                        <div class="col-sm-10">
                            <input readonly id="turma-id" type="number" class="form-control" value="<?=$view['id']?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="turma-nome" class="col-form-label col-sm-2">Nome</label>
                        <div class="col-sm-10">
                            <input readonly id="turma-nome" type="text" class="form-control" value="<?=$view['nome']?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="turma-ano" class="col-form-label col-sm-2">Ano</label>
                        <div class="col-sm-10">
                            <input readonly id="turma-ano" type="number" class="form-control" value="<?=$view['ano']?>">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- TODO colocar cada painel em ABAS,
            e deixar a aba de TAREFAS (ainda não feita) como a principal -->

        <!-- De fato, essa página provavelmente vai ser a página principal do site -->


        <div class="card mt-3">
            <div class="card-header">
                <h4>Alunos</h4>
            </div>
            <div class="card-body pb-0">
                <?php if (count($view['alunos']) == 0): ?>
                    <div class="alert alert-warning">
                        Não há alunos nessa turma
                    </div>
                <?php else: ?>
                    <table class="table table-striped table-hover">
                        <thead>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Login</th>
                            <th>Último acesso</th>
                        </thead>
                        <tbody>
                            <?php foreach ($view['alunos'] as $aluno): ?>
                                <tr>
                                    <td><?=$aluno['id']?></td>
                                    <td><?=$aluno['nome']?></td>
                                    <td><?=$aluno['login']?></td>
                                    <td><?=$aluno['ultimo_acesso'] === null 
                                         ? 'Ainda não acessou o sistema'
                                         : date('Y-m-d H:i', $aluno['ultimo_acesso'])?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4>Disciplinas</h4>
            </div>
            <div class="card-body pb-0">
                <?php if (count($view['alunos']) == 0): ?>
                    <div class="alert alert-warning">
                        Não há disciplinas nessa turma
                    </div>
                <?php else: ?>
                    <table class="table table-striped table-hover">
                        <thead>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Professor(es)</th>
                        </thead>
                        <tbody>
                            <?php foreach ($view['disciplinas'] as $disciplina): ?>
                                <tr>
                                    <td><?=$disciplina['id']?></td>
                                    <td><?=$disciplina['nome']?></td>
                                    <td>
                                        <!-- TODO adicionar link em cada nome -->
                                        <?php
                                            $nomesProfessores = [];
                                            foreach ($disciplina['professores'] as $professor) {
                                                $nomesProfessores[] = $professor['nome'];
                                            }
                                        ?>
                                        <?= implode(', ', $nomesProfessores) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </main>

</body>
</html>