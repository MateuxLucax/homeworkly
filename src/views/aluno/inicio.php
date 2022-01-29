<script>
    document.addEventListener('DOMContentLoaded', () => {
        const calendarEl = document.querySelector('#calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            themeSystem: 'bootstrap',
            selectable: true,
            select: (selectInfo) => {
                const title = prompt('Informe o Título da tarefa;');
                console.log([selectInfo, title]);
            }
        });
        calendar.render();
    });
</script>

<div id='calendar'></div>