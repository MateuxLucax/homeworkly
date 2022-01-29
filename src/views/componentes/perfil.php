<div class="card bg-light p-4">
    <div class="card-body d-flex flex-column align-items-center">
        <h2 class="text-center"><i class="far fa-user me-2 pb-4"></i>Perfil</h2>

        <div class="w-50">

            <div class="mb-4">
                <label for="nome" class="form-label">Nome</label>
                <div class="input-group">
                <span class="input-group-text">
                    <i class="far fa-user-circle"></i>
                </span>
                    <input type="text"
                           class="form-control"
                           name="nome"
                           id="nome"
                           value="<?= $view['perfil_nome']?>"
                           aria-label="Nome"
                           disabled />
                </div>
            </div>

            <div>
                <label for="email" class="form-label">Login</label>
                <div class="input-group">
                <span class="input-group-text">
                    <i class="far fa-user"></i>
                </span>
                    <input type="text"
                           class="form-control"
                           name="login"
                           id="login"
                           aria-label="Login"
                           value="<?= $view['perfil_login']?>"
                           disabled />
                </div>
            </div>
        </div>

        <form onsubmit="submitLogin(event)" method="post" class="py-4 w-50">
            <h5 class="text-center">Alterar senha</h5>
            <div class="mb-4">
                <label for="senhaAtual" class="form-label">Senha atual</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password"
                           class="form-control"
                           name="senha-atual"
                           id="senhaAtual"
                           placeholder="********"
                           aria-label="Senha atual"
                           required />
                    <button class="btn btn-outline-secondary" onclick="toggleVisibilityAtual()" type="button"><i id="icone-senha-atual" class="fas fa-eye-slash"></i></button>
                </div>
            </div>

            <div class="mb-4">
                <label for="senhaNova" class="form-label">Senha nova</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password"
                           class="form-control"
                           name="senha-nova"
                           id="senhaNova"
                           placeholder="********"
                           aria-label="Senha nova"
                           required />
                    <button class="btn btn-outline-secondary" onclick="toggleVisibilityNova()" type="button"><i id="icone-senha-nova" class="fas fa-eye-slash"></i></button>
                </div>
            </div>

            <div class="text-center">
                <button class="btn btn-primary" type="submit">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
    const toggleVisibilityAtual = () => {
        const passwordIcon = document.querySelector('#icone-senha-atual');
        const password = document.querySelector('#senhaAtual');

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        const icon = passwordIcon.classList.contains('fa-eye') ? 'fa-eye-slash' : 'fa-eye';

        password.setAttribute('type', type);
        passwordIcon.classList.toggle(icon);
    }
    const toggleVisibilityNova = () => {
        const passwordIcon = document.querySelector('#icone-senha-nova');
        const password = document.querySelector('#senhaNova');

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        const icon = passwordIcon.classList.contains('fa-eye') ? 'fa-eye-slash' : 'fa-eye';

        password.setAttribute('type', type);
        passwordIcon.classList.toggle(icon);
    }

    const form = document.querySelector('form');
    const submitLogin = async (event) => {
        event.preventDefault();

        const payload = {
            id: <?= $view['perfil_id_usuario']?>,
            senha_nova: form.senhaNova.value,
            senha_atual: form.senhaAtual.value
        };

        const response = await fetch(`<?= $view['perfil_alterar_senha']?>`, { method: 'PUT', body: JSON.stringify(payload) });
        const returnText = await response.text()
        try {
            const json = JSON.parse(returnText);
            if (response.status !== 200) {
                if (!json.hasOwnProperty('mensagem')) throw 'Erro no retorno';
                Swal.fire({
                    icon: 'warning',
                    text: json.mensagem
                });
                return;
            } else if (response.status === 200){
                Swal.fire({
                    icon: 'success',
                    text: 'Senha alterada com sucesso!'
                })
            }
        } catch (err) {
            console.error(err, '\n', returnText);
        } finally {
            form.reset();
        }
    }
</script>
