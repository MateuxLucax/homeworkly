<?php foreach ($view['turma'] as $turma) : ?>
<div class="card pt-3 mb-3">
    <h2><?= $turma['nome'] ?></h2>
    <?php foreach (json_decode($turma['disciplinas'], true) as $disciplina) : ?>
    <h5><?= $disciplina['nome'] ?></h5>
    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">Aluno</th>
                <th scope="col">Tarefas</th>
                <th scope="col">Nota média</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($disciplina['alunos'] as $aluno) : ?>
            <tr>
                <td><?= $aluno['nome'] ?></td>
                <td><?= $aluno['tarefas'] ?></td>
                <td><?= $aluno['nota_media'] <= 0 ? '-' : $aluno['nota_media'] ?></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php endforeach ?>
</div>
<?php endforeach ?>