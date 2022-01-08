<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . '/dao/UsuarioDao.php';
require_once $root . '/models/TipoUsuario.php';
//UsuarioDao::validaSessaoTipo(TipoUsuario::PROFESSOR); // FIXME dá erro
UsuarioDao::validaSessao();

require_once $root . '/database/Query.php';

if (!isset($_GET['id'])) {
    respondWithNotFoundPage('<b>Erro do sistema:</b> nenhum <b>ID</b> de tarefa fornecido.');
}

$id = $_GET['id'];
$queryResult = Query::select(
    'SELECT ta.id_tarefa
          , pro.id_usuario as id_professor
          , pro.nome as nome_professor
          , di.id_disciplina
          , di.nome as nome_disciplina
          , tu.id_turma
          , tu.ano as ano_turma
          , tu.nome as nome_turma
          , ta.titulo
          , ta.descricao
          , ta.esforco_minutos
          , ta.com_nota
          , ta.abertura
          , ta.entrega
          , ta.fechamento
          , ta.fechada
       FROM tarefa ta
       JOIN usuario pro ON ta.id_professor = pro.id_usuario
       JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
       JOIN turma tu ON di.id_turma = tu.id_turma
      WHERE ta.id_tarefa = :id',
    ['id' => $id]
);

if (count($queryResult) == 0) {
    respondWithNotFoundPage("Tarefa de <b>id $id</b> não encontrada.");
}

$tarefa = $queryResult[0];

// bloquear o botão de alterar caso o professor acessando a tela não seja o que criou a tarefa

$view['tarefa'] = $tarefa;
$view['mostrar-botao-editar'] = true;
$view['pasta_usuario'] = TipoUsuario::pasta($_SESSION['tipo']);
require $root . '/views/tarefas/tarefa.php';