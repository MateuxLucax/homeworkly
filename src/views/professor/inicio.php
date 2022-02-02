<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const calendarEl = document.querySelector('#calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            themeSystem: 'bootstrap',
            validRange: {
                start: '<?= $view['ano_turma'] ?>-01-01',
                end: '<?= $view['ano_turma'] + 1 ?>-01-01'
            },
            selectable: true,
            select: (selectInfo) => {
                let modalCriarTarefa = new bootstrap.Modal(document.querySelector('#criar-tarefa-modal'), {});
                modalCriarTarefa.show();

            }
        });
        calendar.render();
        const response = await fetch(`<?= $view['inicio_eventos'] ?>`);
        const eventos = await response.json();
        eventos.forEach((evento) => {
            calendar.addEvent(evento);
        });
    });
</script>

<div id='calendar'></div>
