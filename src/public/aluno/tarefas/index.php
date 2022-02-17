<?php
$root = '../../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'dao/TarefaDAO.php';
require_once $root . 'models/TipoUsuario.php';
require_once $root . 'models/Evento.php';
require_once $root . 'utils/SessionUtil.php';
require_once $root . 'dao/DisciplinaDAO.php';
require_once $root . 'database/Query.php';
require_once $root . 'models/Disciplina.php';
require_once $root . 'models/Tarefa.php';
require_once $root . 'models/Entrega.php';
require_once $root . 'models/Avaliacao.php';


$usuario = SessionUtil::usuarioLogado();

$sql = 'SELECT t.id_tarefa
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
          , d.id_disciplina AS id_disciplina
       FROM tarefa t
 INNER JOIN disciplina d ON t.id_disciplina = d.id_disciplina
 INNER JOIN turma tu ON tu.id_turma = d.id_turma
  LEFT JOIN entrega e   ON e.id_tarefa = t.id_tarefa AND e.id_aluno = :idAluno
  LEFT JOIN avaliacao a ON a.id_tarefa = t.id_tarefa AND a.id_aluno = :idAluno
      	AND tu.id_turma = :idTurma
      	AND t.abertura <= CURRENT_TIMESTAMP  -- Não listar as que ainda não foram abertas
   ORDER BY t.entrega, t.fechamento';

$turmaAtualAluno = TurmaDAO::turmaAtualDeAluno($usuario->getId());

$rows = Query::select($sql, [
    ':idAluno' => $usuario->getId(),
    ':idTurma' => $turmaAtualAluno->getId()
]);

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
        ->setDisciplina(DisciplinaDAO::buscar($t['id_disciplina']));

    $entrega = null;
    if ($t['entrega_feita']) {
        $entrega = (new Entrega)
            ->setTarefa($tarefa)
            ->setAluno($usuario)
            ->setDataHora(DateUtil::toLocalDateTime($t['data_hora']))
            ->setEmDefinitivo($t['em_definitivo']);
    }


    $avaliacao = null;
    if ($t['avaliacao_feita']) {
        $avaliacao = (new Avaliacao)
            ->setTarefa($tarefa)
            ->setAluno($usuario)
            ->setNota($t['nota'])
            ->setVisto($t['visto'])
            ->setComentario($t['comentario']);
    }

    $situacao = $tarefa->entregaSituacao($entrega);
    $situacaoIndex = match ($situacao) {
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

$view['title'] = 'Tarefas';
$view['content_path'] = 'views/aluno/tarefas/listar.php';
$view['sidebar_links'] = 'aluno/componentes/sidebar.php';
$view['tarefasPorSituacao'] = $tarefasPorSituacao;

require_once $root . 'views/componentes/base.php';
