<?php

$root = '../../../';

require_once $root . 'utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

require_once $root . 'dao/DisciplinaDAO.php';
require_once $root . 'dao/TarefaDAO.php';
require_once $root . 'database/Query.php';
require_once $root . 'utils/SessionUtil.php';
require_once $root . 'models/Disciplina.php';
require_once $root . 'models/Tarefa.php';
require_once $root . 'models/Entrega.php';
require_once $root . 'models/Avaliacao.php';

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

$tarefasPorSituacao = [
    'atrasadas'  => [],
    'pendentes'  => [],
    'entregues'  => [],
    'nao-feitas' => []
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
          , e.data_hora
          , e.em_definitivo
          , a.visto
          , a.nota
          , a.comentario
          , e.id_tarefa IS NOT NULL as entrega_feita
          , a.id_tarefa IS NOT NULL as avaliacao_feita
       FROM tarefa t
  LEFT JOIN entrega e   ON e.id_tarefa = t.id_tarefa AND e.id_aluno = :idAluno
  LEFT JOIN avaliacao a ON a.id_tarefa = t.id_tarefa AND a.id_aluno = :idAluno
      WHERE t.id_disciplina = :id
        AND t.abertura <= CURRENT_TIMESTAMP  -- Não listar as que ainda não foram abertas
   ORDER BY t.entrega, t.fechamento';

$rows = Query::select($sql, [
    ':id' => $disciplina->getId(),
    ':idAluno' => $_SESSION['id_usuario']
]);

$alunoLogado = SessionUtil::usuarioLogado();

foreach ($rows as $t) {
    $tarefa = (new Tarefa)
        ->setId($t['id_tarefa'])
        ->setTitulo($t['titulo'])
        ->setDescricao($t['descricao'])
        ->setEsforcoMinutos($t['esforco_minutos'])
        ->setComNota($t['com_nota'])
        ->setDataHoraAbertura(DateUtil::toLocalDateTime($t['abertura']))
        ->setDataHoraEntrega(DateUtil::toLocalDateTime($t['entrega']))
        ->setDataHoraFechamento($t['fechamento'] ? DateUtil::toLocalDateTime($t['fechamento']) : null)
        ->setDisciplina($disciplina);
    
    if (array_key_exists($t['id_professor'], $professoresPorId)) {
        $tarefa->setProfessor($professoresPorId[$t['id_professor']]);
    }

    $entrega = null;
    if ($t['entrega_feita']) {
        $entrega = (new Entrega)
            ->setTarefa($tarefa)
            ->setAluno($alunoLogado)
            ->setDataHora(DateUtil::toLocalDateTime($t['data_hora']))
            ->setEmDefinitivo($t['em_definitivo']);
    }


    $avaliacao = null;
    if ($t['avaliacao_feita']) {
        $avaliacao = (new Avaliacao)
            ->setTarefa($tarefa)
            ->setAluno($alunoLogado)
            ->setNota($t['nota'])
            ->setVisto($t['visto'])
            ->setComentario($t['comentario']);
    }

    $situacao = $tarefa->entregaSituacao($entrega);
    $situacaoIndex = match($situacao) {
        EntregaSituacao::PENDENTE_ATRASADA => 'atrasadas',
        EntregaSituacao::PENDENTE          => 'pendentes',
        EntregaSituacao::ENTREGUE_ATRASADA => 'entregues',
        EntregaSituacao::ENTREGUE          => 'entregues',
        EntregaSituacao::NAO_FEITA         => 'nao-feitas'
    };

    $tarefasPorSituacao[$situacaoIndex][] = [
        'tarefa'    => $tarefa,
        'avaliacao' => $avaliacao,
        'entrega'   => $entrega
    ];
}

$view['title'] = $disciplina->getNome();

$view['tarefasPorSituacao'] = $tarefasPorSituacao;
$view['disciplina'] = $disciplina;

$view['content_path'] = 'views/aluno/disciplinas/disciplina.php';
$view['sidebar_links'] = 'aluno/componentes/sidebar.php';

require_once $root . 'views/componentes/base.php';