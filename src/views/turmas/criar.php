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
                    <table class="table table-striped table-hover">
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
                        id="btn-adicionar-aluno"
                        class="btn btn-outline-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modal-adicionar-aluno"
                    >
                        <i class="fas fa-plus-circle"></i>
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
                    <table class="table table-hover mb-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Login</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-alunos-pesquisa">
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
                criarInputDisciplina(),
                btnAdicionarDisciplina
            );
        };

        function criarInputDisciplina() {
            const group = document.createElement('div');
            group.classList.add('mb-3', 'input-group');

            const input = document.createElement('input');
            Object.assign(input, {
                type: 'text',
                name: 'disciplina[]'
            })
            input.classList.add('disciplina-nome', 'form-control');
            group.appendChild(input);

            const iconeRemover = document.createElement('i');
            iconeRemover.classList.add('fas', 'fa-minus-circle');

            const btnRemover = document.createElement('button');
            Object.assign(btnRemover, {
                type: 'button',
                tabIndex: -1,
                onclick: () => { group.remove() }
            });
            btnRemover.classList.add('btn', 'btn-outline-danger')
            btnRemover.appendChild(iconeRemover);
            group.appendChild(btnRemover);

            return group;
        }

        //
        // Adicionar alunos dinamicamente
        //

        const btnPesquisarAluno = document.getElementById('btn-pesquisar-alunos');
        const tbodyAlunosPesquisa = document.getElementById('tbody-alunos-pesquisa');

        btnPesquisarAluno.onclick = () => {
            const pesquisa = document.getElementById('input-pesquisa-aluno').value;
            while (tbodyAlunosPesquisa.firstChild) {
                tbodyAlunosPesquisa.firstChild.remove();
            }
            fetch('/admin/usuarios/listar', {
                headers: { 'Accept': 'application/json' },
                method: 'POST',
                body: JSON.stringify({ filtros: { nome: pesquisa, tipo: 'aluno' } })
            }).then(resp => {
                if (resp.status == 200) return resp.json();
                else throw {};
            }).then(alunos => {
                tbodyAlunosPesquisa.append(...alunos.map(criarAlunoResultadoPesquisa));
            }, () => {
                Swal.fire({
                    icon: 'error',
                    text: 'Não foi possível realizar a pesquisa'
                });
            });
        };

        function criarAlunoResultadoPesquisa({id_usuario, nome, login}) {
            const btnAdd = document.createElement('button');
            btnAdd.classList.add('btn', 'btn-success');
            const iconeAdd = document.createElement('i');
            iconeAdd.classList.add('fas', 'fa-plus');
            btnAdd.append(iconeAdd);
            btnAdd.onclick = () => {
                document.getElementById('btn-fechar-modal-adicionar-aluno').click();
                criarAlunoTurma(id_usuario, nome, login);
            };

            return criarTr(id_usuario, nome, login, btnAdd);
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

            const inputAluno = document.createElement('input');
            Object.assign(inputAluno, {
                name: 'aluno[]',
                type: 'hidden',
                value: id
            });
            alunosContainer.append(inputAluno);


            const btnRemover = document.createElement('button');
            btnRemover.classList.add('btn', 'btn-outline-danger');
            const iconeRemover = document.createElement('i');
            iconeRemover.classList.add('fas', 'fa-minus-circle');
            const trAluno = criarTr(id, nome, login, btnRemover);
            btnRemover.append(iconeRemover);
            btnRemover.onclick = () => {
                alunosInseridos.delete(id);
                inputAluno.remove();
                trAluno.remove();
            };

            tbodyAlunos.append(trAluno);
        }
    </script>

</body>
</html>