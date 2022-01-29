<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const response = await fetch(`<?= $view['inicio_eventos'] ?>`);
        const eventos = await response.json();
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
            events: eventos,
            eventContent: (args, createElement) => {
                const icone = args.event._def.extendedProps.icon;
                let retorno = args.event._def.title;
                if (icone) retorno = icone + args.event._def.title;
                return {
                    html: retorno
                };
            },
        });
        calendar.render();
    });
</script>

<div id='calendar'></div>