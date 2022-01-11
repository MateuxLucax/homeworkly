<!DOCTYPE html>
<html lang="pt-BR">
<?php require_once $root.'/views/componentes/head.php'; ?>
<body>

<!--TODO não em h1, e colocar links para a turma e para a disciplina
    TODO e também deixar num estilo mais breadcrumbs
    e fazer o mesmo na view turma.php-->

<?php
    $paginaAlterar = isset($view['tarefa']);
    $tarefa = $paginaAlterar ? $view['tarefa'] : null;
?>

<main class="container">
    <div class="header mb-3">
        <h1><?=$view['ano']?> / <?=$view['turma_nome']?> / <b><?=$view['disciplina_nome']?></b></h1>
    </div>
    <form id="form-criar-tarefa">
        <?php if ($paginaAlterar): ?>
            <input type="hidden" name="id" value="<?=$tarefa?->id()?>">
        <?php endif; ?>
        <input type="hidden" name="disciplina" value="<?= $view['disciplina_id'] ?>" />
        <input type="hidden" name="professor" value="<?= $view['professor_id'] ?>" />
        <div class="card mb-3">
            <div class="card-header">
                <span class="card-title">
                    <?= $paginaAlterar ? 'Alterar tarefa' : 'Criar tarefa' ?>
                </span>
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
                               <?= $aberturaPassou ? 'data-bs-toggle="tooltip" title="A tarefa já foi abertura, então sua data de abertura não pode ser modificada."' : '' ?>
                               value="<?=$paginaAlterar ? dataISO($tarefa?->abertura()) : '' ?>"/>
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
                               value="<?= $paginaAlterar ? dataISO($tarefa?->entrega()) : '' ?>"/>
                    </div>
                    <div class="mb-3 col-12 col-md-4">
                        <label class="form-label" for="fechamento">
                            Data fechamento
                            &nbsp;
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" title="Opcional – se não informar, você pode fechar manualmente depois. Depois dela, alunos não podem mais fazer entregas."></i>
                        </label>
                        <input class="form-control" type="datetime-local" name="fechamento" id="fechamento"
                               value="<?= $paginaAlterar && $tarefa?->fechamento() != null ? dataISO($tarefa->fechamento()) : '' ?>"/>
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
              fechamento = new Date(form.fechamento.value);

        form.abertura.setCustomValidity(
              !switchAbrirAgora.checked && abertura < new Date()
            ? 'A data de abertura não pode estar no passado'
            : ''
        );

        form.entrega.setCustomValidity(
              form.entrega.value && entrega < abertura
            ? 'A data de entrega deve vir depois da data de abertura'
            : ''
        );

        let validFechamento = '';
        if (form.fechamento.value) {
            if (form.entrega.value && fechamento < entrega) {
                validFechamento = 'A data de fechamento deve vir depois da data de entrega;'
            }
            if (fechamento < abertura) {
                validFechamento = 'A data de fechamento deve vir depois da data de abertura';
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
        msgErro = 'Não foi possível criar a tarefa';
        msgSucc = 'Tarefa criada com sucesso';
    } else {
        target = 'alterar';
        metodo = 'PUT';
        status = 200;
        msgErro = 'Não foi possível alterar a tarefa';
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
        const textProm = response.text();
        if (response.status != status) {
            Swal.fire({
                icon: 'error',
                title: 'Erro do sistema',
                text: msgErro
            });
            console.log(await textProm);
        } else {
            agendarAlertaSwal({
                icon: 'success',
                text: msgSucc
            });
            const text = await textProm;
            try {
                const ret = JSON.parse(text);
                location.assign(`tarefa?id=${ret.id}`);
            } catch (err) {
                console.error(err, '\n', text);
            }
        }
    };


</script>