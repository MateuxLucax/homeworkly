<?php

$root = '../../..';

require_once $root.'/utils/response-utils.php';
require_once $root.'/dao/TurmaDAO.php';
require_once $root.'/dao/UsuarioDAO.php';
require_once $root.'/models/Turma.php';
require_once $root.'/models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

require_once $root.'/database/Connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $view['id-turma'] = $_GET['id'];
    $view['title'] = 'Alterar turma';
    require_once $root.'/views/turmas/criar.php';
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $data = readJsonRequestBody();

    $turma = new Turma;
    $turma
        ->setId($data['id'])
        ->setNome($data['nome'])
        ->setAno($data['ano'])
        ->setDisciplinas(array_map(
            fn($disc) => (new Disciplina)
                ->setTurma($turma)
                ->setNome($disc['nome'])
                ->setProfessores(array_map(
                    fn($idProf) => (new Usuario)->setId($idProf),
                    $disc['professores']
                )),
            $data['disciplinas']
        ))
        ->setAlunos(array_map(
            fn($idAluno) => (new Usuario)->setId($idAluno),
            $data['alunos']
        ));

    $pdo = Connection::getInstance();

    $pdo->beginTransaction();
    try {
        TurmaDAO::alterar($turma);
        $pdo->commit();
        respondJson(HttpCodes::OK, ['id' => $turma->getId()]);
    } catch (Exception $e) {
        $pdo->rollBack();
        respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
    }
}
else
{
    respondJson(HttpCodes::METHOD_NOT_ALLOWED);
}
