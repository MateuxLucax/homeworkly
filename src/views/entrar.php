<!DOCTYPE html>
<html lang="pt-BR">
<?php
    require $root . 'views/componentes/head.php';
?>

<body class="position-relative" style="height: 100vh;">

<main class="container-fluid d-flex flex-column align-items-center justify-content-center h-100">

    <form onsubmit="submitLogin(event)" method="post">
        <img src="/static/images/full-logo.svg" class="img-fluid mx-auto d-block mb-5" alt="HomeWorkly logo">

        <div class="mb-4">
            <label for="login" class="form-label">Usuário</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-person-fill"></i>
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
                <i class="bi bi-lock-fill"></i>
            </span>
                <input type="password"
                       class="form-control"
                       name="senha"
                       id="senha"
                       placeholder="Senha"
                       aria-label="Usuário"
                       required />
                <button class="btn btn-outline-secondary" onclick="toggleVisibility()" type="button"><i id="icone-senha" class="bi bi-eye-slash-fill"></i></button>
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
    const body = document.querySelector('body');

    const toggleVisibility = () => {
        const passwordIcon = document.querySelector('#icone-senha');
        const password = document.querySelector('#senha');

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        const icon = passwordIcon.classList.contains('bi-eye-fill') ? 'bi-eye-slash-fill' : 'bi-eye-fill';

        password.setAttribute('type', type);
        passwordIcon.classList.toggle(icon);
    }

    const insertFailedToLoginToast = (message) => {
        const toast = document.querySelector("#failed-login-toast");
        if (toast) toast.remove();

        const html = `<div style="margin-top: 32px; margin-right: 32px;" id="failed-login-toast" class="position-absolute top-0 end-0 toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                          <div class="d-flex">
                            <div class="toast-body">
                              ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                          </div>
                        </div>`;

        body.insertAdjacentHTML("beforeend", html);

        (new bootstrap.Toast(document.querySelector('#failed-login-toast'))).show();
    }

    const submitLogin = (event) => {
        event.preventDefault();

        const payload = {
            login: form.login.value,
            senha: form.senha.value
        };

        fetch('auth', { method: 'POST', body: JSON.stringify(payload) })
        .then(response => {
            if (response.status === 200) {
                response.json().then(json => {
                    window.location.assign(json.location);
                });
            } else {
                response.json().then(json => {
                    // TODO usar sweetalert
                    insertFailedToLoginToast(json.message);
                });
            }
        });
    }
</script>
</body>
</html>