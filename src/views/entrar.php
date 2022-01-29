<!DOCTYPE html>
<html lang="pt-BR">
<?php require $root . 'views/componentes/head.php'; ?>

<body>

<main class="container-fluid min-vh-100 d-flex flex-column align-items-center justify-content-center h-100">

    <form onsubmit="submitLogin(event)" method="post">
        <img src="/static/images/full-logo.svg" class="img-fluid mx-auto d-block mb-5" alt="HomeWorkly logo">

        <div class="mb-4">
            <label for="login" class="form-label">Usuário</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="far fa-user"></i>
                </span>
                <input type="text"
                       class="form-control"
                       name="login"
                       id="login"
                       placeholder="Usuário"
                       aria-label="Usuário"
                       required />
            </div>
        </div>

        <div class="mb-4">
            <label for="senha" class="form-label">Senha</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
            </span>
                <input type="password"
                       class="form-control"
                       name="senha"
                       id="senha"
                       placeholder="Senha"
                       aria-label="Senha"
                       required />
                <button class="btn btn-outline-secondary" onclick="toggleVisibility()" type="button"><i id="icone-senha" class="fas fa-eye-slash"></i></button>
            </div>
        </div>

        <div class="checkbox mb-4">
            <label>
                <input type="checkbox" value="manter-conectador"> Mantenha-me conectado
            </label>
        </div>


        <div class="mb-4 d-flex">
            <a href="#" class="link-primary mx-auto">Esqueceu sua senha?</a>
        </div>

        <button class="w-100 btn btn-lg btn-primary" type="submit">Entrar</button>
    </form>

</main>

<script>
    const form = document.querySelector('form');

    const toggleVisibility = () => {
        const passwordIcon = document.querySelector('#icone-senha');
        const password = document.querySelector('#senha');

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        const icon = passwordIcon.classList.contains('fa-eye') ? 'fa-eye-slash' : 'fa-eye';

        password.setAttribute('type', type);
        passwordIcon.classList.toggle(icon);
    }

    const submitLogin = async (event) => {
        event.preventDefault();

        const payload = {
            login: form.login.value,
            senha: form.senha.value
        };

        const response = await fetch('auth', { method: 'POST', body: JSON.stringify(payload) });
        const returnText = await response.text()
        try {
            const json = JSON.parse(returnText);
            if (response.status !== 200) {
                if (!json.hasOwnProperty('message')) throw 'Erro no retorno';
                Swal.fire({
                    icon: 'warning',
                    text: json.message
                });
                return;
            }
            if (!json.hasOwnProperty('location')) throw 'Erro no retorno';
            location.assign(json.location);
        } catch (err) {
            console.error(err, '\n', returnText);
        }
    }
</script>
</body>
</html>