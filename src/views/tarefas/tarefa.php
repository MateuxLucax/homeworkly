<!-- TODO estado da tarefa em relação ao aluno: pendente; entregue -->
<!-- mostrar que nem o estado da tarefa -->

<?php
    $tarefa = $view['tarefa'];
    $disciplina = $tarefa->disciplina();
    $turma = $disciplina->getTurma();
    $professor = $tarefa->professor();

    $permissaoTarefa = $view['permissaoTarefa'];
?>

<?php $rootUsuario = '/' . TipoUsuario::toString($_SESSION['tipo']) . '/'; ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <?=$turma->getAno()?>
    </li>
    <li class="breadcrumb-item">
        <a href="<?=$rootUsuario?>turmas/turma?id=<?=$turma->getId()?>">
            <?=$turma->getNome()?>
        </a>
    </li>
    <li class="breadcrumb-item active">
        <a href="<?=$rootUsuario?>disciplinas/disciplina?id=<?=$disciplina->getId()?>">
            <?=$disciplina->getNome()?>
        </a>
    </li>
</ol>

<div class="card px-0 mb-3">
    <div class="card-header d-flex align-items-center">
        Tarefa
        &nbsp;
        <small>(ID <?= $tarefa->id()?>)</small>
        <div class="ms-auto d-flex align-items-center">

            <?php
                $estado = $tarefa->estado();
                $classeBgEstado = match($estado) {
                    TarefaEstado::ESPERANDO_ABERTURA => 'bg-primary',
                    TarefaEstado::ABERTA             => 'bg-success',
                    TarefaEstado::FECHADA            => 'bg-dark',
                    TarefaEstado::ARQUIVADA          => 'bg-secondary'
                };
            ?>
            <h5 class="mb-0">
                <span class="badge <?= $classeBgEstado ?>">
                    <?= $estado->toString() ?>
                </span>
            </h5>

            <?php
                $permissaoTarefaAlterar = $permissaoTarefa->alterar($_SESSION['id_usuario'], $_SESSION['tipo']);
                $mostrarBotao = $permissaoTarefaAlterar != PermissaoTarefa::NAO_AUTORIZADO;
                $desabilitarBotao = $permissaoTarefaAlterar != PermissaoTarefa::PODE;
                $desabilitarMotivo = match ($permissaoTarefaAlterar) {
                    PermissaoTarefa::ARQUIVADA => 'é de um ano passado e está arquivada',
                    PermissaoTarefa::FECHADA   => 'já foi fechada',
                    default                    => '[cód. '.$permissaoTarefaAlterar.']'
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

            <div class="col-sm-6">
                <label class="form-label" for="fechamento">Data de fechamento</label>
                <input disabled readonly class="form-control" id="fechamento" type="datetime-local"
                       value="<?=dataISO($tarefa->dataHoraFechamento())?>"/>
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

//
// Aluno
//

if ($_SESSION['tipo'] == TipoUsuario::ALUNO)
{
    $entrega = $view['entrega'];
    $entregaSituacao = $tarefa->entregaSituacao($entrega);
    ?>

    <div class="card px-0 mb-3">
        <div class="card-header d-flex align-items-center">
            <?php if ($entregaSituacao->entregue()) {
                echo '<span>Entregue em <i>'.$entrega->dataHora()->format('d/m H:i').'</i></span>';
                if ($entregaSituacao == EntregaSituacao::ENTREGUE_ATRASADA) {
                    echo '<h5 class="mb-0" style="margin-left: 15px;"><span class="badge bg-warning text-dark">Atrasada</span></h5>';
                }
            } else {
                echo 'Entrega pendente';
            } ?>
        </div>
        <div class="card-body">
            <?php if ($entregaSituacao->entregue() && $tarefa->fechada()): ?>
                <div class="d-flex align-items-center">
                    <div>Avaliação do professor</div>
                    <?php if ($tarefa->comNota()) {
                        $nota = $entrega->nota();
                        $textoAvaliacao = ($nota ?? '?') .'/10';
                        $bgAvaliacao = !$nota ? 'bg-secondary' : ($nota < 7 ? 'bg-warning' : 'bg-success');
                    } else {
                        if ($entrega->visto() === null) {
                            $textoAvaliacao = 'Não visto';
                            $bgAvaliacao = 'bg-secondary';
                        } else if ($entrega->visto()) {
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

                <?php if (!empty($entrega->comentario())): ?>
                    <textarea disabled readonly id="entrega-comentario" class="mt-3 form-control" rows="3"
                    ><?= $entrega->comentario() ?></textarea>
                <?php endif; ?>

                <hr/>

            <?php endif; ?>

            <?php
            $conteudoEntrega = $entrega == null ? '' : $entrega->conteudo();

            $formMethod = $formAction = $endpointEntrega = $iconeBotao = $textoBotao = '';

            if ($entregaSituacao->pendente()) {
                if ($entrega != null) {
                    $formMethod = 'PUT';
                    $endpointEntrega = 'alterar?aluno='.$_SESSION['id_usuario'].'&tarefa='.$tarefa->id();
                    $iconeBotao = 'fa-paper-plane';
                    $textoBotao = 'Alterar';
                } else {
                    $formMethod = 'POST';
                    $endpointEntrega = 'criar?tarefa='.$tarefa->id();
                    $iconeBotao = 'fa-paper-plane';
                    $textoBotao = 'Salvar';
                }
                $formAction = "/aluno/entregas/$endpointEntrega";

                if ($entregaSituacao == EntregaSituacao::PENDENTE_ATRASADA) {
                    echo
                    '<div class="alert alert-warning">
                        Já passou da data de entrega, então sua entrega ficará marcada como <b>atrasada</b>.
                    </div>';
                }
            } else if ($entregaSituacao == EntregaSituacao::NAO_FEITA) {
                echo
                '<div class="alert alert-warning">
                    Você não pode mais entregar a tarefa em definitivo.
                </div>';
            }
            ?>

            <!-- method de form aparentemente só pode ser GET ou POST, então colocado como data attribute -->
            <form id="form-fazer-entrega" data-method="<?= $formMethod ?>" action="<?= $formAction ?>">
                <label for="conteudo" class="form-label">Sua entrega</label>
                <textarea
                    class="mb-0 form-control" name="conteudo" id="conteudo" rows="3" required
                    <?= $entregaSituacao->pendente() ? '' : 'disabled readonly'?>
                ><?= $conteudoEntrega ?></textarea>
                <?php if ($entregaSituacao->pendente()): ?>
                    <div class="float-end">
                        <button type="button" id="btn-fazer-entrega" class="mt-3 btn btn-primary">
                            <i class="fas <?= $iconeBotao ?>"></i>
                            <?= $textoBotao ?>
                        </button>
                        <?php if ($entrega != null): ?>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-confirmar-entrega-em-definitivo" class="mt-3 btn btn-success">
                                <i class="fas fa-check"></i>
                                Entregar em definitivo
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
<?php } ?>

<?php
//
// Professor
//

if ($_SESSION['tipo'] == TipoUsuario::PROFESSOR)
{
    $entregasPorAluno = $view['entregasPorAluno'];
    ?>

    <div class="card px-0">
        <div class="card-header d-flex align-items-center">
            <div class="card-title mb-0">Entregas</div>

            <?php $verboBotaoEntregas = $tarefa->estado() == TarefaEstado::ARQUIVADA ? 'Ver' : 'Avaliar'; ?>

            <a href="../entregas/?tarefa=<?=$tarefa->id()?>" class="ms-auto btn btn-primary">
                <i class="fas fa-spell-check"></i>
                <?= $verboBotaoEntregas ?> entregas
            </a>
            
            <!-- TODO atalho para avaliar entrega de cada aluno, passando por get o id do aluno ao /entregas/?tarefa -->

        </div>
        <div class="card-body">
            <table class="table <?= count($entregasPorAluno) == 0 ? 'd-none' : '' ?>">
                <tr>
                    <th>Aluno</th>
                    <th>Situação</th> <!-- Rascunho, Atrasado ou A tempo? -->
                    <!-- TODO usar as mesmas categorias na visão do aluno quando ele estiver vendo a entrega dele -->

                    <th>Avaliação</th> <!-- Avaliação pendente ou Nota ou Visto -->
                    <th>Data</th>
                </tr>
                <?php foreach ($entregasPorAluno as $alunoEntrega) {
                    $aluno = $alunoEntrega['aluno'];
                    $entrega = $alunoEntrega['entrega'];

                    $entregaSituacao = $tarefa->entregaSituacao($entrega);

                    list($textoSituacao, $bgSituacao, $corSituacao) = match($entregaSituacao) {
                        EntregaSituacao::PENDENTE             => ['Pendente', 'bg-info', 'text-dark'],
                        EntregaSituacao::PENDENTE_ATRASADA    => ['Atrasada', 'bg-warning', 'text-dark'],
                        EntregaSituacao::NAO_FEITA            => ['Não feita', 'bg-danger', 'text-white'],
                        EntregaSituacao::ENTREGUE             => ['Entregue', 'bg-success', 'text-white'],
                        EntregaSituacao::ENTREGUE_ATRASADA    => ['Entregue com atraso', 'bg-success', 'text-white']
                    };


                    $avaliacao = '';
                    // @Copypaste "Avaliação do professor" logo acima,
                    // TODO extrair para algum lugar
                    // o mais imediato seria uma função dentro desse arquivo mesmo
                    if ($tarefa->comNota()) {
                        $nota = $entrega?->nota();
                        if ($nota == null) {
                            $textoAvaliacao = 'Sem nota';
                            $bgAvaliacao = 'bg-secondary';
                        } else {
                            $textoAvaliacao = $nota . '/10';
                            $bgAvaliacao = $nota < 7 ? 'bg-warning' : 'bg-success';
                        }
                    } else {
                        if ($entrega?->visto() === null) {
                            $textoAvaliacao = 'Não visto';
                            $bgAvaliacao = 'bg-secondary';
                        } else if ($entrega->visto()) {
                            $textoAvaliacao = 'Visto';
                            $bgAvaliacao = 'bg-success';
                        } else {
                            $textoAvaliacao = 'Não aceito';
                            $bgAvaliacao = 'bg-danger';
                        }
                    }
                    $corTextoAvaliacao = $bgAvaliacao == 'bg-warning' ? 'text-dark' : '';

                    $avaliacao = '
                        <span class="badge '.$bgAvaliacao.' '.$corTextoAvaliacao.'">
                            '.$textoAvaliacao.'
                        </span>';

                    $comentario = '';
                    if ($entrega?->comentario() != null) {
                        // espaços em branco com alt+255
                        $comentario = '  <h5 class="mb-1 text-secondary"><i class="fas fa-comment-dots" data-bs-toggle="tooltip" title="'.$entrega->comentario().'"></i></h5>';
                    }
                    ?>
                    <tr>
                        <td><?= $aluno->getNome() ?></td>
                        <td><span class="badge <?= $bgSituacao ?> <?= $corSituacao ?>">
                            <?= $textoSituacao ?>
                        </span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?= $avaliacao ?>
                                <?= $comentario ?>
                            </div>
                        </td>
                        <td><?= $entrega?->dataHora()?->format('d/m/Y H:i') ?></td>
                    </tr>
                <?php } // foreach ?>
            </table>
        </div>
    </div>
    
    <?php
}
?>

<?php if ($_SESSION['tipo'] == TipoUsuario::ALUNO): ?>
<div class="modal fade" id="modal-confirmar-entrega-em-definitivo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p>Tem certeza que deseja entregar essa tarefa em definitivo?</p>
                <p>Você não poderá mais alterar a entrega.</p>

                <button class="btn btn-success" id="btn-confirmar-entrega-em-definitivo">
                    Ok
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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
    const btnFazerEntrega = document.getElementById('btn-fazer-entrega');
    const btnEntregaEmDefinitivo = document.getElementById('btn-confirmar-entrega-em-definitivo');

    formEntrega?.addEventListener('submit', event => event.preventDefault());
    // ENTER também causa submit, mas queremos que só seja pelo botão

    btnFazerEntrega?.addEventListener('click', async () => { fazerEntrega(false); });
    btnEntregaEmDefinitivo?.addEventListener('click', async () => { fazerEntrega(true); });

    async function fazerEntrega(emDefinitivo) {
        const target = formEntrega.action;
        const method = formEntrega.dataset.method;

        const body = JSON.stringify({
            conteudo: formEntrega.conteudo.value,
            emDefinitivo
        });

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
    }
<?php endif; ?>

</script>