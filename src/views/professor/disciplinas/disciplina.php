<?php
    $turma = $disciplina->getTurma();
    $disciplina = $view['disciplina'];
    $tarefasPorEstado = $view['tarefasPorEstado'];
?>

<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <?=$turma->getAno()?>
    </li>
    <li class="breadcrumb-item">
        <?=$turma->getNome()?>
    </li>
    <li class="breadcrumb-item active">
        <?=$disciplina->getNome()?>
    </li>
</ol>

<div class="card px-0 mb-3">
    <div class="card-header">Professor(es)</div>
    <div class="card-body pb-0">
        <ul>
            <?php foreach ($disciplina->getProfessores() as $prof) {
                echo '<li>'.$prof->getNome().'</li>';
            } ?>
        </ul>
    </div>
</div>

<style>
    .card-tarefa:hover {
        filter: brightness(90%);
    }
</style>

<?php
$estadoToString = [
    'esperando_abertura' => 'Esperando abertura',
    'aberta' => 'Abertas',
    'fechada' => 'Fechadas'
]; ?>

<div class="card px-0 mb-3">
    <div class="card-header d-flex align-items-center">
        <span>Tarefas</span>
        <a class="ms-auto btn btn-success" href="/professor/tarefas/criar?disciplina=<?=$disciplina->getId()?>">
            <i class="fas fa-plus-circle"></i>&nbsp;
            Criar
        </a>

    </div>
    <div class="card-body">

        <ul class="nav nav-pills nav-fill mb-3">
            <?php
            $active = 'active';
            foreach ($tarefasPorEstado as $estado => $_) {
                echo '<li class="nav-item">
                    <button class="nav-link '.$active.'" data-bs-toggle="pill" data-bs-target="#tarefas-'.$estado.'">
                        '.$estadoToString[$estado].'
                    </button>
                </li>';
                $active = '';
            } ?>
        </ul>

        <div class="tab-content">
            <?php
            $active = 'show active';
            foreach ($tarefasPorEstado as $estado => $tarefas): ?>

                <div class="tab-pane <?=$active?>" id="tarefas-<?=$estado?>">

                    <?php if (count($tarefas) == 0) {
                        echo '<div class="alert alert-success mb-0">
                            Não há tarefas '.strtolower($estadoToString[$estado]).' suas nessa disciplina
                        </div>';
                    } ?>

                    <div class="d-grid gap-4 align-items-center px-0"
                         style="grid-template-columns: repeat(3, 1fr);">
                        <?php foreach ($tarefas as $tarefa): ?>

                            <div class="card-tarefa card h-100 bg-light"
                                 style="cursor: pointer;"
                                 onclick="location.assign('/professor/tarefas/tarefa?id=<?= $tarefa->id() ?>')"
                            >
                                <div class="card-body d-flex flex-column h-100">
                                    <div>
                                        <h5 class="pb-2">
                                            <?= $tarefa->titulo() ?>
                                        </h5>
                                        <p class="mb-0">
                                            <i class="far fa-calendar-minus me-2"></i>
                                            <?= $tarefa->dataHoraAbertura()->format('d/m/Y') ?>
                                            -
                                            <?= $tarefa->dataHoraEntrega()->format('d/m/Y') ?>
                                        </p>
                                        <p class="mt-2 mb-0">
                                            <?= $tarefa->descricao() ?>
                                        </p>

                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>

            <?php $active = '';
            endforeach; ?>

        </div>

    </div>
</div>
