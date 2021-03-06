<?php require_once $root . 'utils/DateUtil.php'; ?>

<?php
    $situacaoToString = [
        'atrasadas' => 'Atrasadas',
        'entregues' => 'Entregues',
        'pendentes' => 'Pendentes',
        'nao-feitas' => 'Não feitas'
    ]
?>

<style>
    .card-tarefa:hover {
        filter: brightness(90%);
    }
</style>

<div class="card px-0 mb-3">
    <div class="card-header">
        <div class="card-title">
            Tarefas
        </div>
    </div>
    <div class="card-body">

        <ul class="nav nav-pills nav-fill mb-3">
            <?php
            $active = 'active';
            foreach ($tarefasPorSituacao as $situacao => $_) {
                echo '<li class="nav-item">
                    <button class="nav-link '.$active.'" data-bs-toggle="pill" data-bs-target="#tarefas-'.$situacao.'">
                        '.$situacaoToString[$situacao].'
                    </button>
                </li>';
                $active = '';
            } ?>
        </ul>

        <div class="tab-content">
            <?php
            $active = 'show active';
            foreach ($tarefasPorSituacao as $situacao => $dadosTarefas): ?>

                <div class="tab-pane <?=$active?>" id="tarefas-<?=$situacao?>">

                    <?php if (count($dadosTarefas) == 0) {
                        echo '<div class="alert alert-success mb-0">
                            Não há tarefas '.strtolower($situacaoToString[$situacao]).' suas nessa disciplina
                        </div>';
                    } ?>

                    <div class="d-grid gap-4 align-items-center px-0"
                         style="grid-template-columns: repeat(3, 1fr);">
                        <?php
                        foreach ($dadosTarefas as $dadosTarefa):
                            $tarefa = $dadosTarefa['tarefa'];
                            $entrega = $dadosTarefa['entrega'];
                            $avaliacao = $dadosTarefa['avaliacao'];
                            ?>

                            <div class="card-tarefa card h-100 bg-light"
                                 style="cursor: pointer;"
                                 onclick="location.assign('tarefa?id=<?= $tarefa->id() ?>')"
                            >
                                <div class="card-body d-flex flex-column h-100">
                                    <div>
                                        <h5 class="pb-2">
                                            <?= $tarefa->titulo() ?>
                                            <?php if ($entrega != null && !$entrega->emDefinitivo()) {
                                                echo ' <i data-bs-toggle="tooltip" data-bs-placement="bottom" title="Em rascunho. Você ainda não entregou em definitivo" class="fas fa-edit"></i>';
                                            } ?>

                                            <?php if ($tarefa->fechada() && $avaliacao != null && $avaliacao->comentario() != null) {
                                                echo ' <i data-bs-toggle="tooltip" data-bs-placement="bottom" title="Professor comentou sua entrega" class="fas fa-comment-dots"></i>';
                                            } ?>
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

                                        <p class="mt-2 mb-0 text-end">
                                        <?php if ($avaliacao != null && $tarefa->fechada()) {
                                            if ($tarefa->comNota()) {
                                                $nota = $avaliacao->nota();
                                                $corTexto = $nota >= 7 ? 'text-success' : 'text-danger';
                                                echo 'Nota: <span class="'.$corTexto.'">'.$avaliacao->nota() . '/10</span>';
                                            } else {
                                                $visto = $avaliacao->visto();

                                                if ($visto === null) {
                                                    $texto = 'Não visto';
                                                    $corTexto = 'text-secondary';
                                                } else if ($visto === true) {
                                                    $texto = 'Visto';
                                                    $corTexto = 'text-success';
                                                } else {
                                                    $texto = 'Não aceito';
                                                    $corTexto = 'text-danger';
                                                }
                                                echo '<span class="'.$corTexto.'">'.$texto.'</span>';
                                            }
                                        } ?>
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

<script>
    document.querySelectorAll('[data-bs-toggle=tooltip]').forEach(t => new bootstrap.Tooltip(t));
</script>