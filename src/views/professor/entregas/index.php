<!DOCTYPE html>
<html lang="en">
<?php require $root . 'views/componentes/head.php' ?>
<body>

<?php
$tarefa = $view['tarefa'];
$entragasPorAluno = $view['entregasPorAluno'];

$arquivada = $tarefa->estado() == TarefaEstado::ARQUIVADA;

$idAlunoEntregaAberta = isset($_GET['aluno']) ? $_GET['aluno'] : $entregasPorAluno[0]['aluno']->getId()
?>

<!-- layout 90% copiado de views/componentes/base.php :^) -->
<!-- essa página é pra ficar mais com esse layout próprio mesmo, para o professor focar em avaliar as entregas -->

<main class="min-vh-100">
  <div class="container-fluid">
    <div class="col-3 col-xxl-2 bg-light fixed-top">
      <div class="d-flex flex-column flex-shrink-0 vh-100">

        <p class="text-center m-3">Selecione o aluno</p>

        <ul class="list-group">
          <?php foreach ($entregasPorAluno as $alunoEntrega) {
            $aluno = $alunoEntrega['aluno'];
            echo '
            <li style="cursor: pointer;" data-id="'.$aluno->getId().'"
              class="link-aluno list-group-item '.($aluno->getId() == $idAlunoEntregaAberta ? 'active' : '').'"
            >
              '.$alunoEntrega['aluno']->getNome().'
            </li>';
          } ?>
        </ul>

      </div>
    </div>
    <div class="col-9 offset-3 col-xxl-10 offset-xxl-2">
      <div class="container-xl p-3">

        <div class="card mt-3">
          <div class="card-body">
            <h5 class="card-title">
              <?= $tarefa->titulo() ?>
            </h5>
            <p>
              <?= $tarefa->descricao() ?>
            </p>
            <a class="btn btn-secondary" href="../tarefas/tarefa?id=<?=$tarefa->id()?>">
              <i class="fas fa-arrow-left"></i>
              Voltar para a tarefa
            </a>
          </div>
        </div>

        <?php foreach ($entregasPorAluno as $alunoEntrega) {
          $aluno = $alunoEntrega['aluno'];
          $entrega = $alunoEntrega['entrega'];
          $avaliacao = $alunoEntrega['avaliacao']; ?>

          <div id="card-entrega-aluno-<?=$aluno->getId()?>"
             class="card-entrega card mt-3 <?= $aluno->getId() == $idAlunoEntregaAberta ? '' : 'd-none' ?>"
          >
            <div class="card-header">
              <div class="card-title">
                Entrega
                <!-- TODO dizer quando a tarefa foi entregue com atraso -->
                <?php if ($entrega != null): ?>
                  &nbsp;
                  <small class="text-muted">
                    (feita em <i><?=$entrega->dataHora()->format('d/m/Y H:i')?></i>)
                  </small>
                <?php endif; ?>
              </div>
            </div>
            <div class="card-body">

              <?php
              if ($entrega == null || ( !$entrega->emDefinitivo() && $tarefa->fechada())) {
                echo '
                <div class="alert alert-warning">
                  Esse aluno '.($tarefa->fechada() ? '' : 'ainda').' não realizou a entrega.
                </div>';
              }
              else { 
                if (!$entrega->emDefinitivo()) {
                  echo '
                  <div class="alert alert-warning">
                    A entrega ainda não foi feita em definitivo.
                  </div>';
                }
              } ?>

              <?php if ($entrega != null && !empty($entrega->conteudo())): ?>
                <textarea readonly disabled class="mb-3 form-control"
                ><?=$entrega->conteudo()?></textarea>
                <hr>
              <?php endif; ?>

              <form class="form-avaliar-entrega" data-id-aluno="<?=$aluno->getId()?>">
                <input type="hidden" name="tarefa" value="<?=$tarefa->id()?>">
                <input type="hidden" name="aluno" value="<?=$aluno->getId()?>">
                <?php if ($tarefa->comNota())
                { ?>
                  <label class="form-label" for="nota">
                    Nota
                    <?= $arquivada ? '' : '<small><span class="text-muted">(usar ponto, por exemplo 9.5 em vez de 9,5)</span></small>' ?>
                  </label>
                  <div class="input-group mb-3" style="width: 100px">
                    <input id="nota" class="text-end form-control" type="text"
                           pattern="((0*\d)(\.\d+)?)|(10)" align="left" required
                           value="<?=$avaliacao?->nota()?>"
                           <?= $arquivada ? 'readonly disabled' : '' ?>
                    />
                    <span class="input-group-text">/10</span>
                  </div>
                <?php }
                else
                { ?>
                  <!-- TODO mais bonito: cores (success, danger) e ícones (fa-check, fa-times), dentro de badge -->
                  <div class="form-check">
                    <input type="radio" class="form-check-input" name="visto" value="true" id="visto-true"
                            <?= $avaliacao?->visto() !== false ? 'checked' : '' ?>>
                    <label for="visto-true">Visto</label>
                  </div>
                  <div class="mb-3 form-check">
                    <input type="radio" class="form-check-input" name="visto" value="false" id="visto-false"
                            <?= $avaliacao?->visto() === false ? 'checked' : '' ?>>
                    <label for="visto-false">Não aceito</label>
                  </div>
                <?php } ?>
                <label class="form-label" for="comentario">Comentário</label>
                <textarea id="comentario" class="form-control" name="comentario"
                      <?= $arquivada ? 'readonly disabled' : '' ?>
                ><?=$avaliacao?->comentario()?></textarea>

                <?php if (!$arquivada): ?>
                  <button type="submit" class="mt-3 btn btn-outline-primary float-end">
                    <i class="fas fa-paper-plane"></i>
                    Salvar
                  </button>
                <?php endif; ?>
              </form>
            </div>
          </div>
        <?php } ?>

      </div>
    </div>
  </div>
</main>

<script>
  let   linkAlunoAtual   = document.querySelector('.link-aluno.active');
  let   cardEntregaAtual = document.querySelector('.card-entrega:not(.d-none)');
  const linksAlunos      = document.getElementsByClassName('link-aluno');

  for (const link of linksAlunos) {
    link.onclick = () => {
      linkAlunoAtual.classList.remove('active');
      link.classList.add('active');
      linkAlunoAtual = link;
      cardEntregaAtual.classList.add('d-none');
      const id = link.getAttribute('data-id');
      const cardEntrega = document.getElementById(`card-entrega-aluno-${id}`);
      cardEntrega.classList.remove('d-none');
      cardEntregaAtual = cardEntrega;
    }
  }


  for (const form of document.getElementsByClassName('form-avaliar-entrega')) {
    form.onsubmit = async e => {
      e.preventDefault();
      const body = {
        tarefa:     form.tarefa.value,
        aluno:      form.aluno.value,
        comentario: form.comentario.value
      };
      <?php if ($tarefa->comNota()): ?>
        body.nota = form.nota.value;
      <?php else: ?>
        body.visto = form.visto.value;
      <?php endif; ?>

      console.log(body);

      const response = await fetch('avaliar', {
        method: 'POST',
        body: JSON.stringify(body)
      });
      const text = await response.text();
      try {
        const json = JSON.parse(text);

        Swal.fire({
          icon: response.status == 200 ? 'success' : 'error',
          text: json.message
        });

        if (json.hasOwnProperty('exception')) {
          console.log(json.exception);
        }

      } catch (e) {
        console.error(e, '\n', text);
      }
    }
  }
</script>

</body>
</html>