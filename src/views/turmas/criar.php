<!-- TODO nas pesquisas por alunos e professores, colocar um alerta quando não houverem resultados, senão parece que ainda tá carregando -->
<!DOCTYPE html>
<html lang="pt-BR">
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
                            <span id="btn-excluir-turma-wrapper" class="ms-auto">
                                <button id="btn-excluir-turma" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-confirmar-exclusao">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="turma-nome" class="form-label">Nome</label>
                                <input type="text" name="turma-nome" id="turma-nome" class="form-control" required />
                            </div>
                            <div class="col-md-6">
                                <label for="turma-ano" class="form-label">Ano</label>
                                <input type="number" name="turma-ano" id="turma-ano" class="form-control" value="<?=date('Y')?>" required min="1970">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex align-items-center">
                    Disciplinas
                    <button type="button" id="btn-adicionar-disciplina" class="ms-auto btn btn-success">
                        <i class="fas fa-plus-circle"></i>
                        Adicionar disciplina
                    </button>
                </div>
                <div class="card-body" id="disciplinas-container">
                </div>
            </div>

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
                                Adicionar aluno
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

                    <!-- TODO colocar ícone de pesquisa -->
                    <div class="mb-3">
                        <label for="input-pesquisa-professores" class="form-label">Pesquisar por nome</label>
                        <input type="search" class="form-control" id="input-pesquisa-professores" value=""/>
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

                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link disabled">Pesquisar por</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                type="button"
                                id="tab-pesquisar-por-nome"
                                class="nav-link active"
                                data-bs-toggle="tab"
                                data-bs-target="#pesquisar-por-nome"
                                role="tab"
                            >Nome</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                type="button"
                                id="tab-pesquisar-por-turma"
                                class="nav-link"
                                data-bs-toggle="tab"
                                data-bs-target="#pesquisar-por-turma"
                                role="tab"
                            >Turma</button>
                        </li>
                    </ul>

                    <div class="tab-content mb-3">
                        <div
                            class="tab-pane fade show active"
                            id="pesquisar-por-nome"
                            role="tabpanel"
                        >
                            <div class="input-group">
                                <input type="search" class="form-control" id="input-pesquisa-aluno" />
                                <button class="btn btn-primary" id="btn-pesquisar-alunos">
                                    <i class="fas fa-search"></i>
                                </button>

                                <!-- TODO em vez de botão pra pesquisar, fazer no keydown (com debounce) -->

                            </div>
                        </div>

                        <div
                            class="tab-pane fade"
                            id="pesquisar-por-turma"
                            role="tabpanel"
                        >
                            <div class="row">
                                <div class="col-4">
                                    <select id="pesquisa-aluno-select-ano" class="form-control mb-3">
                                        <option value="-1">- Ano -</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select disabled id="pesquisa-aluno-select-turma" class="form-control mb-3"></select>
                                </div>
                            </div>
                        
                        </div>
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
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
        
    </div>

    <script>

        //
        // Submissão do formulário
        //

        const form = document.getElementById('form-criar-turma');

        form.onsubmit = async event => {
            event.preventDefault();

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

            for (const disciplina of dados.disciplinas) {
                if (disciplina.professores.length == 0) {
                    Swal.fire({
                        icon: 'warning',
                        text: `Toda disciplina precisa de pelo menos um professor. '${disciplina.nome}' não tem nenhum professor.`
                    });
                    return;
                }
            }

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

            const response = await fetch(target, {method: metodo, body: JSON.stringify(dados)});
            const textProm = response.text();

            if (response.status != statusEsperado) {
                Swal.fire({
                    icon: 'error',
                    text: alertaErro
                });
                console.log(await textProm);
                return;
            }

            agendarAlertaSwal({
                icon: 'success',
                text: alertaSucesso
            });

            try {
                const ret = await textProm;
                const id = JSON.parse(ret).id;
                location.assign(`turma?id=${id}`);
            } catch (err) {
                console.error(err, '\n', ret);
            }
        };

        //
        // Adicionar disciplinas dinamicamente
        //

        let proxIdDOMDisciplina = 1;
        // Cada disciplina tem um ID distinto no DOM para os professores serem colocados na disciplina correta

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
            const inputNome = criarElemento('input', ['disciplina-nome', 'form-control'], inputGroup, {
                type: 'text',
                required: true,
                minlength: 5
            });
            const btnRemoverWrapper = criarElemento('span', [], inputGroup);
            const btnRemover = criarElemento('button', ['btn', 'btn-danger'], btnRemoverWrapper, {
                type: 'button',
                onclick: () => { card.remove() }
            });
            criarElemento('i', ['fas', 'fa-minus-circle'], btnRemover);

            const cardProfessores = criarElemento('div', ['card'], cardBody);
            const cardHeaderProfessores = criarElemento('div', ['card-header', 'd-flex', 'align-items-center'], cardProfessores);
            const cardBodyProfessores = criarElemento('div', ['card-body'], cardProfessores);

            cardHeaderProfessores.append('Professor(es)');
            const btnAddProfessor = criarElemento('button', ['btn', 'btn-success', 'ms-auto'], cardHeaderProfessores, {
                type: 'button',
                onclick: () => {
                    elemModalAdicionarProfessor.setAttribute('data-id-dom-disciplina', idDOMDisciplina)
                    modalAdicionarProfessor.show();
                }
            });
            criarElemento('i', ['fas', 'fa-search'], btnAddProfessor);
            btnAddProfessor.append('  Adicionar professor');

            document.getElementById('btn-fechar-modal-adicionar-professor').onclick = () => { modalAdicionarProfessor.hide(); }

            const tableProfessores = criarElemento('table', ['table-professores', 'table', 'table-hover', 'd-none'], cardBodyProfessores);
            const theadProfessores = criarElemento('thead', [], tableProfessores);
            theadProfessores.append(criarTr(['ID', 'Nome', 'Login', ''], 'th'));
            const tbodyProfessores = criarElemento('tbody', ['tbody-professores'], tableProfessores);

            if (disciplina) {
                if (!disciplina.podeExcluir) {
                    btnRemover.disabled = true;
                    btnRemoverWrapper.setAttribute('data-bs-toggle', 'tooltip');
                    btnRemoverWrapper.title = 'Essa disciplina não pode ser excluída pois há tarefas nela';
                    new bootstrap.Tooltip(btnRemoverWrapper);
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

        const todosOsProfessores = [];

        const inputPesquisaProfessores = document.getElementById('input-pesquisa-professores');

        inputPesquisaProfessores.onkeyup = atualizarPesquisaProfessores;

        (async () => {
            const response = await fetch('/admin/usuarios/listar', {
                headers: { Accept: 'application/json' },
                method: 'POST',
                body: JSON.stringify({
                    filtros: {tipo: 'professor'}
                })
            });
            const textProm = response.text();
            if (response.status != 200) {
                agendarAlertaSwal({
                    icon: 'error',
                    title: 'Erro do sistema',
                    text: 'Não foi possível abrir a turma para alterações porque não conseguimos carregar a lista de professores'
                });
                console.log(await textProm);
                history.back();
            }
            try {
                const ret = await textProm;
                JSON.parse(ret).forEach(prof => todosOsProfessores.push(prof));
                atualizarPesquisaProfessores();
            } catch (err) {
                console.error(err, '\n', ret);
            }
        })();

        const tablePesquisaProfessores = document.getElementById('table-pesquisa-professores');
        const tbodyPesquisaProfessores = document.getElementById('tbody-pesquisa-professores');

        function atualizarPesquisaProfessores() {
            removerFilhos(tbodyPesquisaProfessores);
            tablePesquisaProfessores.classList.add('d-none');

            const pesquisa = inputPesquisaProfessores.value;
            const re = new RegExp(pesquisa, 'i');

            tbodyPesquisaProfessores.append(
                ...todosOsProfessores
                   .filter(prof => prof.nome.match(re))
                   .map(criarProfessorResultadoPesquisa)
            );

            if (tbodyPesquisaProfessores.childElementCount > 0)
                tablePesquisaProfessores.classList.remove('d-none');
        }

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

        // TODO Na pesquisa por alunos, desabilitar o botão de adicionar à turma
        // se ele já tiver sido adicionado (com tooltip pra explicação)

        //
        // Adicionar alunos dinamicamente
        //

        // TODO ao mudar de 'pesquisar por', limpar a table pesquisa

        //
        // Adicionar alunos dinamicamente >> Pesquisa por nome
        //

        const btnPesquisarAlunos = document.getElementById('btn-pesquisar-alunos');
        const tablePesquisaAlunos = document.getElementById('table-pesquisa-alunos')
        const tbodyPesquisaAlunos = document.getElementById('tbody-pesquisa-alunos');

        function pesquisaAlunosLimpar() {
            removerFilhos(tbodyPesquisaAlunos);
            tablePesquisaAlunos.classList.add('d-none');
        }

        function pesquisaAlunosPreencher(alunos) {
            if (alunos.length == 0) return;
            tablePesquisaAlunos.classList.remove('d-none');
            tbodyPesquisaAlunos.append(...alunos.map(criarAlunoResultadoPesquisa));
        }

        btnPesquisarAlunos.onclick = async () => {
            pesquisaAlunosLimpar();

            const pesquisa = document.getElementById('input-pesquisa-aluno').value;

            const response = await fetch('/admin/usuarios/listar', {
                headers: { 'Accept': 'application/json' },
                method: 'POST',
                body: JSON.stringify({ filtros: { nome: pesquisa, tipo: 'aluno' } })
            });
            const textProm = response.text();

            if (response.status != 200) {
                Swal.fire({
                    icon: 'error',
                    text: 'Não foi possível realizar a pesquisa'
                });
                console.log(await textProm);
                return;
            }

            const ret = await textProm;
            try {
                pesquisaAlunosPreencher(JSON.parse(ret));
            } catch (err) {
                console.error(err, '\n', ret);
            }
        };

        //
        // Adicionar alunos dinamicamente >> Pesquisa por turma
        //

        const pesquisaAlunoSelectAno = document.getElementById('pesquisa-aluno-select-ano');
        const pesquisaAlunoSelectTurma = document.getElementById('pesquisa-aluno-select-turma');

        let turmasPorAno;

        (async () => {
            const response = await fetch('listar', {
                method: 'POST',
                headers: {'Accept': 'application/json'},
                body: JSON.stringify({ 'agrupar_por': 'ano' })
            });
            const textProm = response.text();
            if (response.status != 200) {
                Swal.fire({
                    icon: 'error',
                    warning: 'Não foi possível carregar as turmas para a pesquisa por alunos'
                });
                console.log(await textProm);
                return;
            }

            const ret = await textProm;
            try {
                turmasPorAno = JSON.parse(ret);
                for (const ano in turmasPorAno) {
                    criarElemento('option', [], pesquisaAlunoSelectAno, {
                        value: ano,
                        innerText: ano
                    });
                }
            } catch (err) {
                console.error(err, '\n', ret);
            }
        })();

        pesquisaAlunoSelectAno.onchange = () => {
            pesquisaAlunosLimpar();

            removerFilhos(pesquisaAlunoSelectTurma);
            const ano = Number(pesquisaAlunoSelectAno.value);
            if (ano == -1) {
                pesquisaAlunoSelectTurma.disabled = true;
                return;
            }
            pesquisaAlunoSelectTurma.disabled = false;
            criarElemento('option', [], pesquisaAlunoSelectTurma, {
                value: -1,
                innerText: '- Turma -'
            });
            for (const turma of turmasPorAno[ano])
                criarElemento('option', [], pesquisaAlunoSelectTurma, {
                    value: turma.id,
                    innerText: turma.nome
                });
        };

        pesquisaAlunoSelectTurma.onchange = async () => {
            pesquisaAlunosLimpar();

            const idTurma = pesquisaAlunoSelectTurma.value;
            if (idTurma == -1) return;

            const response = await fetch(`alunos?id=${idTurma}`);
            const textProm = response.text();

            if (response.status != 200) {
                Swal.fire({
                    icon: 'error',
                    text: 'Não foi possível carregar os alunos da turma selecionada'
                });
                console.log(await textProm);
                return;
            }

            const ret = await textProm;
            try {
                pesquisaAlunosPreencher(JSON.parse(ret));
            } catch (err) {
                console.error(err, '\n', ret);
            }
        }

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

        if (idTurmaAlterar != -1) (async () => {
            const response = await fetch(`turma?id=${idTurmaAlterar}`, { headers: { Accept: 'application/json' }});
            const textProm = response.text();

            if (response.status != 200) {
                agendarAlertaSwal({
                    icon: 'error',
                    text: 'Não foi possível carregar os dados da turma apra alterá-la'
                });
                location.assign(`turma?id=${idTurmaAlterar}`)
                console.log(await textProm);
            }
            const ret = await textProm;
            try {
                const turma = JSON.parse(ret);
                if (!turma.podeExcluir) {
                    document.getElementById('btn-excluir-turma').disabled = true;
                    const btnExcluirWrapper = document.getElementById('btn-excluir-turma-wrapper');
                    btnExcluirWrapper.setAttribute('data-bs-toggle', 'tooltip');
                    btnExcluirWrapper.title = 'Essa turma não pode ser excluída porque alguma de suas disciplinas não pode ser excluída';
                    new bootstrap.Tooltip(btnExcluirWrapper);
                }
                document.getElementById('turma-nome').value = turma.nome;
                document.getElementById('turma-ano').value = turma.ano;
                for (const aluno of turma.alunos) {
                    adicionarAlunoTurma(aluno.id, aluno.nome, aluno.login);
                }
                for (const disciplina of turma.disciplinas) {
                    disciplinasContainer.appendChild(criarDisciplina(disciplina));
                }
            } catch (err) {
                console.error(err, '\n', ret);
            }
        })();

        //
        // Exclusão de turma (quando alterar)
        //

        const btnConfirmarExclusao = document.getElementById('btn-confirmar-exclusao');

        btnConfirmarExclusao?.addEventListener('click', async () => {
            const id = btnConfirmarExclusao.getAttribute('data-id');

            const response = await fetch('excluir', {method: 'DELETE', body: JSON.stringify({id})});
            const textProm = response.text();

            if (response.status != 200) {
                Swal.fire({
                    icon: 'error',
                    text: 'Não foi possível excluir a turma'
                });
                document.getElementById('btn-cancelar-exclusao').click();  // fechar o modal
                console.log(await textProm);
                return;
            }

            agendarAlertaSwal({
                icon: 'success',
                text: 'Turma excluída com sucesso'
            });
            location.assign('listar');
        });

    </script>
    

</body>
</html>