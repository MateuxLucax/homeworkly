<?php

require_once $root . '/database/Query.php';

require_once $root . '/models/Tarefa.php';
require_once $root . '/models/Usuario.php';
require_once $root . '/models/Entrega.php';


class EntregaDAO
{
    public static function buscar(int $idAluno, int $idTarefa): ?Entrega
    {
        $sql =
        'SELECT conteudo
              , data_hora
              , em_definitivo
              , visto
              , nota
              , comentario
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
            ->setEmDefinitivo($e['em_definitivo'])
            ->setVisto($e['visto'])
            ->setNota($e['nota'])
            ->setComentario($e['comentario']);
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
}