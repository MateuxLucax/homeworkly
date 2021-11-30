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

            <div class="text-end">
                <button type="submit" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-paper-plane"></i>&nbsp;
                    Criar
                </button>
            </div>

        </form>
    </main>

    <script>
        const form = document.getElementById('form-criar-turma');

        form.onsubmit = event => {
            event.preventDefault();

            // TODO validação dos campos

            const payload = {
                nome: form['turma-nome'].value,
                ano:  form['turma-ano'].value,
                disciplinas: Array.from(form['disciplina-nome[]']).map(i => i.value)
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
                name: 'disciplina-nome[]'
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
    </script>

</body>
</html>