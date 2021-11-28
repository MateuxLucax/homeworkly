<!DOCTYPE html>
<html lang="en">
<?php require $root.'views/componentes/head.php' ?>

<body>

    <!-- TODO botões pro administrador poder editar -- adicionar alunos, disciplinas (criar tb) etc. -->

    <main class="container">

        <!-- TODO deve ter uma forma mais bonita de mostrar todas essas informações na mesma linha -->

        <h1 class="mt-3 mb-3">
            <?=$view['ano']?> / <b><?=$view['nome']?></b> <small class="text-muted">#<?=$view['id']?></small>
        </h1>
    
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-alunos" data-bs-toggle="tab" data-bs-target="#alunos" type="button" role="tab" aria-controls="alunos" aria-selected="true">
                    Alunos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-disciplinas" data-bs-toggle="tab" data-bs-target="#disciplinas" type="button" role="tab" aria-controls="disciplinas" aria-selected="false">
                    Disciplinas
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="alunos" role="tabpanel" aria-labelledby="tab-alunos">

                <div class="mt-3">
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

            <div class="tab-pane fade" id="disciplinas" role="tabpanel" aria-labelledby="tab-disciplinas">

                <div class="mt-3">
                    <?php if (count($view['disciplinas']) == 0): ?>
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
        </div>


    </main>

</body>
</html>