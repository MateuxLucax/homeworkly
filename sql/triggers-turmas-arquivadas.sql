CREATE OR REPLACE FUNCTION ano_anterior(ano INT) RETURNS BOOLEAN AS $$
BEGIN
	RETURN ano < date_part('year', current_date);
END
$$ LANGUAGE plpgsql;

-- Não pode alterar ou deletar turma arquivada

CREATE OR REPLACE FUNCTION proibe_alterar_turma_arquivada() RETURNS TRIGGER AS $$
BEGIN
	IF ano_anterior(OLD.ano) THEN
		RAISE EXCEPTION 'Turmas arquivadas não podem ser alteradas nem deletadas';
	ELSE 
		RETURN NEW;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_alterar_turma_arquivada_update
BEFORE UPDATE ON turma
FOR EACH ROW EXECUTE PROCEDURE proibe_alterar_turma_arquivada();

CREATE or replace TRIGGER tr_proibe_alterar_turma_arquivada_delete
BEFORE DELETE ON turma
FOR EACH ROW EXECUTE PROCEDURE proibe_alterar_turma_arquivada();

-- Não pode criar turma em ano diferente

CREATE OR REPLACE FUNCTION proibe_criar_turma_em_ano_diferente() RETURNS TRIGGER AS $$
DECLARE
	ano_atual INT := date_part('year', current_date);
BEGIN
	IF NEW.ano <> ano_atual THEN
		RAISE EXCEPTION 'Turmas devem ser criadas no ano atual';
	ELSE 
		RETURN NEW;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_criar_turma_em_ano_diferente
BEFORE INSERT ON turma
FOR EACH ROW EXECUTE PROCEDURE proibe_criar_turma_em_ano_diferente();

/* ------------------------------------------------------- */
-- Não pode inserir ou deletar aluno em turma arquivada

CREATE OR REPLACE FUNCTION proibe_deletar_aluno_em_turma_arquivada() RETURNS TRIGGER AS $$
declare
	ano_turma int;
begin
	select ano into ano_turma from turma where id_turma = old.id_turma;
	IF ano_anterior(ano_turma) THEN
	    RAISE EXCEPTION 'Turmas arquivadas não podem ter alunos deletados';
	ELSE
		RETURN OLD;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_deletar_aluno_em_turma_arquivada
BEFORE DELETE ON aluno_em_turma
FOR EACH ROW EXECUTE PROCEDURE proibe_deletar_aluno_em_turma_arquivada();


CREATE OR REPLACE FUNCTION proibe_inserir_aluno_em_turma_arquivada() RETURNS TRIGGER AS $$
declare 
	ano_turma int;
begin
	SELECT ano into ano_turma FROM turma WHERE id_turma = NEW.id_turma;
	IF ano_anterior(ano_turma) THEN
	    RAISE EXCEPTION 'Turmas arquivadas não podem ter alunos inseridos';
	ELSE
		RETURN NEW;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_inserir_aluno_em_turma_arquivada
BEFORE INSERT ON aluno_em_turma
FOR EACH ROW EXECUTE PROCEDURE proibe_inserir_aluno_em_turma_arquivada();

/* ------------------------------------------------------- */
-- Não pode inserir ou deletar disciplina de turma arquivada

CREATE OR REPLACE FUNCTION proibe_deletar_disciplina_de_turma_arquivada() RETURNS TRIGGER AS $$
declare
	ano_turma int;
begin
	SELECT ano into ano_turma FROM turma WHERE id_turma = OLD.id_turma;
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Disciplinas de turmas arquivadas não podem ser deletadas';
	ELSE
		RETURN OLD;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_deletar_disciplina_de_turma_arquivada
BEFORE DELETE ON disciplina
FOR EACH ROW EXECUTE PROCEDURE proibe_deletar_disciplina_de_turma_arquivada();

CREATE OR REPLACE FUNCTION proibe_inserir_disciplina_em_turma_arquivada() RETURNS TRIGGER as $$
declare
	ano_turma int;
begin
	select ano into ano_turma from turma where id_turma = new.id_turma;
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Disciplinas não podem ser inseridas em turmas arquivadas';
	ELSE
		RETURN OLD;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_inserir_disciplina_em_turma_arquivada
BEFORE DELETE ON disciplina
FOR EACH ROW EXECUTE PROCEDURE proibe_inserir_disciplina_em_turma_arquivada();




/* ------------------------------------------------------- */
-- Não pode inserir ou deletar professor em disciplina de turma arquivada

CREATE OR REPLACE FUNCTION proibe_deletar_professor_de_disciplina_arquivada() RETURNS TRIGGER as $$
DECLARE
	ano_turma INT;
begin
	SELECT t.ano 
	  into ano_turma
	  FROM turma t
	  JOIN disciplina d
	    ON t.id_turma = d.id_turma
	 WHERE d.id_disciplina = OLD.id_disciplina;
	
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Professores não podem ser deletados de disciplinas arquivadas';
	ELSE
		RETURN OLD;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_deletar_professor_de_disciplina_arquivada
BEFORE DELETE ON professor_de_disciplina
FOR EACH ROW EXECUTE PROCEDURE proibe_deletar_professor_de_disciplina_arquivada();

CREATE OR REPLACE FUNCTION proibe_inserir_professor_em_disciplina_arquivada() returns trigger as $$
DECLARE
	ano_turma INT;
begin
	SELECT t.ano
	  into ano_turma
      FROM turma t
      JOIN disciplina d
        ON t.id_turma = d.id_turma
	 WHERE d.id_disciplina = NEW.id_disciplina;
	
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Professores não podem ser inseridos em disciplinas arquivadas';
	ELSE
		RETURN OLD;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_inserir_professor_em_disciplina_arquivada
BEFORE INSERT ON professor_de_disciplina
FOR EACH ROW EXECUTE PROCEDURE proibe_inserir_professor_em_disciplina_arquivada();

/* ------------------------------------------------------- */
-- Não pode inserir ou atualizar ou deletar tarefa de turma arquivada

CREATE OR REPLACE FUNCTION proibe_alterar_tarefa_arquivada() RETURNS TRIGGER AS $$
DECLARE
	ano_turma INT;
begin
	 SELECT tu.ano
	   into ano_turma
	   FROM turma tu
	   JOIN disciplina d
	     ON tu.id_turma = d.id_turma
	  WHERE d.id_disciplina = OLD.id_disciplina;
	
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Tarefas de turmas arquivadas não podem ser alteradas';
	ELSE
		RETURN OLD;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_alterar_tarefa_arquivada
BEFORE UPDATE ON tarefa
FOR EACH ROW EXECUTE PROCEDURE proibe_alterar_tarefa_arquivada();

CREATE OR REPLACE FUNCTION proibe_deletar_tarefa_arquivada() RETURNS TRIGGER as $$
DECLARE
	ano_turma int;
begin
	SELECT tu.ano
	  into ano_turma
      FROM turma tu
	  JOIN disciplina d
	    ON tu.id_turma = d.id_turma
	 WHERE d.id_disciplina = OLD.id_disciplina;
	
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Tarefas de turmas arquivadas não podem ser deletadas';
	ELSE
		RETURN OLD;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_deletar_tarefa_arquivada
BEFORE DELETE ON tarefa
FOR EACH ROW EXECUTE PROCEDURE proibe_deletar_tarefa_arquivada();

CREATE OR REPLACE FUNCTION proibe_inserir_tarefa_arquivada() RETURNS TRIGGER AS $$
DECLARE
	ano_turma INT;
begin
	SELECT tu.ano
	  into ano_turma
	  FROM turma tu
	  JOIN disciplina d
	    ON tu.id_turma = d.id_turma
	 WHERE d.id_disciplina = NEW.id_disciplina;
	
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Não se pode inserir uma tarefa numa disciplina de turma arquivada';
	ELSE
		RETURN NEW;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_inserir_tarefa_arquivada
BEFORE INSERT ON tarefa
FOR EACH ROW EXECUTE PROCEDURE proibe_inserir_tarefa_arquivada();



/* ------------------------------------------------------- */
-- Não pode inserir ou atualizar ou deletar entrega de tarefa arquivada

CREATE OR REPLACE FUNCTION proibe_alterar_entrega_arquivada() RETURNS TRIGGER AS $$
DECLARE
	ano_turma INT;
begin
	 SELECT tu.ano
	   into ano_turma
	   FROM turma tu
	   JOIN disciplina d
	     ON tu.id_turma = d.id_turma
	   join tarefa ta
	     on d.id_disciplina = ta.id_disciplina
	  WHERE ta.id_tarefa = OLD.id_tarefa;
	
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Entregas de tarefas arquivadas não podem ser alteradas';
	ELSE
		RETURN OLD;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_alterar_entrega_arquivada
BEFORE UPDATE ON entrega
FOR EACH ROW EXECUTE PROCEDURE proibe_alterar_entrega_arquivada();

CREATE OR REPLACE FUNCTION proibe_deletar_entrega_arquivada() RETURNS TRIGGER as $$
DECLARE
	ano_turma int;
begin
	 SELECT tu.ano
	   into ano_turma
	   FROM turma tu
	   JOIN disciplina d
	     ON tu.id_turma = d.id_turma
	   join tarefa ta
	     on d.id_disciplina = ta.id_disciplina
	  WHERE ta.id_tarefa = OLD.id_tarefa;
	
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Entregas de tarefas arquivadas não podem ser deletadas';
	ELSE
		RETURN OLD;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_deletar_entrega_arquivada
BEFORE DELETE ON entrega
FOR EACH ROW EXECUTE PROCEDURE proibe_deletar_entrega_arquivada();

CREATE OR REPLACE FUNCTION proibe_inserir_entrega_arquivada() RETURNS TRIGGER AS $$
DECLARE
	ano_turma INT;
begin
	 SELECT tu.ano
	   into ano_turma
	   FROM turma tu
	   JOIN disciplina d
	     ON tu.id_turma = d.id_turma
	   join tarefa ta
	     on d.id_disciplina = ta.id_disciplina
	  WHERE ta.id_tarefa = NEW.id_tarefa;
	
	IF ano_anterior(ano_turma) THEN
		RAISE EXCEPTION 'Não se pode inserir uma entrega numa tarefa arquivada';
	ELSE
		RETURN NEW;
	END IF;
END
$$ LANGUAGE plpgsql;

CREATE or replace TRIGGER tr_proibe_inserir_entrega_arquivada
BEFORE INSERT ON entrega
FOR EACH ROW EXECUTE PROCEDURE proibe_inserir_entrega_arquivada();


