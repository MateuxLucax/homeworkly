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
                    <input class="form-control" type="text" name="titulo" id="titulo"/>
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
                        <input class="form-control" type="datetime-local" name="abertura" id="abertura" readonly/>
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
                        <input class="form-control" type="datetime-local" name="entrega" id="entrega" />
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
                        <label class="form-label" for="esforcoHoras">Estimativa de esforço</label>
                        <input class="form-control" type="time" name="esforcoHoras" id="esforcoHoras" />
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
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

    // TODO usar masks nos inputs de data e de esforço em horas

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

    radioAbrirAgora.onchange  = () => { trocarTipoAbertura('agora'); }
    radioAbrirDepois.onchange = () => { trocarTipoAbertura('depois'); }

    //
    // Envio do formulário
    //

    const form = document.getElementById('form-criar-tarefa');

    form.onsubmit = event => {
        event.preventDefault();

        // TODO validação

        // TODO fazer a request
    };

</script>