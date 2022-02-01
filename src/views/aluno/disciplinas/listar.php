<div class="card pt-3">

    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">Disciplina</th>
                <th scope="col">Professores</th>
                <th scope="col">Tarefas</th>
                <th scope="col">Nota média</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($view['disciplinas'] as $disciplina) : ?>
                <tr>
                    <td><?= $disciplina['disciplina'] ?></td>
                    <td><?= join(', ', array_map(fn($row) => $row->getNome(), $disciplina['professores']))  ?></td>
                    <td><?= $disciplina['tarefas'] ?></td>
                    <td><?= $disciplina['nota_media'] <= 0 ? '-' : $disciplina['nota_media'] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>