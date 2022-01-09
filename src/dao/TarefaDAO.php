<?php

require_once $root . '/models/Tarefa.php';
require_once $root . '/models/Usuario.php';
require_once $root . '/models/TipoUsuario.php';
require_once $root . '/database/Connection.php';
require_once $root . '/database/Query.php';
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/dao/DisciplinaDAO.php';

class TarefaDAO
{
    public static function buscar(int $id): ?Tarefa
    {
        $result = Query::select(
            'SELECT ta.id_tarefa
                  , pro.id_usuario AS id_professor
                  , pro.nome AS nome_professor
                  , di.id_disciplina
                  , di.nome AS nome_disciplina
                  , tu.id_turma
                  , tu.nome AS nome_turma
                  , tu.ano AS turma_ano
                  , ta.titulo
                  , ta.descricao
                  , ta.esforco_minutos
                  , ta.com_nota
                  , ta.abertura
                  , ta.entrega
                  , ta.fechamento
                  , ta.fechada
               FROM tarefa ta
               JOIN usuario pro ON ta.id_professor = pro.id_usuario
               JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
               JOIN turma tu ON di.id_turma = tu.id_turma
               WHERE id_tarefa = :id',
            ['id' => $id]
        );

        if (count($result) == 0) return null;

        $ta = $result[0];

        $tarefa = (new Tarefa)
            ->setId($ta['id_tarefa'])
            ->setTitulo($ta['titulo'])
            ->setDescricao($ta['descricao'])
            ->setEsforcoMinutos($ta['esforco_minutos'])
            ->setComNota($ta['com_nota'])
            ->setAbertura(new DateTime($ta['abertura']))
            ->setEntrega(new DateTime($ta['entrega']))
            ->setFechamento($ta['fechamento'] ? new DateTime($ta['fechamento']) : null)
            ->setFechadaManualmente($ta['fechada'])
            ->setProfessor((new Usuario)
                ->setId($ta['id_professor'])
                ->setNome($ta['nome_professor'])
                ->setTipo(TipoUsuario::PROFESSOR))
            ->setDisciplina((new Disciplina)
                ->setId($ta['id_disciplina'])
                ->setNome($ta['nome_disciplina'])
                ->setTurma((new Turma)
                    ->setId($ta['id_turma'])
                    ->setNome($ta['nome_turma'])
                    ->setAno($ta['turma_ano'])));

        return $tarefa;
    }
}