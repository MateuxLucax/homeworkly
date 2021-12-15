<?php 

class Turma
{
    private int    $id;
    private string $nome;
    private int    $ano;
    private array  $disciplinas;
    private array  $alunos;

    public function setId(int $id): Turma {
        $this->id = $id;
        return $this;
    }

    public function setNome(string $nome): Turma {
        $this->nome = $nome;
        return $this;
    }

    public function setAno(int $ano): Turma {
        $this->ano = $ano;
        return $this;
    }

    public function setDisciplinas(array $disciplinas): Turma {
        $this->disciplinas = $disciplinas;
        return $this;
    }

    public function setAlunos(array $alunos): Turma {
        $this->alunos = $alunos;
        return $this;
    }

    public function getId(): int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getAno(): int { return $this->ano; }
    public function getDisciplinas(): array { return $this->disciplinas; }
    public function getAlunos(): array { return $this->alunos; }

    public function addDisciplina(Disciplina $disciplina): Turma {
        $this->disciplinas[] = $disciplina;
        return $this;
    }

    public function toArray(): array {
        $arr = [];
        if (isset($this->id)) $arr['id'] = $this->id;
        if (isset($this->nome)) $arr['nome'] = $this->nome;
        if (isset($this->ano)) $arr['ano'] = $this->ano;
        if (isset($this->disciplinas)) {
            $arr['disciplinas'] = [];
            foreach ($this->disciplinas as $disc) {
                $arrDisc = [];
                if (!is_null($disc?->getId())) $arrDisc['id'] = $disc->getId();
                if (!is_null($disc?->getNome())) $arrDisc['nome'] = $disc->getNome();
                if (!is_null($disc?->getProfessores())) {
                    $arrDisc['professores'] = [];
                    foreach ($disc->getProfessores() as $prof) {
                        $arrProf = [];
                        if (!is_null($prof?->getId())) $arrProf['id'] = $prof->getId();
                        if (!is_null($prof?->getNome())) $arrProf['nome'] = $prof->getNome();
                        if (!is_null($prof?->getLogin())) $arrProf['login'] = $prof->getLogin();
                        $arrProf['ultimo_acesso'] = $prof?->getUltimoAcesso();
                        $arrDisc['professores'][] = $arrProf;
                    }
                }
                $arr['disciplinas'][] = $arrDisc;
            }
        }
        if (isset($this->alunos)) {
            $arr['alunos'] = [];
            foreach ($this->alunos as $aluno) {
                $arrAluno = [];
                if (!is_null($aluno?->getId())) $arrAluno['id'] = $aluno->getId();
                if (!is_null($aluno?->getNome())) $arrAluno['nome'] = $aluno->getNome();
                if (!is_null($aluno?->getLogin())) $arrAluno['login'] = $aluno->getLogin();
                $arrAluno['ultimo_acesso'] = $aluno?->getUltimoAcesso();
                $arr['alunos'][] = $arrAluno;
            }
        }
        return $arr;
    }
}