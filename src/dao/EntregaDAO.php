<?php

require_once $root . '/database/Query.php';

require_once $root . '/models/Tarefa.php';
require_once $root . '/models/Usuario.php';
require_once $root . '/models/Entrega.php';
require_once $root . '/models/Avaliacao.php';


class EntregaDAO
{
    public static function buscar(int $idAluno, int $idTarefa): ?Entrega
    {
        $sql =
        'SELECT conteudo
              , data_hora
              , em_definitivo
           FROM entrega
          WHERE (id_aluno, id_tarefa)
              = (:idAluno, :idTarefa)';

        $params = [
            ':idAluno' => $idAluno,
            ':idTarefa' => $idTarefa,
        ];

        $result = Query::select($sql, $params);
        
        if (count($result) == 0) {
            return null;
        }

        $e = $result[0];

        return (new Entrega)
            ->setTarefa((new Tarefa)->setId($idTarefa))
            ->setAluno((new Usuario)->setId($idAluno))
            ->setConteudo($e['conteudo'])
            ->setDataHora(DateUtil::toLocalDateTime($e['data_hora']))
            ->setEmDefinitivo($e['em_definitivo']);
    }

    public static function alterar(Entrega $entrega): bool
    {
        $sql =
        'UPDATE entrega
            SET conteudo = :conteudo
              , data_hora = :dataHora
              , em_definitivo = :emDefinitivo
         WHERE (id_aluno, id_tarefa) = (:idAluno, :idTarefa)';

        $params = [
            ':idAluno'      => $entrega->aluno()->getId(),
            ':idTarefa'     => $entrega->tarefa()->id(),
            ':conteudo'     => $entrega->conteudo(),
            ':dataHora'     => $entrega->dataHora()->format('Y-m-d H:i:s'),
            ':emDefinitivo' => $entrega->emDefinitivo() ?'true': 'false'
        ];

        return Query::execute($sql, $params);
    }

    public static function criar(Entrega $entrega): bool
    {
        $sql =
        'INSERT INTO entrega (id_tarefa, id_aluno, conteudo, data_hora, em_definitivo)
         VALUES (:idTarefa, :idAluno, :conteudo, :dataHora, :emDefinitivo)';

        $params = [
            ':idTarefa'     => $entrega->tarefa()->id(),
            ':idAluno'      => $entrega->aluno()->getId(),
            ':conteudo'     => $entrega->conteudo(),
            ':dataHora'     => $entrega->dataHora()->format('Y-m-d H:i:s'),
            ':emDefinitivo' => $entrega->emDefinitivo() ? 'true' : 'false',
        ];

        return Query::execute($sql, $params);
    }

    /**
     * Retorna todos os alunos da turma e, para cada, sua entrega na tarefa dada (ou null se não fez) e a avaliação do professor (ou null se não fez).
     * Ordem: primeiro as entregas em definitivo e depois as pendentes, e dentro de cada segmento primeiro as que ainda não foram avaliadas.
     * 
     * @param Tarefa $tarefa (deve ter ->disciplina(), e a disciplina deve ter ->getTurma())
     * 
     * @return array [['aluno' => objAluno, 'entrega' => objEntrega, 'avaliacao' => objAvaliacao], ...]
     */
    public static function entregasPorAluno(Tarefa $tarefa): array
    {
        $sqlEntregas =
            'SELECT u.id_usuario AS aluno_id
                  , u.nome AS aluno_nome
                  , e.conteudo
                  , e.data_hora
                  , e.em_definitivo
                  , a.visto
                  , a.nota
                  , a.comentario
                  , (a.visto IS NULL AND a.nota IS NULL) AS avaliacao_pendente
                  , (e.id_aluno IS NOT NULL) as entrega_feita
                  , (a.id_aluno IS NOT NULL) as avaliacao_feita
              FROM usuario u
              JOIN aluno_em_turma aet
                ON aet.id_aluno = u.id_usuario
               AND aet.id_turma = :idTurma
         LEFT JOIN entrega e
                ON e.id_aluno = u.id_usuario
               AND e.id_tarefa = :idTarefa
         LEFT JOIN avaliacao a
                ON a.id_aluno = u.id_usuario
               AND a.id_Tarefa = :idTarefa
          ORDER BY e.em_definitivo, avaliacao_pendente DESC';  // PostgreSQL: true < false

        $entregasPorAluno = Query::select($sqlEntregas, [':idTarefa' => $tarefa->id(), ':idTurma' => $tarefa->disciplina()->getTurma()->getId() ]);

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
                        ->setEmDefinitivo($a['em_definitivo']);
                }
                $avaliacao = null;
                if ($a['avaliacao_feita']) {
                    $avaliacao = (new Avaliacao)
                        ->setTarefa($tarefa)
                        ->setAluno($aluno)
                        ->setVisto($a['visto'])
                        ->setNota($a['nota'])
                        ->setComentario($a['comentario']);
                }

                return [
                    'aluno' => $aluno,
                    'entrega' => $entrega,
                    'avaliacao' => $avaliacao
                ];
            },
            $entregasPorAluno
        );

        return $entregasPorAluno;
    }
}