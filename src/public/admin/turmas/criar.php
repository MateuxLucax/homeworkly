<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

require_once $root.'dao/UsuarioDAO.php';
require_once $root.'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

require_once $root.'database/Connection.php';
require_once $root.'utils/HttpCodes.php';
require_once $root.'dao/TurmaDAO.php';
require_once $root.'models/Turma.php';
require_once $root.'models/Disciplina.php';
require_once $root.'models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $view['title'] = 'Criar turma';
    $view['ativo-nav'] = 'turmas';
    require_once $root.'views/turmas/criar.php';
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $data = readJsonRequestBody();

    $turma = new Turma;

    $turma
        ->setNome($data['nome'])
        ->setAno($data['ano'])
        ->setDisciplinas(array_map(
            fn($disc) => (new Disciplina)
                ->setNome($disc['nome'])
                ->setTurma($turma)
                ->setProfessores(array_map(
                    fn($idProf) => (new Usuario)->setId($idProf),
                    $disc['professores']
                )),
            $data['disciplinas']))
        ->setAlunos(array_map(
            fn($idAluno) => (new Usuario)->setId($idAluno),
            $data['alunos']
        ));

    try {
        TurmaDAO::criar($turma);
        respondJson(HttpCodes::CREATED, ['id' => $turma->getId()]);
    } catch(Exception $e) {
        $pdo->rollBack();
        respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
    }
}
else
{
    respondJson(HttpCodes::METHOD_NOT_ALLOWED);
}