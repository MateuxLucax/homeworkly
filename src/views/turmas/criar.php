<!DOCTYPE html>
<html lang="en">
<?php require_once $root.'views/componentes/head.php'; ?>
<body>

    <main class="container">
        <form id="form-criar-turma">

            <div class="card mb-3 mt-3">
                <div class="card-header">
                    Turma
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="turma-nome" class="form-label">Nome</label>
                        <input type="text" name="turma-nome" id="turma-nome" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="turma-ano" class="form-label">Ano</label>
                        <input type="number" name="turma-ano" id="turma-ano" class="form-control" value="<?=date('Y')?>">
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    Disciplinas
                </div>
                <div class="card-body" id="disciplinas-container">

                    <button type="button" style="width: 100%" id="btn-adicionar-disciplina" class="btn btn-outline-success">
                        <i class="fas fa-plus-circle"></i>
                    </button>

                </div>
            </div>

            <div id="alunos-container" style="display: hidden">
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    Alunos
                </div>
                <div class="card-body" id="alunos-container">
                    <table class="table table-striped table-hover d-none" id="table-alunos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Login</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-alunos">
                        </tbody>
                    </table>
                    <button
                        type="button"
                        style="width: 100%"
                        class="btn btn-outline-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modal-adicionar-aluno"
                    >
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-paper-plane"></i>&nbsp;
                    Criar
                </button>
            </div>

        </form>
    </main>

    <div class="modal" id="modal-adicionar-aluno">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Adicionar aluno
                </div>
                <div class="modal-body">

                    <div class="input-group mb-3">
                        <input type="search" class="form-control" id="input-pesquisa-aluno" />
                        <button class="btn btn-primary" id="btn-pesquisar-alunos">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <table class="table table-hover mb-3 d-none" id="table-pesquisa-alunos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Login</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-pesquisa-alunos">
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        id="btn-fechar-modal-adicionar-aluno"
                        data-bs-toggle="modal"
                        data-bs-target="#modal-adicionar-aluno"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
        
    </div>

    <script>
        const form = document.getElementById('form-criar-turma');

        form.onsubmit = event => {
            event.preventDefault();

            // TODO validação dos campos

            const payload = {
                nome: form['turma-nome'].value,
                ano:  form['turma-ano'].value,
                disciplinas: Array.from(form['disciplina[]'] ?? []).map(i => i.value),
                alunos: Array.from(form['aluno[]'] ?? []).map(i => i.value)
            };

            console.log(payload);

            fetch('criar', {method: 'POST', body: JSON.stringify(payload)})
            .then(response => {
                if (response.status == 201) {
                    agendarAlertaSwal({
                        icon: 'success',
                        text: 'Turma criada com sucesso'
                    });
                    response.json().then(ret => {
                        window.location.assign(`turma?id=${ret.id}`);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: 'Não foi possível criar a turma'
                    });
                    response.text().then(console.log);
                }
            });
        };

        //
        // Adicionar disciplinas dinamicamente
        //

        const disciplinasContainer   = document.getElementById('disciplinas-container');
        const btnAdicionarDisciplina = document.getElementById('btn-adicionar-disciplina');

        btnAdicionarDisciplina.onclick = () => {
            disciplinasContainer.insertBefore(
                criarDisciplina(),
                btnAdicionarDisciplina
            );
        };

        function criarDisciplina() {
            const card = criarElemento('div', ['card', 'mb-3', 'bg-dark']);
            const cardBody = criarElemento('div', ['card-body'], card);

            const inputGroup = criarElemento('div', ['input-group', 'mb-3'], cardBody);

            criarElemento('input', ['disciplina-nome', 'form-control'], inputGroup, {
                type: 'text',
                name: 'disciplina[]'
            });

            const btnRemover = criarElemento('button', ['btn', 'btn-danger'], inputGroup, {
                type: 'button',
                onclick: () => { card.remove() }
            });

            criarElemento('i', ['fas', 'fa-minus-circle'], btnRemover);

            const cardProfessores = criarElemento('div', ['card'], cardBody);
            const cardHeaderProfessores = criarElemento('div', ['card-header'], cardProfessores);
            cardHeaderProfessores.append('Professor(es)');
            const cardBodyProfessores = criarElemento('div', ['card-body'], cardProfessores);

            const tableProfessores = criarElemento('table', ['table', 'table-hover', 'd-none'], cardBodyProfessores);
            const theadProfessores = criarElemento('thead', [], tableProfessores);
            theadProfessores.append(criarTr(['ID', 'Nome', 'Login', ''], 'th'));
            const tbodyProfessores = criarElemento('tbody', [], tableProfessores);

            const btnAddProfessor = criarElemento('button', ['btn', 'btn-outline-success'], cardBodyProfessores, { type: 'button', });
            const iconeAddProfessor = criarElemento('i', ['fas', 'fa-search'], btnAddProfessor);
            btnAddProfessor.style.width = '100%';
            btnAddProfessor.setAttribute('data-bs-toggle', 'modal');
            btnAddProfessor.setAttribute('data-bs-target', '#modal-adicionar-professor');

            // TODO fazer esse modal

            return card;
        }

        //
        // Adicionar alunos dinamicamente
        //

        const btnPesquisarAlunos = document.getElementById('btn-pesquisar-alunos');
        const tablePesquisaAlunos = document.getElementById('table-pesquisa-alunos')
        const tbodyPesquisaAlunos = document.getElementById('tbody-pesquisa-alunos');

        btnPesquisarAlunos.onclick = () => {
            tablePesquisaAlunos.classList.add('d-none');
            const pesquisa = document.getElementById('input-pesquisa-aluno').value;
            while (tbodyPesquisaAlunos.firstChild) {
                tbodyPesquisaAlunos.firstChild.remove();
            }
            fetch('/admin/usuarios/listar', {
                headers: { 'Accept': 'application/json' },
                method: 'POST',
                body: JSON.stringify({ filtros: { nome: pesquisa, tipo: 'aluno' } })
            }).then(resp => {
                if (resp.status == 200) return resp.json();
                else throw {};
            }).then(alunos => {
                if (alunos.length > 0) {
                    tablePesquisaAlunos.classList.remove('d-none');
                    tbodyPesquisaAlunos.append(...alunos.map(criarAlunoResultadoPesquisa));
                }
            }, () => {
                Swal.fire({
                    icon: 'error',
                    text: 'Não foi possível realizar a pesquisa'
                });
            });
        };

        function criarAlunoResultadoPesquisa({id_usuario, nome, login}) {
            const btnAdd = criarElemento('button', ['btn', 'btn-success'], null, { type: 'button' });
            const iconeAdd = criarElemento('i', ['fas', 'fa-plus'], btnAdd);
            btnAdd.onclick = () => {
                document.getElementById('btn-fechar-modal-adicionar-aluno').click();
                criarAlunoTurma(id_usuario, nome, login);
            };

            return criarTr([id_usuario, nome, login, btnAdd]);
        }

        const alunosContainer = document.getElementById('alunos-container');
        const tbodyAlunos = document.getElementById('tbody-alunos');

        const alunosInseridos = new Set();

        function criarAlunoTurma(id, nome, login) {
            if (alunosInseridos.has(id)) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Aluno já adicionado a turma'
                });
                return;
            }

            alunosInseridos.add(id);

            const inputAluno = criarElemento('input', [], alunosContainer, {
                name: 'aluno[]',
                type: 'hidden',
                value: id
            });

            const tableAlunos = document.getElementById('table-alunos');

            const btnRemover = criarElemento('button', ['btn', 'btn-outline-danger'], null, { type: 'button' });
            const iconeRemover = criarElemento('i', ['fas', 'fa-minus-circle'], btnRemover);

            const trAluno = criarTr([id, nome, login, btnRemover]);
            btnRemover.onclick = () => {
                alunosInseridos.delete(id);
                if (alunosInseridos.size == 0) {
                    tableAlunos.classList.add('d-none');
                }
                inputAluno.remove();
                trAluno.remove();
            };

            tbodyAlunos.append(trAluno);

            tableAlunos.classList.remove('d-none');
        }
    </script>

</body>
</html>