<script>
    document.addEventListener('DOMContentLoaded', () => {
        const eventos = [JSON.parse(<?= $view['inicio_eventos'] ?>)];
        
        console.log(eventos);
        const calendarEl = document.querySelector('#calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            themeSystem: 'bootstrap',
            validRange: {
                start: '2022-01-01',
                end: '2023-01-01'
            },
            // selectable: true,
            // select: (selectInfo) => {
            //     const title = prompt('Informe o Título da tarefa;');
            //     console.log([selectInfo, title]);
            // }
            events: eventos
        });
        calendar.render();
    });
</script>

<div id='calendar'></div>