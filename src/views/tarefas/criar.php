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
                    <div class="mb-3 col-12 col-sm-4">
                        <label class="form-label" for="abertura">
                            Data abertura
                            &nbsp;
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" title="Quando a tarefa se torna disponível para os alunos."></i>
                        </label>
                        <input class="form-control" type="datetime-local" name="abertura" id="abertura" readonly required
                               value="<?=$paginaAlterar ? dataISO($tarefa?->abertura()) : '' ?>"/>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input checked class="form-check-input" type="radio" name="abrir" value="agora" id="abrir-agora">
                                <label class="form-check-label">Agora</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="abrir" value="depois" id="abrir-depois">
                                <label class="form-check-label">Depois</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 col-12 col-sm-4">
                        <label class="form-label" for="entrega">
                            Data entrega
                            &nbsp;
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" title="Depois da data de entrega, entregas ainda podem ser feitas, mas são marcadas como atrasadas."></i>
                        </label>
                        <input class="form-control" type="datetime-local" name="entrega" id="entrega" required 
                               value="<?= $paginaAlterar ? dataISO($tarefa?->entrega()) : '' ?>"/>
                    </div>
                    <div class="mb-3 col-12 col-sm-4">
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
                        $esforcoValue = '';
                        if ($paginaAlterar) {
                            $esforcoMinutos = $tarefa?->esforcoMinutos();
                            $horas = (int) ($esforcoMinutos / 60);
                            $mins  = $esforcoMinutos % 60;
                            $esforcoValue = sprintf('%2d:%2d', $horas, $mins);
                        }
                    ?>
                    <div class="col-6">
                        <label class="form-label" for="esforcoMinutos">Estimativa de esforço</label>
                        <input class="form-control" type="time" name="esforcoMinutos" id="esforcoMinutos" required value="<?=$esforcoValue?>"/>
                    </div>

                    <?php $comNota = !$paginaAlterar || $tarefa->comNota(); ?>
                    <div class="col-6">
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
    // atualização automática se "agora" + tratamento da troca pelos radios
    //

    const inputAbertura = document.getElementById('abertura');

    const radioAbrirAgora  = document.getElementById('abrir-agora');
    const radioAbrirDepois = document.getElementById('abrir-depois');

    let intervalAtualizarAbertura;

    trocarTipoAbertura(radioAbrirAgora.checked ? 'agora' : 'depois');
    
    function atualizarAbertura() {
        const agora = new Date();
        // https://stackoverflow.com/a/61082536
        agora.setMinutes(agora.getMinutes() - agora.getTimezoneOffset());
        inputAbertura.value = agora.toISOString().slice(0, 16);
    }

    function trocarTipoAbertura(tipo) {
        if (tipo == 'agora') {
            inputAbertura.setAttribute('readonly', true);
            atualizarAbertura();
            intervalAtualizarAbertura = setInterval(atualizarAbertura, 5000)
        } else {  // depois
            inputAbertura.removeAttribute('readonly');
            inputAbertura.value = '';
            clearInterval(intervalAtualizarAbertura);
        }
    }

    radioAbrirAgora.addEventListener('change',  () => { trocarTipoAbertura('agora'); });
    radioAbrirDepois.addEventListener('change', () => { trocarTipoAbertura('depois'); });

    //
    // Validação extra das datas
    //

    function validarDatas() {
        const abertura   = new Date(form.abertura.value),
              entrega    = new Date(form.entrega.value),
              fechamento = new Date(form.fechamento.value);

        form.abertura.setCustomValidity(
              radioAbrirDepois.checked && abertura < new Date()
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
    radioAbrirAgora .addEventListener('change', validarDatas);
    radioAbrirDepois.addEventListener('change', validarDatas);

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

        const [horas, minutos] = form.esforcoMinutos.value.split(':');
        const dados = {
            professor:      form.professor.value,
            disciplina:     form.disciplina.value,
            titulo:         form.titulo.value,
            descricao:      form.descricao.value,
            esforcoMinutos: horas*60 + minutos,
            comNota:        form.comNota.value == 'true',
            abertura:       form.abertura.value,
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