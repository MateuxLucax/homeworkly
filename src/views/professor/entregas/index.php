<!DOCTYPE html>
<html lang="en">
<?php require $root . 'views/componentes/head.php' ?>
<body>

<?php
$tarefa = $view['tarefa'];
$entragasPorAluno = $view['entregasPorAluno'];

// Por padrão deixar a entrega do primeiro aluno aberta, caso contrário do que vier no GET (TODO)
$idAlunoEntregaAberta = $entregasPorAluno[0]['aluno']->getId()
?>

<!-- mostrar dados da tarefa em algum lugar aqui, onde?
     pelo menos o conteúdo; num modal? -->

<!-- layout 90% copiado de views/componentes/base.php :^) -->
<!-- essa página é pra ficar mais com esse layout próprio mesmo, para o professor focar em avaliar as entregas -->

<main class="min-vh-100">
    <div class="container-fluid">
        <div class="col-3 col-xxl-2 bg-light fixed-top">
            <div class="d-flex flex-column flex-shrink-0 vh-100">

                <ul class="list-group">
                    <?php foreach ($entregasPorAluno as $alunoEntrega) {
                        $aluno = $alunoEntrega['aluno'];
                        // TODO fazer navegação pelas entregas manualmente

                        echo '
                        <li class="list-group-item '.($aluno->getId() == $idAlunoEntregaAberta ? 'active' : '').'">
                            '.$alunoEntrega['aluno']->getNome().'
                        </li>';
                    } ?>
                </ul>

            </div>
        </div>
        <div class="col-9 offset-3 col-xxl-10 offset-xxl-2">
            <div class="container-xl p-3">
            
                <a class="btn btn-secondary" href="../tarefas/tarefa?id=<?=$tarefa->id()?>">
                    <i class="fas fa-arrow-left"></i>
                    Voltar para a tarefa
                </a>

                <?php foreach ($entregasPorAluno as $alunoEntrega) {
                    $aluno = $alunoEntrega['aluno'];
                    $entrega = $alunoEntrega['entrega']; ?>

                    <div class="card mt-3 <?= $aluno->getId() == $idAlunoEntregaAberta ? '' : 'd-none' ?>">
                        <div class="card-header">
                            <div class="card-title">Entrega</div>
                        </div>
                        <div class="card-body">

                            <?php
                            if ($entrega == null)
                            { ?>
                                <div class="alert alert-warning">
                                    Esse aluno <?= $tarefa->fechada() ? '' : 'ainda' ?> não realizou a entrega.
                                </div>
                            <?php }
                            else
                            { ?>

                                <!-- dados da entrega -->
                                <hr>
                                <!-- painel para professor avaliar entrega -->

                            <?php }
                            ?>

                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</main>

</body>
</html>