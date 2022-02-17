<?php

require_once $root . 'dao/DisciplinaDAO.php';

$professor = $_COOKIE['professor'];
$turma = isset($_COOKIE['turmaSelecionada']) ? $_COOKIE['turmaSelecionada'] : $_COOKIE['turmaInicial'];

$disciplinas = DisciplinaDAO::disciplinaDeTurmaProfessor($professor, $turma);
// TODO nvdd fazer um select das disciplinas do professor das turmas do ano atual

?>

<script>
    let botao = document.querySelector("#criar-modal-continuar");
    const escolherDisciplina = (disciplinaEl) => {
        botao.href = '/professor/tarefas/criar.php?disciplina=' + disciplinaEl.value;
    }
</script>

<div class="modal fade" id="criar-tarefa-modal" tabindex="-1" aria-labelledby="criar-tarefa" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="criar-tarefa">Criar tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Informe em qual disciplina você deseja criar a tarefa:</h6>
                <select class="form-select" onchange="escolherDisciplina(this)" aria-label="Opção inicial">

                    <?php foreach ($disciplinas as $disciplina) : ?>
                        <option value="<?= $disciplina->getId() ?>">
                            <?=$disciplina->getTurma()->getNome()?> – <?=$disciplina->getNome()?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a id="criar-modal-continuar" href="/professor/tarefas/criar.php?disciplina=<?= $disciplinas[0]->getId() ?>" class="btn btn-primary">Continuar</a>
            </div>
        </div>
    </div>
</div>