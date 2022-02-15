<li class="nav-item mb-2">
    <a href="/professor" class="nav-link link-dark <?= active('/professor/'); ?>" aria-current="page">
        <i class="far fa-calendar-alt fa-fw me-2"></i>
        Calendário de tarefas
    </a>
</li>
<li class="nav-item mb-2">
    <a href="/professor/tarefas" class="nav-link link-dark  <?= active('/professor/tarefas/'); ?>">
        <i class="far fa-clipboard fa-fw me-2"></i>
        Lista de tarefas
    </a>
</li>
<li class="nav-item">
    <a href="/professor/alunos" class="nav-link link-dark  <?= active('/professor/alunos/'); ?>">
        <i class="fas fa-graduation-cap"></i>
        Alunos
    </a>
</li>

<hr>

<li class="nav-item">
    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#criar-tarefa-modal">
        Criar uma tarefa
        <i class="fas fa-plus ms-2"></i>
    </button>
</li>