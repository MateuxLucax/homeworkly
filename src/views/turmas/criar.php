<!DOCTYPE html>
<html lang="en">
<?php require_once $root.'/views/componentes/head.php'; ?>
<body>

    <?php if (isset($view['id-turma'])): ?>
    <div class="modal fade" id="modal-confirmar-exclusao">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Tem certeza que deseja excluir essa turma?</p>
                    <button class="btn btn-danger" id="btn-confirmar-exclusao" data-id="<?=$view['id-turma']?>">Excluir</button>
                    <button class="btn btn-secondary" id="btn-cancelar-exclusao" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <header>
        <?php require_once $root.'/views/componentes/navbar-admin.php'; ?>
    </header>

    <main class="container">
        <form id="form-criar-turma">

            <input type="hidden" name="acao" value="<?= isset($view['id-turma']) ? 'alterar' : 'criar' ?>" />

            <?php if (isset($view['id-turma'])) echo '<input type="hidden" name="id" value="'.$view['id-turma'].'"/>'; ?>

            <div class="card mb-3 mt-3">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        Turma
                        <?php if (isset($view['id-turma'])): ?>
                            &nbsp;
                            <span class="text-muted">#<?= $view['id-turma'] ?></span>
                            <button id="btn-excluir-turma" type="button" class="btn btn-danger ms-auto" data-bs-toggle="modal" data-bs-target="#modal-confirmar-exclusao">
                                <i class="fas fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="turma-nome" class="form-label">Nome</label>
                                <input type="text" name="turma-nome" id="turma-nome" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="turma-ano" class="form-label">Ano</label>
                                <input type="number" name="turma-ano" id="turma-ano" class="form-control" value="<?=date('Y')?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-10 my-auto">
                                    Disciplinas
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" id="btn-adicionar-disciplina" class="btn btn-success float-end">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="disciplinas-container">
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-10 my-auto">
                                    Alunos
                                </div>
                                <div class="col-sm-2">
                                    <button
                                        type="button"
                                        class="btn btn-success float-end"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-adicionar-aluno"
                                    >
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
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
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-paper-plane"></i>&nbsp;
                    <?= isset($view['id-turma']) ? 'Alterar' : 'Criar' ?>
                </button>
            </div>

        </form>
    </main>

    <div class="modal" id="modal-adicionar-professor">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Adicionar professor
                </div>
                <div class="modal-body">

                    <div class="input-group mb-3">
                        <input type="search" class="form-control" id="input-pesquisa-professores" />
                        <button class="btn btn-primary" id="btn-pesquisar-professores">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <table class="table table-hover mb-3 d-none" id="table-pesquisa-professores">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Login</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-pesquisa-professores">
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        id="btn-fechar-modal-adicionar-professor"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
        
    </div>

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

            const dados = {
                id:   form['id']?.value,
                acao: form['acao'].value,
                nome: form['turma-nome'].value,
                ano:  form['turma-ano'].value,
                disciplinas: Array.from(document.getElementsByClassName('card-disciplina')).map(d => {
                    const disciplina = {
                        nome: d.getElementsByClassName('disciplina-nome')[0].value,
                        professores: Array.from(d.getElementsByClassName('professor-id')).map(i => i.value)
                    };
                    const id = d.getElementsByClassName('disciplina-id')[0]?.value;
                    if (id) disciplina.id = id;
                    return disciplina;
                }),
                alunos: Array.from(document.getElementsByClassName('aluno-id')).map(i => i.value)
            };

            let target, alertaSucesso, alertaErro, statusEsperado;
            if (dados.acao == 'criar') {
                target = 'criar';
                metodo = 'POST';
                alertaSucesso = 'Turma criada com sucesso';
                alertaErro = 'Não foi possível criar a turma';
                statusEsperado = 201;
            } else if (dados.acao == 'alterar') {
                target = 'alterar';
                metodo = 'PUT';
                alertaSucesso = 'Turma alterada com sucesso';
                alertaErro = 'Não foi possível alterar a turma';
                statusEsperado = 200;
            }

            fetch(target, {method: metodo, body: JSON.stringify(dados)})
            .then(response => {
                if (response.status != statusEsperado) {
                    Swal.fire({
                        icon: 'error',
                        text: alertaErro
                    });
                    response.text().then(console.log);
                    return;
                }
                agendarAlertaSwal({
                    icon: 'success',
                    text: alertaSucesso
                });
                return response.text();
            }).then(ret => {
                try {
                    const id = JSON.parse(ret).id;
                    window.location.assign(`turma?id=${id}`);
                } catch (e) {
                    console.log(ret)
                }
            });
        };

        //
        // Adicionar disciplinas dinamicamente
        //

        let proxIdDOMDisciplina = 1;
        // Usado para gerar atributos ID para os elementos das disciplinas
        // que por sua vez são usados para adicionar os professores na disciplina correta

        const disciplinasContainer   = document.getElementById('disciplinas-container');
        const btnAdicionarDisciplina = document.getElementById('btn-adicionar-disciplina');

        btnAdicionarDisciplina.onclick = () => {
            disciplinasContainer.insertBefore(criarDisciplina(), disciplinasContainer.firstChild);
        };

        const elemModalAdicionarProfessor = document.getElementById('modal-adicionar-professor');
        const modalAdicionarProfessor = new bootstrap.Modal(elemModalAdicionarProfessor);

        // Usado tanto para criar uma nova disciplina quando o usuário clica no botão (sem argumentos)
        // quanto para carregar uma disciplina existente (passada como argumento)
        function criarDisciplina(disciplina) {
            const idDOMDisciplina = `disciplina-${proxIdDOMDisciplina++}`;  
            const card = criarElemento('div', ['card-disciplina', 'card', 'mb-3', 'bg-dark', 'bg-gradient'], null, { id: idDOMDisciplina });
            const cardBody = criarElemento('div', ['card-body'], card);

            const inputGroup = criarElemento('div', ['input-group', 'mb-3'], cardBody);
            const inputNome = criarElemento('input', ['disciplina-nome', 'form-control'], inputGroup, { type: 'text' });
            const btnRemover = criarElemento('button', ['btn', 'btn-danger'], inputGroup, {
                type: 'button',
                onclick: () => { card.remove() }
            });
            criarElemento('i', ['fas', 'fa-minus-circle'], btnRemover);

            const cardProfessores = criarElemento('div', ['card'], cardBody);
            const cardHeaderProfessores = criarElemento('div', ['card-header'], cardProfessores);
            const cardBodyProfessores = criarElemento('div', ['card-body'], cardProfessores);

            const headerProfsRow = criarElemento('div', ['row'], cardHeaderProfessores);
            const headerProfsColTitulo = criarElemento('div', ['col-sm-10', 'my-auto'], headerProfsRow);
            headerProfsColTitulo.append('Professor(es)');
            const headerProfsColBtn = criarElemento('div', ['col-sm-2'], headerProfsRow);

            const btnAddProfessor = criarElemento('button', ['btn', 'btn-success', 'float-end'], headerProfsColBtn, {
                type: 'button',
                onclick: () => {
                    elemModalAdicionarProfessor.setAttribute('data-id-dom-disciplina', idDOMDisciplina)
                    modalAdicionarProfessor.show();
                }
            });
            document.getElementById('btn-fechar-modal-adicionar-professor').onclick = () => { modalAdicionarProfessor.hide(); }
            const iconeAddProfessor = criarElemento('i', ['fas', 'fa-search'], btnAddProfessor);

            const tableProfessores = criarElemento('table', ['table-professores', 'table', 'table-hover', 'd-none'], cardBodyProfessores);
            const theadProfessores = criarElemento('thead', [], tableProfessores);
            theadProfessores.append(criarTr(['ID', 'Nome', 'Login', ''], 'th'));
            const tbodyProfessores = criarElemento('tbody', ['tbody-professores'], tableProfessores);

            if (disciplina) {
                if (!disciplina.podeExcluir) {
                    btnRemover.disabled = true;
                    // TODO adicionar mensagem (tooltip) explicando pq não pode excluir
                }
                criarElemento('input', ['disciplina-id'], card, {
                    type: 'hidden',
                    name: 'disciplina-id',
                    value: disciplina.id
                });
                inputNome.value = disciplina.nome;
                for (const prof of disciplina.professores) {
                    adicionarProfessorDisciplina(prof.id, prof.nome, prof.login, card);
                }
            }

            return card;
        }

        //
        // Adicionar professores às disciplinas dinamicamente
        //

        const btnPesquisarProfessores = document.getElementById('btn-pesquisar-professores');
        const tablePesquisaProfessores = document.getElementById('table-pesquisa-professores');
        const tbodyPesquisaProfessores = document.getElementById('tbody-pesquisa-professores');

        btnPesquisarProfessores.onclick = () => {
            tablePesquisaProfessores.classList.add('d-none');
            const pesquisa = document.getElementById('input-pesquisa-professores').value;

            while (tbodyPesquisaProfessores.firstChild) {
                tbodyPesquisaProfessores.firstChild.remove();
            }

            fetch('/admin/usuarios/listar', {
                headers: { Accept: 'application/json' },
                method: 'POST',
                body: JSON.stringify({
                    filtros: {
                        nome: pesquisa,
                        tipo: 'professor'
                    }
                })
            }).then(resp => {
                if (resp.status != 200) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Não foi possível realizar a pesquisa'
                    });
                    resp.text().then(console.log);
                    return;
                }
                resp.text().then(ret => {
                    try {
                        const professores = JSON.parse(ret);
                        console.log(professores);
                        if (professores.length == 0) return;
                        tablePesquisaProfessores.classList.remove('d-none');
                        tbodyPesquisaProfessores.append(...professores.map(criarProfessorResultadoPesquisa));
                    } catch (e) {
                        console.error(e);
                        console.error(ret);
                    }
                });
            })
        };

        function criarProfessorResultadoPesquisa({ id_usuario, nome, login }) {
            const btnAdd = criarElemento('button', ['btn', 'btn-success'], null, {
                type: 'button',
                onclick: () => {
                    // Importante pegar esse cardDisciplina aqui dentro da callback, porque senão
                    // o professor vai pra disciplina errada (fica retido o card da disciplina de uma pesquisa anterior)
                    const cardDisciplina = document.getElementById(elemModalAdicionarProfessor.getAttribute('data-id-dom-disciplina'));
                    modalAdicionarProfessor.hide();
                    adicionarProfessorDisciplina(id_usuario, nome, login, cardDisciplina);
                }
            });
            criarElemento('i', ['fas', 'fa-plus'], btnAdd);

            return criarTr([ id_usuario, nome, login, btnAdd ]);
        }

        function adicionarProfessorDisciplina(id, nome, login, cardDisciplina) {
            const inputsAdicionados = cardDisciplina.getElementsByClassName('professor-id');
            if (Array.from(inputsAdicionados).some(i => i.value == id)) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Professor já adicionado nessa disciplina'
                });
                return;
            }

            const input = criarElemento('input', ['professor-id'], cardDisciplina, {
                type: 'hidden',
                value: id
            });

            const table = cardDisciplina.getElementsByClassName('table-professores')[0];
            table.classList.remove('d-none');

            const btnRemover = criarElemento('button', ['btn', 'btn-outline-danger'], null, { type: 'button', });
            criarElemento('i', ['fas', 'fa-minus-circle'], btnRemover);
            const tbody = cardDisciplina.getElementsByClassName('tbody-professores')[0];
            const tr = criarTr([id, nome, login, btnRemover]);
            tbody.append(tr);

            btnRemover.onclick = () => {
                input.remove();
                tr.remove();
                if (inputsAdicionados.length == 0) {
                    table.classList.add('d-none');
                }
            };
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
                if (resp.status != 200) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Não foi possível realizar a pesquisa'
                    });
                    resp.text().then(console.log);
                    return;
                }
                resp.text().then(ret => {
                    try {
                        const alunos = JSON.parse(ret);
                        if (alunos.length == 0) return;
                        tablePesquisaAlunos.classList.remove('d-none');
                        tbodyPesquisaAlunos.append(...alunos.map(criarAlunoResultadoPesquisa));
                    } catch (e) {
                        console.error(e);
                        console.error(ret);
                    }
                })
            });
        };

        function criarAlunoResultadoPesquisa({id_usuario, nome, login}) {
            const btnAdd = criarElemento('button', ['btn', 'btn-success'], null, { type: 'button' });
            const iconeAdd = criarElemento('i', ['fas', 'fa-plus'], btnAdd);
            btnAdd.onclick = () => {
                document.getElementById('btn-fechar-modal-adicionar-aluno').click();
                adicionarAlunoTurma(id_usuario, nome, login);
            };
            return criarTr([id_usuario, nome, login, btnAdd]);
        }

        const alunosContainer = document.getElementById('alunos-container');
        const tbodyAlunos = document.getElementById('tbody-alunos');

        const alunosAdicionados = new Set();

        function adicionarAlunoTurma(id, nome, login) {
            if (alunosAdicionados.has(id)) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Aluno já adicionado a turma'
                });
                return;
            }

            alunosAdicionados.add(id);

            const inputAluno = criarElemento('input', ['aluno-id'], alunosContainer, {
                name: 'aluno[]',
                type: 'hidden',
                value: id
            });

            const tableAlunos = document.getElementById('table-alunos');

            const btnRemover = criarElemento('button', ['btn', 'btn-outline-danger'], null, { type: 'button' });
            const iconeRemover = criarElemento('i', ['fas', 'fa-minus-circle'], btnRemover);

            const trAluno = criarTr([id, nome, login, btnRemover]);
            btnRemover.onclick = () => {
                alunosAdicionados.delete(id);
                if (alunosAdicionados.size == 0) {
                    tableAlunos.classList.add('d-none');
                }
                inputAluno.remove();
                trAluno.remove();
            };

            tbodyAlunos.append(trAluno);

            tableAlunos.classList.remove('d-none');
        }


        const idTurmaAlterar = <?= $view['id-turma'] ?? '-1' ?>;

        if (idTurmaAlterar != -1) {
            fetch(`turma?id=${idTurmaAlterar}`, {headers: {'Accept': 'application/json'}})
            .then(resp => {
                if (resp.status != 200) {
                    // TODO agendarAlertaSwal e depois mudar de página, já que essa não vai ser carregada normalmente?
                    Swal.fire({
                        icon: 'warning',
                        text: 'Não foi possível carregar os dados da turma para alterá-la'
                    });
                }
                return resp.text();
            }).then(ret => {
                try {
                    const turma = JSON.parse(ret);
                    console.log(turma);
                    if (!turma.podeExcluir) {
                        document.getElementById('btn-excluir-turma').disabled = true;
                        // TODO adicionar tooltip explicando pq não pode excluir
                    }
                    document.getElementById('turma-nome').value = turma.nome;
                    document.getElementById('turma-ano').value = turma.ano;
                    for (const aluno of turma.alunos) {
                        adicionarAlunoTurma(aluno.id, aluno.nome, aluno.login);
                    }
                    for (const disciplina of turma.disciplinas) {
                        disciplinasContainer.appendChild(criarDisciplina(disciplina));
                    }
                } catch (e) {
                    console.error(e);
                    console.error(ret);
                }

            });
        }

        //
        // Exclusão de turma (quanto alterar)
        //

        const btnConfirmarExclusao = document.getElementById('btn-confirmar-exclusao');
        const btnCancelarExclusao = document.getElementById('btn-cancelar-exclusao');

        btnConfirmarExclusao?.addEventListener('click', () => {
            const id = btnConfirmarExclusao.getAttribute('data-id');
            fetch('excluir', {method: 'DELETE', body: JSON.stringify({id})})
            .then(resp => {
                resp.text().then(console.log);
                if (resp.status != 200) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Não foi possível excluir a turma'
                    });
                    btnCancelarExclusao.click();  // fechar o modal
                } else {
                    agendarAlertaSwal({
                        icon: 'success',
                        text: 'Turma excluída com sucesso'
                    });
                    window.location.assign('listar');
                }
            });
        });

    </script>
    

</body>
</html>