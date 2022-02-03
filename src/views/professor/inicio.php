<script>
    const padTo2Digits = (num) => {
        return num.toString().padStart(2, '0');
    }

    const formatDate = (date) => {
        return (
            [
                date.getFullYear(),
                padTo2Digits(date.getMonth() + 1),
                padTo2Digits(date.getDate()),
            ].join('-') +
            ' ' + [
                padTo2Digits(date.getHours()),
                padTo2Digits(date.getMinutes()),
                padTo2Digits(date.getSeconds()),
            ].join(':')
        );
    }

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
                Cookies.set('dataAbertura', formatDate(selectInfo.start));
                Cookies.set('dataEntrega', formatDate(selectInfo.end));
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