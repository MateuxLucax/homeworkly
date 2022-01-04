<!DOCTYPE html>
<html lang="pt-BR">
<?php require_once $root.'/views/componentes/head.php'; ?>
<body>

<!--TODO não em h1, e colocar links para a turma e para a disciplina
    TODO e também deixar num estilo mais breadcrumbs
    e fazer o mesmo na view turma.php-->

<main class="container">
    <div class="header mb-3">
        <h1><?=$view['ano']?> / <?=$view['turma_nome']?> / <b><?=$view['disciplina_nome']?></b></h1>
    </div>
    <form id="form-criar-tarefa">
        <input type="hidden" name="disciplina" value="<?= $view['disciplina_id'] ?>" />
        <input type="hidden" name="professor" value="<?= $view['professor_id'] ?>" />
        <div class="card mb-3">
            <div class="card-header">
                <span class="card-title">Criar tarefa</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="titulo">Título</label>
                    <input class="form-control" type="text" name="titulo" id="titulo" minlength="5" required/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="descricao">Descrição</label>
                    <textarea class="form-control" name="descricao" id="descricao"></textarea>
                </div>
                <div class="row">
                    <div class="mb-3 col-12 col-sm-4">
                        <label class="form-label" for="abertura" style="text-decoration: underline dotted;"  data-bs-toggle="tooltip" title="Quando a tarefa se torna disponível para os alunos.">
                            Data abertura
                            <i class="fas fa-question-circle"></i>
                        </label>
                        <input class="form-control" type="datetime-local" name="abertura" id="abertura" readonly required/>
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
                        <label class="form-label" for="entrega" style="text-decoration: underline dotted;" data-bs-toggle="tooltip" title="Opcional. Depois dela, entregas ficam marcadas como atrasadas.">
                            Data entrega
                            <i class="fas fa-question-circle"></i>
                        </label>
                        <input class="form-control" type="datetime-local" name="entrega" id="entrega" required />
                    </div>
                    <div class="mb-3 col-12 col-sm-4">
                        <label class="form-label" for="fechamento" style="text-decoration: underline dotted;" data-bs-toggle="tooltip" title="Opcional – pode fechar manualmente depois. Depois dela, alunos não podem mais fazer entregas.">
                            Data fechamento
                            <i class="fas fa-question-circle"></i>
                        </label>
                        <input class="form-control" type="datetime-local" name="fechamento" id="fechamento" />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label" for="esforcoMinutos">Estimativa de esforço</label>
                        <input class="form-control" type="time" name="esforcoMinutos" id="esforcoMinutos" required/>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="comNota">Avaliação</label>
                        <br/>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="comNota" value="1" id="avaliacao-nota" checked />
                            <label class="form-check-label" for="avaliacao-nota">Nota</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="comNota" value="0" id="avaliacao-visto" />
                            <label class="form-check-label" for="avaliacao-nota">Visto</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end">
            <button class="btn btn-outline-primary btn-lg" type="submit">
                <i class="fas fa-paper-plane"></i>
                Criar
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

    form.onsubmit = event => {
        event.preventDefault();

        const emptyToNull = x => x === '' ? null : x;

        const [horas, minutos] = form.esforcoMinutos.value.split(':');
        const dados = {
            professor:      form.professor.value,
            disciplina:     form.disciplina.value,
            titulo:         form.titulo.value,
            descricao:      form.descricao.value,
            esforcoMinutos: horas*60 + minutos,
            comNota:        form.comNota.value != 0,
            abertura:       form.abertura.value,
            entrega:        emptyToNull(form.entrega.value),
            fechamento:     emptyToNull(form.fechamento.value)
        };

        fetch('criar', {method: 'POST', body: JSON.stringify(dados)})
        .then(resp => {
            if (resp.status != 201) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro do sistema',
                    text: 'Não foi possível criar a tarefa'
                });
                resp.text().then(console.log);
                return;
            };
            resp.text().then(text => {
                try {
                    const ret = JSON.parse(text);
                    agendarAlertaSwal({
                        icon: 'success',
                        text: 'Tarefa criada com sucesso'
                    });
                    window.location.assign(`tarefa?id=${ret.id}`);
                } catch (e) {
                    console.error(e);
                    console.error(text);
                }
            })
        });
    };

</script>