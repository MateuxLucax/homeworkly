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

            <div class="text-end">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-plus-circle"></i>&nbsp;
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
                ano:  form['turma-ano'].value
            }

            fetch('criar', {method: 'POST', body: JSON.stringify(payload)})
            .then(response => {
                response.text().then(console.log);
                if (response.status == 201) {
                    agendarAlertaSwal({
                        icon: 'success',
                        text: 'Turma criada com sucesso'
                    })
                    window.location.assign('listar');
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: 'Não foi possível criar a turma'
                    });
                }
            });
        };
    </script>

</body>
</html>