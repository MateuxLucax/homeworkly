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
                const title = prompt('Informe o Título da tarefa;');
                console.log([selectInfo, title]);
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
