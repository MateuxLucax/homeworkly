<!doctype html>
<html lang="en">
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/../views/componentes/head.php'; ?>
<body>
    <div id='calendar'></div>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                themeSystem: 'bootstrap',
                dateClick: function() {
                    alert('a day has been clicked!');
                }
            });
            calendar.render();
        });

    </script>
</body>
</html>