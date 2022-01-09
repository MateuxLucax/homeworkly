<!DOCTYPE html>
<html>
<?php require_once $root . '/views/componentes/head.php'; ?>
<body>

<!-- TODO estado da tarefa: esperando abertura; aberta; atrasada; fechada -->
<!-- TODO estado da tarefa em relação ao aluno: pendente; entregue -->
<!-- mostrar com uma badge ou algo assim colorido no card-header -->
<!-- Mas essa lógica ficará no modelo da tarefa, acessível por métodos retornando enums -->

<!-- TODO botão de alterar, mas antes tela para alterar tarefa existente... -->

<?php
    $tarefa = $view['tarefa'];
    $disciplina = $tarefa->disciplina();
    $turma = $disciplina->getTurma();
    $professor = $tarefa->professor();
?>

<main class="container">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <?= $turma->getAno() ?>
            </li>
            <li class="breadcrumb-item">
                <a href="/<?=$view['pasta_usuario']?>/turmas/turma?id=<?= $turma->getId() ?>">
                    <?= $turma->getNome() ?>
                </a>
            </li>
            <li class="breadcrumb-item">
                <!-- TODO colocar link para quando página da disciplina for criada -->
                <?= $disciplina->getNome() ?>
            </li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-header">
            Tarefa
        </div>
        <div class="card-body">
            <h5 class="card-title"><?= $tarefa->titulo() ?></h5>
            <p><?= $tarefa->descricao() ?></p>
            <hr/>

            <?php function dataISO(DateTime $data) : string {
                return $data->format('Y-m-d\TH:i');
            } ?>

            <div class="row mb-3">
                <div class="col-sm-6">
                    <label class="form-label" for="entrega">Data de entrega</label>
                    <input disabled readonly class="form-control" id="entrega" type="datetime-local"
                        value="<?=dataISO($tarefa->entrega())?>"/>
                </div>
                <div class="col-sm-6">
                    <?php if (!is_null($tarefa->fechamento())): ?>
                        <label class="form-label" for="fechamento">Data de fechamento</label>
                        <input disabled readonly class="form-control" id="fechamento" type="datetime-local"
                            value="<?=dataISO($tarefa->fechamento())?>"/>
                    <?php else: ?>
                        <label class="form-label" for="fechada">Fechada manualmente</label>
                        <input disabled readonly class="form-control" id="fechamento" type="text"
                            value="<?=$tarefa->fechadaManualmente() ? 'Sim' : 'Não'?>"/>
                    <?php endif; ?>
                </div>
            </div>

            <?php
                $esforcoMinutos = $tarefa->esforcoMinutos();
                $esforcoValue = sprintf("%02d:%02d", (int) ($esforcoMinutos / 60), $esforcoMinutos % 60);
            ?>
            
            <div class="row">
                <div class="col-sm-6">
                    <label class="form-label" for="esforco">Estimativa de esforço</label>
                    <input disabled readonly class="form-control" type="time" value="<?=$esforcoValue?>"/>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="comNota">Avaliação</label>
                    <br/>
                    <div class="form-check form-check-inline">
                        <input disabled class="form-check-input" type="radio" name="comNota" value="1" id="avaliacao-nota" 
                               <?= $tarefa->comNota() ? 'checked' : '' ?> />
                        <label class="form-check-label" for="avaliacao-nota">Nota</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input disabled class="form-check-input" type="radio" name="comNota" value="0" id="avaliacao-visto"
                               <?= $tarefa->comNota() ? '' : 'checked' ?> />
                        <label class="form-check-label" for="avaliacao-nota">Visto</label>
                    </div>
                </div>
            </div>
            <hr/>

            <!-- TODO link para o perfil do professor quando páginas de perfil forem criadas -->
            <!-- TODO uma imagem de perfil circular ficaria bonita aqui... -->
            <span>
                Aberta por
                <b><?= $professor->getNome() ?></b>
                em
                <i><?= $tarefa->abertura()->format('d/m/Y H:i') ?></i>
            </span>
        </div>
    </div>
</main>

</body>
</html>