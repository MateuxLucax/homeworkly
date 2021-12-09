<?php

$root = '../../../';

require_once $root.'database/Connection.php';
require_once $root.'database/Query.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'controllers/UsuarioController.php';
require_once $root.'utils/response-utils.php';
require_once $root.'utils/HttpCodes.php';

UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$headers = getallheaders();
$retornarJson = $headers['Accept'] == 'application/json';

if (!isset($_GET['id'])) {
  if ($retornarJson) {
    respondJson(HttpCodes::BAD_REQUEST, ['mensagem' => 'Não foi informado ID']);
  } else {
    header('Location: listar');
  }
}

$id = $_GET['id'];

if (!is_numeric($id)) {
  if ($retornarJson) {
    respondJson(HttpCodes::NOT_FOUND, ['mensagem' => $id.' não é um id válido.']);
  } else {
    respondWithNotFoundPage("<b>$id</b> não é um id válido.");
  }
}

$resTurma = Query::select('SELECT id_turma AS id, nome, ano FROM turma WHERE id_turma = :id', [':id' => $id]);

if (count($resTurma) == 0) {
  if ($retornarJson) {
    respondJson(HttpCodes::NOT_FOUND, ['mensagem' => 'A turma de id '.$id.' não foi encontrada']);
  } else {
    respondWithNotFoundPage("A turma de id <b>$id</b> não foi encontrada.");
  }
}

$turma = $resTurma[0];

$turma['alunos'] = Query::select(
    'SELECT al.id_usuario AS id, al.nome, al.login, al.ultimo_acesso
       FROM usuario al
       JOIN aluno_em_turma alt
         ON alt.id_aluno = al.id_usuario
        AND alt.id_turma = :id'
  , [':id' => $id]
);

$turma['disciplinas'] = Query::select(
      'SELECT id_disciplina AS id, nome FROM disciplina WHERE id_turma = :id'
    , [':id' => $id]
);

for ($i = 0; $i < count($turma['disciplinas']); $i++) {
  $turma['disciplinas'][$i]['professores'] = Query::select(
        'SELECT p.id_usuario AS id, p.nome, p.login, p.ultimo_acesso
           FROM usuario p
           JOIN professor_de_disciplina pd
             ON pd.id_professor = p.id_usuario
            AND pd.id_disciplina = :id'
      , [':id' => $turma['disciplinas'][$i]['id']]
    );
}

if ($retornarJson) {
  respondJson(HttpCodes::OK, $turma);
} else {
  $view['turma'] = $turma;
  $view['title'] = 'Turma';
  require_once $root.'views/turmas/turma.php';
}

