<!DOCTYPE html>
<html lang="en">
<?php require $root.'views/componentes/head.php' ?>

<body>
    <?php $turma = $view['turma'] ?>

    <header>
        <?php require_once $root.'/views/componentes/navbar-admin.php'; ?>
    </header>

    <main class="container">

        <div class="header mt-3 mb-3 d-flex align-items-center">
            <nav>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="listar?ano=<?=$turma->getAno()?>">
                            <?= $turma->getAno() ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= $turma->getNome() ?>
                        <small>(ID <?=$turma->getId()?>)</small>
                    </li>
                </ol>
            </nav>

            <span
                class="ms-auto"
                <?php if (!$turma->podeAlterar()): ?>
                    data-bs-toggle="tooltip"
                    title='Essa turma não pode ser alterada porque está arquivada (é de um ano passado)'
                <?php endif; ?>
            >
                <button
                    class="btn btn-primary"
                    <?= $turma->podeAlterar() ? 'onclick=\'window.location.assign("alterar?id=' . $turma->getId() . '")\'' : 'disabled' ?>
                >
                    <i class="fas fa-edit"></i>
                </button>
            </span>
        </div>
    
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
                    <?php if (count($turma->getAlunos()) == 0): ?>
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
                                <?php foreach ($turma->getAlunos() as $aluno): ?>
                                    <tr>
                                        <td><?=$aluno->getId()?></td>
                                        <td><?=$aluno->getNome()?></td>
                                        <td><?=$aluno->getLogin()?></td>
                                        <td><?=$aluno->getUltimoAcesso() === null 
                                            ? 'Ainda não acessou o sistema'
                                            : date('Y-m-d H:i', $aluno->getUltimoAcesso())?>
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
                    <?php if (count($turma->getDisciplinas()) == 0): ?>
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
                                <?php foreach ($turma->getDisciplinas() as $disciplina): ?>
                                    <tr>
                                        <td><?=$disciplina->getId()?></td>
                                        <td><?=$disciplina->getNome()?></td>
                                        <td>
                                            <!-- TODO adicionar link em cada nome -->
                                            <?php
                                                $nomesProfessores = [];
                                                foreach ($disciplina->getProfessores() as $professor) {
                                                    $nomesProfessores[] = $professor->getNome();
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
    <script>
        for (const t of document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            new bootstrap.Tooltip(t);
    </script>

</body>

</html>