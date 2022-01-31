<?php require_once $root . 'utils/DateUtil.php'; ?>

<div class="d-grid gap-4 align-items-center mt-4 px-0" style="grid-template-columns: repeat(3, 1fr);">
    <?php foreach ($view['tarefas'] as $tarefa) : ?>
        <div class="card h-100 bg-light">
            <div class="card-body d-flex flex-column h-100">
                <div>
                    <h5 class="pb-2"><?= $tarefa->disciplina()->getNome() ?> - <?= $tarefa->titulo() ?></h5>
                    <small>
                        <p><i class="far fa-calendar-minus me-2"></i><?= DateUtil::formatTo($tarefa->dataHoraAbertura(), 'd/m/Y') ?> - <?= DateUtil::formatTo($tarefa->dataHoraEntrega(), 'd/m/Y') ?></p>
                    </small>
                    <p><?= $tarefa->descricao() ?></p>
                </div>
                <a href="tarefa?id=<?= $tarefa->id() ?>" type="button" class="btn btn-outline-dark mt-auto">Responder tarefa<i class="far fa-edit ms-2"></i></a>
            </div>
        </div>
    <?php endforeach ?>
</div>