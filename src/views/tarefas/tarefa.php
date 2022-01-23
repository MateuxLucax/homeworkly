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
            <div class="ms-auto d-flex align-items-center">

                <?php
                    // acho que mostrar 'atrasada' fica um pouco estranho
                    // TODO mostrar 'aberta' mas colocar um alert na entrega
                    // caso o usuário ainda não tenha feito a entrega
                    // de que ela ficará atrasada.

                    // TODO talvez remover esse estado ATRASADA mesmo
                    $estado = $tarefa->estado();
                    $classeBgEstado = match($estado) {
                        TarefaEstado::ESPERANDO_ABERTURA => 'bg-primary',
                        TarefaEstado::ABERTA             => 'bg-success',
                        TarefaEstado::ATRASADA           => 'bg-warning',
                        TarefaEstado::FECHADA            => 'bg-dark',
                        TarefaEstado::ARQUIVADA          => 'bg-secondary'
                    };
                    $classeTextoEstado = $classeBgEstado == 'bg-warning' ? 'text-dark' : '';
                ?>
                <h5 class="mb-0">
                    <span class="badge <?= $classeBgEstado ?> <?= $classeTextoEstado ?>">
                        <?= $estado->toString() ?>
                    </span>
                </h5>

                <?php
                    $permissaoAlterar = $permissao->alterar($_SESSION['id_usuario'], $_SESSION['tipo']);
                    $mostrarBotao = $permissaoAlterar != PermissaoTarefa::NAO_AUTORIZADO;
                    $desabilitarBotao = $permissaoAlterar != PermissaoTarefa::PODE;
                    $desabilitarMotivo = match ($permissaoAlterar) {
                        PermissaoTarefa::ARQUIVADA => 'é de um ano passado e está arquivada',
                        PermissaoTarefa::FECHADA   => 'já foi fechada',
                        default                    => '[cód. '.$permissaoAlterar.']'
                    };

                    if ($mostrarBotao) {
                        $tooltip
                        = $desabilitarBotao
                        ? 'data-bs-toggle="tooltip" title="A tarefa não pode ser alterada pois '.$desabilitarMotivo.'"'
                        : '';

                        echo
                        '<div '.$tooltip.' style="margin-left: 15px;">
                            <span>
                                <button type="button" class="btn btn-primary"
                                        onclick="location.assign(\'alterar?id='.$tarefa->id().'\')"
                                        '.($desabilitarBotao ? 'disabled' : '').'>
                                    <i class="fas fa-edit"></i>
                                </button>
                            </span>
                        </div>';
                    }
                ?>


            </div>
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
                        value="<?=dataISO($tarefa->dataHoraEntrega())?>"/>
                </div>

                <!-- TODO deixar explícito que nenhuma data de fechamento foi informada -->

                <div class="col-sm-6">
                    <?php if (!is_null($tarefa->dataHoraFechamento())): ?>
                        <label class="form-label" for="fechamento">Data de fechamento</label>
                        <input disabled readonly class="form-control" id="fechamento" type="datetime-local"
                            value="<?=dataISO($tarefa->dataHoraFechamento())?>"/>
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
                <i><?= $tarefa->dataHoraAbertura()->format('d/m/Y H:i') ?></i>
            </span>
        </div>
    </div>

    <?php
    if ($_SESSION['tipo'] == TipoUsuario::ALUNO):
        $alunoJaEntregou = $view['entrega'] != null;
        $tarefaPermiteEntrega = $estado == TarefaEstado::ABERTA || $estado == TarefaEstado::ATRASADA; ?>

        <!-- TODO mostrar data e hora da entrega
             e também se a entrega ficou atrasada -->

        <!-- TODO além disso, talvez fica estranho mostrar um estado 'Atrasada' para a entrega,
             porque é o que vai ser mostrado para o aluno mesmo que ele tenha feito a entrega

             mostrar no lugar "Passou da data de entrega" com uma cor diferente, mais neutra?  -->

        <div class="card mb-3">
            <div class="card-header d-flex align-items-center">
                <?php if ($alunoJaEntregou) {
                    $dataHoraEntrega = DateUtil::toLocalDateTime($view['entrega']['data_hora']);
                    echo '<span>Entregue em <i>'.$dataHoraEntrega->format('d/m H:i').'</i></span>';
                    if ($dataHoraEntrega > $tarefa->entrega()) {
                        echo '<h5 class="mb-0" style="margin-left: 15px;"><span class="badge bg-warning text-dark">Atrasada</span></h5>';
                    }
                } else {
                    echo 'Entrega pendente';
                } ?>
            </div>
            <div class="card-body">
                <?php if ($alunoJaEntregou && ($estado == TarefaEstado::FECHADA || $estado == TarefaEstado::ARQUIVADA)): ?>
                    <div class="d-flex align-items-center">
                        <div>Avaliação do professor</div>
                        <?php if ($tarefa->comNota()) {
                            $nota = $view['entrega']['nota'];
                            $textoAvaliacao = ($nota ?? '?') .'/10';
                            $bgAvaliacao = !$nota ? 'bg-secondary' : ($nota < 7 ? 'bg-warning' : 'bg-success');
                        } else {
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
                        }
                        $corTextoAvaliacao = $bgAvaliacao == 'bg-warning' ? 'text-dark' : '';
                        ?>
                        <h5 class="mb-0" style="margin-left: 15px;">
                            <span class="badge <?= $bgAvaliacao ?> <?= $corTextoAvaliacao ?>">
                                <?= $textoAvaliacao ?>
                            </span>
                        </h5>
                    </div>

                    <?php if (!empty($view['entrega']['comentario'])): ?>
                        <textarea disabled readonly id="entrega-comentario" class="mt-3 form-control" rows="3"
                        ><?= $view['entrega']['comentario'] ?></textarea>
                    <?php endif; ?>

                    <hr/>

                <?php endif; ?>

                <?php if ($alunoJaEntregou || $tarefaPermiteEntrega):
                    if ($alunoJaEntregou) {
                        $formMethod = 'PUT';
                        $endpointEntrega = 'alterar?aluno='.$view['entrega']['id_aluno'].'&tarefa='.$view['entrega']['id_tarefa'];
                        $conteudoEntrega = $view['entrega']['conteudo'];
                        $iconeBotao = 'fa-paper-plane';
                        $textoBotao = 'Alterar';
                    } else {
                        $formMethod = 'POST';
                        $endpointEntrega = 'criar?tarefa='.$tarefa->id();
                        $conteudoEntrega = '';
                        $iconeBotao = 'fa-paper-plane';
                        $textoBotao = 'Salvar';
                    }
                    $formAction = "/aluno/entregas/$endpointEntrega";

                    if (!$tarefaPermiteEntrega): ?>
                        <div class="alert alert-warning">
                            Você não pode mais alterar a entrega.
                        </div>
                    <?php endif; ?>

                    <?php if (DateUtil::toLocalDateTime('now') >= $tarefa->dataHoraEntrega()): ?>
                        <div class="alert alert-warning">
                            Ao ser entregue em definitivo, sua entrega ficará marcada como <b>atrasada</b>.
                        </div>
                    <?php endif; ?>

                    <!-- method de form aparentemente só pode ser GET ou POST, então colocado como data attribute -->
                    <form id="form-fazer-entrega" data-method="<?= $formMethod ?>" action="<?= $formAction ?>">
                        <textarea
                            class="mb-0 form-control" name="conteudo" id="conteudo" rows="3" required
                            <?= $alunoJaEntregou && !$tarefaPermiteEntrega ? 'disabled readonly' : ''?>
                        ><?= $conteudoEntrega ?></textarea>
                        <?php if ($tarefaPermiteEntrega): ?>
                            <div class="float-end">
                                <button type="submit" id="btn-fazer-entrega" class="mt-3 btn btn-primary">
                                    <i class="fas <?= $iconeBotao ?>"></i>
                                    <?= $textoBotao ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <div class="mb-0 alert alert-danger">
                        Você não pode mais realizar a entrega desta tarefa.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>
</main>

<script>

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

//
// Aluno
//

const idTarefa = <?= $tarefa->id() ?>;

<?php if ($_SESSION['tipo'] == TipoUsuario::ALUNO): ?>

    //
    // Fazer entrega
    //

    const formEntrega = document.getElementById('form-fazer-entrega');

    formEntrega?.addEventListener('submit', async event => {
        event.preventDefault();

        const target = formEntrega.action;
        const method = formEntrega.dataset.method;
        const body = JSON.stringify({ conteudo: formEntrega.conteudo.value });

        const response = await fetch(target, { method, body });
        const text = await response.text();

        try {
            const ret = JSON.parse(text);
            if (response.status != 200) {
                Swal.fire({
                    icon: 'error',
                    text: ret.message
                });
                if (ret.hasOwnProperty('exception')) console.error(exception);
                return;
            }

            agendarAlertaSwal({
                icon: 'success',
                text: ret.message
            });
            location.assign(`tarefa?id=${idTarefa}`);

        } catch(e) {
            console.error(e);
            console.error(text);
        }
    });

<?php endif; ?>

</script>

</body>
</html>