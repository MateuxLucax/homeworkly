<nav class="navbar navbar-expand-sm navbar-light bg-light">
    <div class="container-fluid">
        <span class="navbar-brand">
            Homeworkly <!-- TODO colocar o logo aqui -->
        </span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-admin">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar-admin">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= $view['ativo-nav'] == 'usuarios' ? 'active' : '' ?>"
                       href="/admin/usuarios/listar">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $view['ativo-nav'] == 'turmas' ? 'active' : '' ?>"
                      href="/admin/turmas/listar">Turmas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>