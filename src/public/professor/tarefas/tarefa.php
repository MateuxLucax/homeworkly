<?php

$root = '../../../';

require_once $root . 'utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

require_once $root . '/public/base/tarefas/tarefa.php';

$tarefa = buscarTarefaOuNotFound();
$permissao = new PermissaoTarefa($tarefa->id());
$permissaoVisualizar = $permissao->visualizar($_SESSION['id_usuario'], $_SESSION['tipo']);
if ($permissaoVisualizar != PermissaoTarefa::PODE) responsePermissaoNaoPode($permissaoVisualizar);

$sqlEntregas =
      'SELECT a.id_usuario AS aluno_id
            , a.nome AS aluno_nome
            , e.conteudo
            , e.visto
            , e.nota
            , e.data_hora
            , e.em_definitivo
            , e.comentario
            , (e.visto IS NULL AND e.nota IS NULL) AS avaliacao_pendente
            , (e.id_aluno IS NOT NULL) as entrega_feita
         FROM usuario a
         JOIN aluno_em_turma aet
           ON aet.id_aluno = a.id_usuario
          AND aet.id_turma = :idTurma
    LEFT JOIN entrega e
           ON e.id_aluno = a.id_usuario
          AND e.id_tarefa = :idTarefa
     ORDER BY e.em_definitivo, avaliacao_pendente DESC';

   // PostgreSQL: true < false

$turma = $tarefa->disciplina()->getTurma();

$entregasPorAluno = Query::select($sqlEntregas, [':idTarefa' => $tarefa->id(), ':idTurma' => $turma->getId() ]);

$entregasPorAluno = array_map(
    function(array $a) use ($tarefa) {
        $aluno = (new Usuario)
            ->setId($a['aluno_id'])
            ->setNome($a['aluno_nome'])
            ->setTipo(TipoUsuario::ALUNO);

        $entrega = null;
        if ($a['entrega_feita']) {
            $entrega = (new Entrega)
                ->setTarefa($tarefa)
                ->setAluno($aluno)
                ->setConteudo($a['conteudo'])
                ->setDataHora(DateUtil::toLocalDateTime($a['data_hora']))
                ->setEmDefinitivo($a['em_definitivo'])
                ->setVisto($a['visto'])
                ->setNota($a['nota'])
                ->setComentario($a['comentario']);
        }

        return [
            'aluno' => $aluno,
            'entrega' => $entrega
        ];
    },
    $entregasPorAluno
);

$view['tarefa'] = $tarefa;
$view['permissaoTarefa'] = $permissao;
$view['entregasPorAluno'] = $entregasPorAluno;

$view['content_path'] = 'views/tarefas/tarefa.php';
$view['sidebar_links'] = 'aluno/componentes/sidebar.php';

require_once $root . 'views/componentes/base.php';