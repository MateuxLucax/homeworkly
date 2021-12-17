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
    $view['ativo-nav'] = 'turmas';
    require_once $root.'/views/turmas/criar.php';
}
else if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $dados = readJsonRequestBody();

    $turma = new Turma;
    $turma
        ->setId($dados['id'])
        ->setNome($dados['nome'])
        ->setAno($dados['ano'])
        ->setAlunos(array_map(
            fn($idAluno) => (new Usuario)->setId($idAluno),
            $dados['alunos']
        ));
    
    foreach ($dados['disciplinas'] ?? [] as $dadosDisciplina) {
        $disciplina = (new Disciplina)
            ->setTurma($turma)
            ->setNome($dadosDisciplina['nome'])
            ->setProfessores(array_map(
                fn($idProf) => (new Usuario)->setId($idProf),
                $dadosDisciplina['professores']
            ));
        if (isset($dadosDisciplina['id'])) {
            $disciplina->setId($dadosDisciplina['id']);
        }
        $turma->addDisciplina($disciplina);
    }

    try {
        TurmaDAO::alterar($turma);
        respondJson(HttpCodes::OK, ['id' => $turma->getId()]);
    } catch (Exception $e) {
        respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
    }
}
else
{
    respondJson(HttpCodes::METHOD_NOT_ALLOWED);
}
