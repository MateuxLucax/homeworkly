<?php



// Em progresso

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

require_once $root . '/database/DisciplinaDAO.php';
require_once $root . '/database/TarefaDAO.php';
require_once $root . '/database/Connection.php';

if (empty($_GET['id'])) {
    respondWithNotFoundPage('<b>Erro do sistema:</b> A página de disciplina foi acessada sem nenhum ID fornecido.');
}

$id = $_GET['id'];

$disciplina = DisciplinaDAO::buscar($id);

if ($disciplina == null) {
    respondWithNotFoundPage('Não existe disciplina com ID <b>'.$id.'</b>');
}

DisciplinaDAO::popularComProfessores($disciplina);

// Indexa os professores pelos seus IDs para facilitar a associação com as tarefas abaixo

$professoresPorId = [];
foreach ($disciplina->getProfessores() as $professor) {
    $professoresPorId[ $professor->getId() ] = $professor;
}

// TODO para colocar as tarefas em cada índice (atrasada, pendente ou entregue)
// instanciar elas após o select para poder chamar ->entregaSituacao() da tarefa

$tarefasPorSituacao = [
    'atrasadas' => [],
    'pendentes' => [],
    'entregues' => []
];

$sql =
'SELECT t.id_tarefa
      , t.id_professor
      , t.titulo
      , t.descricao
      , t.esforco_minutos
      , t.com_nota
      , t.abertura
      , t.entrega
      , t.fechamento
   FROM tarefa t
  WHERE t.id_disciplina = :id';

$pdo = Connection::getInstance();

$stmt = $pdo->prepare($sql, [':id' => $disciplina->getId()]);

while ($t = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $tarefa = (new Tarefa)
        ->setId($t['id_tarefa'])
        ->setTitulo($t['titulo'])
        ->setDescricao($t['descricao'])
        ->setEsforcoMinutos($t['esforco_minutos'])
        ->setDataHoraAbertura(DateUtil::toLocalDateTime($t['abertura']))
        ->setDataHoraEntrega(DateUtil::toLocalDateTime($t['entrega']))
        ->setDataHoraFechamento($ta['fechamento'] ? DateUtil::toLocalDateTime($ta['fechamento']) : null)
        ->setProfessor($professoresPorId[$t['id_professor']]);

    // Buscar entrega do aluno
    // Gerar valor em enum da situação aluno-tarefa
    // Adicionar no $tarefasPorSituacao
}