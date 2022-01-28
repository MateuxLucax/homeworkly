<div class="container-fluid">
    <div id='calendar'></div>
</div>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
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