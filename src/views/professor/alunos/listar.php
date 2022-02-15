<div class="card pt-3">

    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">Disciplina</th>
                <th scope="col">Aluno</th>
                <th scope="col">Tarefas</th>
                <th scope="col">Nota média</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($view['alunos'] as $aluno) : ?>
                <tr>
                    <td><?= $aluno['disciplina'] ?></td>
                    <td><?= $aluno['aluno'] ?></td>
                    <td><?= $aluno['tarefas'] ?></td>
                    <td><?= $aluno['nota_media'] <= 0 ? '-' : $aluno['nota_media'] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>