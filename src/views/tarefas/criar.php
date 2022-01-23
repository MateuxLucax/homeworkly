<!DOCTYPE html>
<html lang="pt-BR">
<?php require_once $root.'/views/componentes/head.php'; ?>
<body>

<?php
    $turma = $view['turma'];
    $disciplina = $view['disciplina'];

    $paginaAlterar = isset($view['tarefa']);
    $tarefa = $paginaAlterar ? $view['tarefa'] : null;
    $permissao = $paginaAlterar ? $view['permissao'] : null;
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
    <form id="form-criar-tarefa">
        <?php if ($paginaAlterar): ?>
            <input type="hidden" name="id" value="<?=$tarefa?->id()?>">
        <?php endif; ?>
        <input type="hidden" name="disciplina" value="<?= $disciplina->getId() ?>" />
        <input type="hidden" name="professor" value="<?= $view['professor_id'] ?>" />
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center">
                <span>
                    <?= $paginaAlterar ? 'Alterar tarefa' : 'Criar tarefa' ?>
                </span>
                <div class="ms-auto">
                    <?php
                        if ($paginaAlterar) {
                            $estado = $tarefa->estado();
                            $corEstado = match($estado) {
                                TarefaEstado::ESPERANDO_ABERTURA => 'bg-primary',
                                TarefaEstado::ABERTA             => 'bg-success',
                                TarefaEstado::ATRASADA           => 'bg-warning',
                                TarefaEstado::FECHADA            => 'bg-dark',
                                TarefaEstado::ARQUIVADA          => 'bg-secondary'
                            };

                            $corTexto = $corEstado == 'bg-warning' ? 'text-dark' : '';

                            echo '<h5 class="mb-0 d-inline"><span class="badge '.$corEstado.' '.$corTexto.'">'.$estado->toString().'</span></h5>';

                            $permissaoExcluir = $permissao->excluir($_SESSION['id_usuario'], $_SESSION['tipo']);
                            $mostrarBotao = $permissaoExcluir != PermissaoTarefa::NAO_AUTORIZADO;
                            $desabilitarBotao = $permissaoExcluir != PermissaoTarefa::PODE;
                            $desabilitarMotivo = match ($permissaoExcluir) {
                                PermissaoTarefa::ARQUIVADA    => 'está arquivada (é de um ano passado)',
                                PermissaoTarefa::FECHADA      => 'já foi fechada',
                                PermissaoTarefa::TEM_ENTREGAS => 'tem entregas',
                                default                       => ''
                            };

                            if ($mostrarBotao): ?>
                                <div style="margin-left: 15px;" class="d-inline" <?= $desabilitarBotao ? 'data-bs-toggle="tooltip" title="A tarefa não pode ser excluída pois '.$desabilitarMotivo.'."' : '' ?>>
                                    <button type="button" class="btn btn-danger"
                                            data-bs-toggle="modal" data-bs-target="#modal-confirmar-exclusao"
                                            <?= $desabilitarBotao ? 'disabled' : '' ?>
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                            <?php endif;
                        }
                    ?>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="titulo">Título</label>
                    <input class="form-control" type="text" name="titulo" id="titulo" minlength="5" required
                           value="<?= $tarefa?->titulo() ?>"/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="descricao">Descrição</label>
                    <textarea class="form-control" name="descricao" id="descricao"><?= $tarefa?->descricao() ?></textarea>
                </div>

                <?php function dataISO(DateTime $data) : string {
                    return $data->format('Y-m-d\TH:i');
                } ?>

                <div class="row">
                    <div class="mb-3 col-12 col-md-4">
                        <label class="form-label" for="abertura">
                            Data abertura
                            &nbsp;
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" title="Quando a tarefa se torna disponível para os alunos."></i>
                        </label>
                        <?php $aberturaPassou = $paginaAlterar && $tarefa->estado() != TarefaEstado::ESPERANDO_ABERTURA; ?>

                        <input class="form-control mb-2" type="datetime-local" name="abertura" id="abertura"
                               <?= $aberturaPassou ? 'readonly disabled' : '' ?>
                               <?= $aberturaPassou ? 'data-bs-toggle="tooltip" title="A tarefa já foi aberta, então sua data de abertura não pode ser modificada."' : '' ?>
                               value="<?=$paginaAlterar ? dataISO($tarefa?->dataHoraAbertura()) : '' ?>"/>
                        <div class="form-check form-switch <?= $aberturaPassou ? 'd-none' : '' ?>">
                            <input type="checkbox" class="form-check-input" id="abrir-agora"
                                   <?= $aberturaPassou ? '' : 'checked' ?>/>
                            <label class="form-check-label" for="abrir-agora">Abrir agora</label>
                        </div>
                    </div>
                    <div class="mb-3 col-12 col-md-4">
                        <label class="form-label" for="entrega">
                            Data entrega
                            &nbsp;
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" title="Depois da data de entrega, entregas ainda podem ser feitas, mas são marcadas como atrasadas."></i>
                        </label>
                        <input class="form-control" type="datetime-local" name="entrega" id="entrega" required 
                               value="<?= $paginaAlterar ? dataISO($tarefa?->dataHoraEntrega()) : '' ?>"/>
                    </div>
                    <div class="mb-3 col-12 col-md-4">
                        <label class="form-label" for="fechamento">
                            Data fechamento
                            &nbsp;
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" title="Opcional – se não informar, você pode fechar manualmente depois. Depois dela, alunos não podem mais fazer entregas."></i>
                        </label>
                        <input class="form-control" type="datetime-local" name="fechamento" id="fechamento"
                               value="<?= $paginaAlterar && $tarefa?->dataHoraFechamento() != null ? dataISO($tarefa->dataHoraFechamento()) : '' ?>"/>
                    </div>
                </div>
                <div class="row">

                    <?php
                        $horasVal = '';
                        $minsVal = '00';
                        if ($paginaAlterar) {
                            $minsTotal = $tarefa->esforcoMinutos();
                            $horasVal = (int) ($minsTotal / 60);
                            $minsVal = sprintf('%02d', $minsTotal % 60);
                        }
                    ?>

                    <!-- TODO validar com customValidiyt conforme usuário editar:
                         depois de contabilizar quantos minutos dá, deve ser maior que 0 -->

                    <div class="col-12 mb-3 col-sm-6 mb-sm-0 col-md-4">
                        <label class="form-label" for="esforcoMinutos">
                            Esforço
                            &nbsp;
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" title="Estimativa de quanto tempo, em horas e minutos, um aluno demorará para realizar essa tarefa."></i>
                        </label>
                        <div class="input-group">
                            <input name="esforcoHoras" class="form-control" placeholder="Horas"
                                   style="text-align: right;" type="text" pattern="\d*" inputmode="numeric" value="<?= $horasVal ?>"/>
                            <span class="input-group-text">:</span>
                            <input name="esforcoMinutos" class="form-control" placeholder="Minutos"
                                   type="text" pattern="[0-5]?\d" inputmode="numberic" value="<?= $minsVal ?>"/>
                        </div>
                    </div>

                    <?php $comNota = !$paginaAlterar || $tarefa->comNota(); ?>
                    <div class="col-12 col-sm-6 col-md-8">
                        <label class="form-label" for="comNota">Avaliação</label>
                        <br/>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="comNota" value="true" id="avaliacao-nota"
                                   <?= $comNota ? 'checked' : '' ?>/>
                            <label class="form-check-label" for="avaliacao-nota">Nota</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="comNota" value="false" id="avaliacao-visto"
                                   <?= $comNota ? '' : 'checked' ?>/>
                            <label class="form-check-label" for="avaliacao-nota">Visto</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end">
            <button class="btn btn-outline-primary btn-lg" type="submit">
                <i class="fas fa-paper-plane"></i>
                <?= $paginaAlterar ? 'Alterar' : 'Criar' ?>
            </button>
        </div>
    </form>
</main>

<?php if ($paginaAlterar): ?>
<div class="modal fade" id="modal-confirmar-exclusao">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p>Tem certeza que deseja excluir esta tarefa?</p>
                <button class="btn btn-danger" id="btn-confirmar-exclusao">Excluir</button>
                <button class="btn btn-secondary" id="btn-cancelar-exclusao" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

</body>

<script type="text/javascript">
    'use strict';

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

    const form = document.getElementById('form-criar-tarefa');

    //
    // Data de abertura
    // atualização automática se "agora" + tratamento da troca pelo switch
    //

    const inputAbertura = document.getElementById('abertura');
    const switchAbrirAgora = document.getElementById('abrir-agora');

    function trocarTipoAbertura(abrirAgora) {
        if (abrirAgora) {
            inputAbertura.required = false;
            inputAbertura.style.display = 'none';
        } else {
            inputAbertura.required = true;
            inputAbertura.style.display = 'inline-block';
        }
    }

    trocarTipoAbertura(switchAbrirAgora.checked);
    switchAbrirAgora.addEventListener('change', () => { trocarTipoAbertura(switchAbrirAgora.checked); });

    //
    // Validação extra das datas
    //

    function validarDatas() {
        const abertura   = new Date(form.abertura.value),
              entrega    = new Date(form.entrega.value),
              fechamento = new Date(form.fechamento.value),
              agora      = new Date();

        let validAbertura = '';
        if (!switchAbrirAgora.checked) {
            if (abertura < agora) {
                validAbertura = 'A data de abertura não pode estar no passado';
            } else if (abertura.getFullYear() > agora.getFullYear()) {
                validAbertura = 'A tarefa deverá ser aberta este ano';
            }
        }
        form.abertura.setCustomValidity(validAbertura);

        let validEntrega = '';
        if (entrega < abertura) {
            validEntrega = 'A data de entrega deve vir depois da data de abertura';
        } else if (entrega.getFullYear() > agora.getFullYear()) {
            validEntrega = 'A entrega deverá ocorrer este ano';
        }
        form.entrega.setCustomValidity(validEntrega);

        let validFechamento = '';
        if (form.fechamento.value) {
            if (fechamento < entrega) {
                validFechamento = 'A data de fechamento deve vir depois da data de entrega;'
            } else if (fechamento < abertura) {
                validFechamento = 'A data de fechamento deve vir depois da data de abertura';
            } else if (fechamento.getFullYear() > agora.getFullYear()) {
                validFechamento = 'A tarefá deverá ser fechada este ano';
            }
        }
        form.fechamento.setCustomValidity(validFechamento);
    }

    validarDatas();
    form.abertura   .addEventListener('change', validarDatas);
    form.entrega    .addEventListener('change', validarDatas);
    form.fechamento .addEventListener('change', validarDatas);
    switchAbrirAgora.addEventListener('change', validarDatas);

    //
    // Envio do formulário
    //

    const paginaAlterar = <?= $paginaAlterar ? 'true' : 'false' ?>;

    let target, status, metodo, msgErro, msgSucc;

    if (!paginaAlterar) {
        target = 'criar';
        status = 201;
        metodo = 'POST';
        msgErro = 'Não foi possível criar a tarefa (o servidor não retornou o motivo)';
        msgSucc = 'Tarefa criada com sucesso';
    } else {
        target = 'alterar';
        metodo = 'PUT';
        status = 200;
        msgErro = 'Não foi possível alterar a tarefa (o servidor não retornou o motivo)';
        msgSucc = 'Tarefa alterada com sucesso';
    }

    form.onsubmit = async event => {
        event.preventDefault();

        const agora = new Date();
        agora.setMinutes(agora.getMinutes() - agora.getTimezoneOffset());

        const dados = {
            professor:      form.professor.value,
            disciplina:     form.disciplina.value,
            titulo:         form.titulo.value,
            descricao:      form.descricao.value,
            esforcoMinutos: Number(form.esforcoHoras.value) * 60 + Number(form.esforcoMinutos.value),
            comNota:        form.comNota.value == 'true',
            abertura:       switchAbrirAgora.checked ? agora.toISOString() : form.abertura.value,
            entrega:        form.entrega.value,
            fechamento:     form.fechamento.value == '' ? null : form.fechamento.value
        };

        if (paginaAlterar) {
            dados.id = form.id.value;
        }

        const response = await fetch(target, {method: metodo, body: JSON.stringify(dados)});
        const textRet = await response.text();
        try {
            const ret = JSON.parse(textRet);
            if (response.status != status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro do sistema',
                    text: ret.message || msgErro
                });
                console.error(ret.exception);
            } else {
                agendarAlertaSwal({
                    icon: 'success',
                    text: msgSucc
                });
                location.assign(`tarefa?id=${ret.id}`);
            }
        } catch(e) {
            console.error(e, '\n', textRet);
        }
    };

    //
    // Excluir tarefa
    //

    if (paginaAlterar) {
        document.getElementById('btn-confirmar-exclusao').onclick = async () => {
            const response = await fetch('excluir', { method: 'DELETE', body: JSON.stringify({id: form.id.value}) });
            const ret = await response.json();
            if (response.status != 200) {
                Swal.fire({
                    icon: 'error',
                    text: ret.message
                });
                document.getElementById('btn-cancelar-exclusao').click();  // Fechar modal
                return;
            }
            agendarAlertaSwal({
                icon: 'success',
                text: ret.message
            });
            // TODO trocar para location.assign('/{tipoUsuario}/disciplinas/disciplina?id=...);
            // quando esse endpoint existir
            history.back();
        }
    }

</script>