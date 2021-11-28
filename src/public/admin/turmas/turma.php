<?php

$root = '../../../';

require_once $root.'database/Connection.php';
require_once $root.'database/Query.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'controllers/UsuarioController.php';
require_once $root.'utils/response-utils.php';
require_once $root.'utils/HttpCodes.php';

UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

if (!isset($_GET['id'])) {
    header('Location: listar');
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    respondWithNotFoundPage("<b>$id</b> não é um id válido.");
}

$resTurma = Query::select('SELECT nome, ano FROM turma WHERE id_turma = :id', [':id' => $id]);

if (count($resTurma) == 0) {
    respondWithNotFoundPage("A turma de id <b>$id</b> não foi encontrada.");
}

$turma = $resTurma[0];

$view['id']   = $id;
$view['nome'] = $turma['nome'];
$view['ano']  = $turma['ano'];

$sqlAlunos = '
  SELECT u.id_usuario, u.nome, u.login, u.ultimo_acesso
    FROM usuario u
    JOIN aluno_em_turma ut
      ON ut.id_aluno = u.id_aluno
     AND ut.id_turma = :id
';

$view['alunos'] = Query::select(
      'SELECT al.id_usuario AS id, al.nome, al.login, al.ultimo_acesso
         FROM usuario al
         JOIN aluno_em_turma alt
           ON alt.id_aluno = al.id_usuario
          AND alt.id_turma = :id'
    , [':id' => $id]
);

$view['disciplinas'] = Query::select(
      'SELECT id_disciplina AS id, nome FROM disciplina WHERE id_turma = :id'
    , [':id' => $id]
);

foreach ($view['disciplinas'] as &$disciplina) {
    $disciplina['professores'] = Query::select(
          'SELECT p.id_usuario AS id, p.nome, p.login, p.ultimo_acesso
             FROM usuario p
             JOIN professor_de_disciplina pd
               ON pd.id_professor = p.id_usuario
              AND pd.id_disciplina = :id'
        , [':id' => $disciplina['id']]
    );
}

$view['title'] = 'Turma';

//var_dump($view);
require_once $root.'views/turmas/turma.php';
