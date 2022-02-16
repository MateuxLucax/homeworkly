<?php

$root = '../../../';

require_once $root . 'utils/response-utils.php';
forbidMethodsNot('POST');
//Comentado para testar
//require_once $root . 'dao/UsuarioDAO.php';
//require_once $root . 'models/TipoUsuario.php';
//UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

// -------------------------------------------------------

require_once $root . 'utils/DateUtil.php';
require_once $root . 'database/Query.php';

$dados = readJsonRequestBody();

foreach (['idDisciplina', 'abertura', 'entrega', 'esforcoMinutos'] as $campoEsperado) {
    if (!array_key_exists($campoEsperado, $dados)) respondJson(
        HttpCodes::BAD_REQUEST,
        ['message' => 'O campo '.$campoEsperado.' não foi fornecido no corpo da request']
    );
}

$resIdTurma = Query::select(
    'SELECT id_turma FROM disciplina WHERE id_disciplina = :id',
    [':id' => $dados['idDisciplina']]
);
if (empty($resIdTurma)) respondJson(
    HttpCodes::NOT_FOUND,
    ['message' => 'Não existe disciplina com ID '.$dados['idDisciplina']]
);
$idTurma = (int) $resIdTurma[0]['id_turma'];

// TODO encapsular cálculo de esforço num objeto, reutilizar para validar a tarefa nos endpoints de criação e alteração

$sqlTarefasAdjacentes = '
    SELECT ta.abertura
         , ta.entrega
         , ta.esforco_minutos
      FROM tarefa ta
      JOIN disciplina di ON di.id_disciplina = ta.id_disciplina
      JOIN turma tu ON tu.id_turma = :idTurma
     WHERE ta.entrega >= :abertura
       AND ta.abertura <= :entrega
';

// Esse WHERE traz todas as tarefas da mesma turma com período que intercede
// o período da tarefa que está sendo criada, seja da forma que for:
//   ****    ******     **      ****
// ####       ####     ####       ####

    $tarefas = Query::select(
    $sqlTarefasAdjacentes,
    [ ':idTurma' => $idTurma
    , ':abertura' => $dados['abertura'].' 00:00:00'
    , ':entrega' => $dados['entrega'].' 23:59:59'
    ]
);

// TODO lidar com o caso em que não há tarefas adjacentes

// Incluir tarefa que está sendo criada
$tarefas[] = [
      'abertura'        => $dados['abertura']
    , 'entrega'         => $dados['entrega']
    , 'esforco_minutos' => $dados['esforcoMinutos']
];

for ($i = 0; $i < count($tarefas); $i++) {
    $tab = DateUtil::toLocalDateTime($tarefas[$i]['abertura']);
    $ten = DateUtil::toLocalDateTime($tarefas[$i]['entrega']);
    $tab->setTime(0, 0, 0);
    $ten ->setTime(0, 0, 0);
    $tarefas[$i]['abertura'] = $tab;
    $tarefas[$i]['entrega']  = $ten;
}

// Datas que especificam o período a ser considerado
$menorData = $tarefas[0]['abertura'];
$maiorData = $tarefas[0]['entrega'];

for ($i = 1; $i < count($tarefas); $i++) {
    if ($tarefas[$i]['abertura'] < $menorData) $menorData = $tarefas[$i]['abertura'];
    if ($tarefas[$i]['entrega']  > $maiorData) $maiorData = $tarefas[$i]['entrega'];
}

function diffDias(DateTime $dataMaior, DateTime $dataMenor) {
    return (int) $dataMaior->diff($dataMenor)->format('%a');
}

$minutosPorDia = [];
$qtdDiasPeriodo = diffDias($maiorData, $menorData);

for ($i = 0; $i < $qtdDiasPeriodo; $i++) {
    $minutosPorDia[$i] = 0.0;
}

foreach ($tarefas as $tarefa) {
    $indiceDiaInicio = diffDias($tarefa['abertura'], $menorData);
    $qtdDiasTarefa = diffDias($tarefa['entrega'], $tarefa['abertura']);
    $minutosPorDiaTarefa = $tarefa['esforco_minutos'] / $qtdDiasTarefa;

    for ($i = 0; $i < $qtdDiasTarefa; $i++) {
        $minutosPorDia[ $indiceDiaInicio + $i ] += $minutosPorDiaTarefa;
    }
}

$mediaMinutosPorDia = array_sum($minutosPorDia) / count($minutosPorDia);

// Dado um valor threshold, tipo 4 horas por dia,
// se a média for maior que isso, retornar que não pode,
// se houver algum dia com mais que 5 horas de trabalho, retornar que não pode
// (threshold * 1.2 ou algo assim)
// caso contrário retornar que pode

// junto retornar qual ficou a média de trabalho por dia, só pra ficar mais informativo pro professor
// mas essa média só considerando o período da tarefa
// porque nvdd o $mediaMinutosPorDia tá incompleto pq não considera outras tarefas no período
// de $menorData a $maiorData que não _intercedam_ a tarefa criada

