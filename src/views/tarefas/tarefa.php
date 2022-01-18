<!DOCTYPE html>
<html>
<?php require_once $root . '/views/componentes/head.php'; ?>
<body>

<!-- TODO estado da tarefa em relação ao aluno: pendente; entregue -->
<!-- mostrar que nem o estado da tarefa -->

<?php
    $tarefa = $view['tarefa'];
    $disciplina = $tarefa->disciplina();
    $turma = $disciplina->getTurma();
    $professor = $tarefa->professor();

    $permissao = $view['permissao'];
?>

<main class="container">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/<?=TipoUsuario::pasta($_SESSION['tipo'])?>/turmas/listar?ano=<?=$turma->getAno()?>">
                    <?= $turma->getAno() ?>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="/<?=TipoUsuario::pasta($_SESSION['tipo'])?>/turmas/turma?id=<?= $turma->getId() ?>">
                    <?= $turma->getNome() ?>
                </a>
            </li>
            <li class="breadcrumb-item">
                <!-- TODO colocar link para quando página da disciplina for criada -->
                <?= $disciplina->getNome() ?>
            </li>
        </ol>
    </nav>
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center">
            Tarefa
            &nbsp;
            <small>(ID <?= $tarefa->id()?>)</small>
            <?php
                $permissaoAlterar = $permissao->alterar($_SESSION['id_usuario'], $_SESSION['tipo']);
                $mostrarBotao = $permissaoAlterar != PermissaoTarefa::NAO_AUTORIZADO;
                $desabilitarBotao = $permissaoAlterar != PermissaoTarefa::PODE;
                $desabilitarMotivo = match ($permissaoAlterar) {
                    PermissaoTarefa::ARQUIVADA => 'é de um ano passado e está arquivada',
                    PermissaoTarefa::FECHADA   => 'já foi fechada',
                    default                    => '[cód. '.$permissaoAlterar.']'
                };

                if ($mostrarBotao): ?>
                    <div class="d-inline ms-auto"
                         <?= $desabilitarBotao ? 'data-bs-toggle="tooltip" title="A tarefa não pode ser alterada pois '.$desabilitarMotivo.'."' : '' ?>
                    >
                        <span class="ms-auto">
                            <button type="button" class="btn btn-primary" onclick="location.assign('alterar?id=<?= $tarefa->id() ?>')"
                                    <?= $desabilitarBotao ? 'disabled' : '' ?>
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                        </span>
                    </div>
                <?php endif; ?>

                <?php
                    $estado = $tarefa->estado();
                    $classeBgEstado = match($estado) {
                        TarefaEstado::ESPERANDO_ABERTURA => 'bg-primary',
                        TarefaEstado::ABERTA             => 'bg-success',
                        TarefaEstado::ATRASADA           => 'bg-warning',
                        TarefaEstado::FECHADA            => 'bg-dark',
                        TarefaEstado::ARQUIVADA          => 'bg-secondary'
                    };
                ?>
                <h5 class="mb-0 ms-auto">
                    <span class="badge <?= $classeBgEstado ?>">
                        <?= $estado->toString() ?>
                    </span>
                </h5>
        </div>
        <div class="card-body">
            <h5 class="card-title">
                <?= $tarefa->titulo() ?>
            </h5>
            <p><?= $tarefa->descricao() ?></p>
            <hr/>

            <?php function dataISO(DateTime $data) : string {
                return $data->format('Y-m-d\TH:i');
            } ?>

            <div class="row mb-3">
                <div class="col-12 mb-3 col-sm-6 mb-sm-0">
                    <label class="form-label" for="entrega">Data de entrega</label>
                    <input disabled readonly class="form-control" id="entrega" type="datetime-local"
                        value="<?=dataISO($tarefa->entrega())?>"/>
                </div>

                <!-- TODO deixar explícito que nenhuma data de fechamento foi informada -->

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
                $minsTotal = $tarefa->esforcoMinutos();
                $horasVal = (int) ($minsTotal / 60);
                $minsVal = sprintf('%02d', $minsTotal % 60);
            ?>
            
            <div class="row">
                <div class="col-12 mb-3 col-sm-6 mb-sm-0 col-md-4">
                    <label class="form-label" for="esforco">Esforço</label>
                    <div class="input-group">
                        <input disabled readonly class="form-control" type="text" value="<?= $horasVal ?>"/>
                        <span class="input-group-text">:</span>
                        <input disabled readonly class="form-control" type="text" value="<?= $minsVal ?>"/>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-8">
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

            <!-- TODO só mostrar em baixo assim quando a tarefa já estiver aberta.
                 Senão, mostrar junto as outras datas como campo de formulário -- a data de abertura é depois e ainda pode ser alterada -->
            <span>
                Aberta por
                <b><?= $professor->getNome() ?></b>
                em
                <i><?= $tarefa->abertura()->format('d/m/Y H:i') ?></i>
            </span>
        </div>
    </div>

    <?php
    if ($_SESSION['tipo'] == TipoUsuario::ALUNO):
        $alunoJaEntregou = $view['entrega'] != null;
        $tarefaPermiteEntrega = $estado == TarefaEstado::ABERTA || $estado == TarefaEstado::ATRASADA; ?>

        <div class="card">
            <div class="card-header d-flex align-items-center">
                Entrega
            </div>
            <div class="card-body">
                <?php
                if ($alunoJaEntregou || $tarefaPermiteEntrega):
                    if ($alunoJaEntregou) {
                        $formAction = 'alterar?id='.$view['entrega']['id'];
                        $conteudoEntrega = $view['entrega']['conteudo'];
                        $iconeBotao = 'fa-edit';
                        $textoBotao = 'Alterar';
                    } else {
                        $formAction = 'criar?tarefa='.$tarefa->id();
                        $conteudoEntrega = '';
                        $iconeBotao = 'fa-paper-plane';
                        $textoBotao = 'Salvar';
                    }

                    if (!$tarefaPermiteEntrega): ?>
                        <div class="alert alert-warning">
                            Você não pode mais alterar a entrega.
                        </div>
                    <?php endif;
                    ?>

                    <form action="/aluno/entregas/<?= $formAction ?>">
                        <textarea
                            class="mb-0 form-control" name="conteudo-entrega" id="conteudo-entrega" rows="3" required
                            <?= $alunoJaEntregou && !$tarefaPermiteEntrega ? 'disabled readonly' : ''?>
                        ><?= $conteudoEntrega ?></textarea>
                        <?php if ($tarefaPermiteEntrega): ?>
                            <div class="float-end">
                                <button type="button" class="mt-3 btn btn-primary">
                                    <i class="fas <?= $iconeBotao ?>"></i>
                                    <?= $textoBotao ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <div class="mt-3 mb-0 alert alert-danger">
                        Você não pode mais realizar a entrega desta tarefa.
                    </div>
                <?php endif; ?>

                <?php if ($estado == TarefaEstado::FECHADA || $estado == TarefaEstado::ARQUIVADA): ?>
                    <hr/>
                    <div class="d-flex align-items-center">
                        <div>Avaliação do professor</div>
                        <?php if ($tarefa->comNota()) {
                            $nota = $view['entrega']['nota'];
                            $textoAvaliacao = ($nota ?? '?') .'/10';
                            $bgAvaliacao = !$nota ? 'bg-secondary' : ($nota < 7 ? 'bg-warning' : 'bg-success');
                        } else {
                            // TODO alterar visto para 3 valores (no BD, mas refletir aqui): visto, não visto / pendente (somente visível pro professor?) e não aceito
                            if ($view['entrega']['visto'] === null) {
                                $textoAvaliacao = 'Não visto';
                                $bgAvaliacao = 'bg-secondary';
                            } else if ($view['entrega']['visto']) {
                                $textoAvaliacao = 'Visto';
                                $bgAvaliacao = 'bg-success';
                            } else {
                                $textoAvaliacao = 'Não aceito';
                                $bgAvaliacao = 'bg-danger';
                            }
                        } ?>
                        <h4 class="mb-0" style="margin-left: 15px;">
                            <span class="badge <?= $bgAvaliacao ?>">
                                <?= $textoAvaliacao ?>
                            </span>
                        </h4>
                    </div>

                    <?php if (!empty($view['entrega']['comentario'])): ?>
                        <label class="form-label mt-3" for="entrega-comentario"Comentário>Comentário</label>
                        <textarea disabled readonly id="entrega-comentario" class="form-control" rows="3"
                        ><?= $view['entrega']['comentario'] ?></textarea>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>
</main>

<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>

</body>
</html>