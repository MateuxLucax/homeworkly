window.addEventListener('load', () => {
    const alertaSwal = sessionStorage.getItem('alerta-swal');
    if (alertaSwal !== null) {
        try {
            Swal.fire(JSON.parse(alertaSwal));
        } catch (error) {
            console.error(`Erro ao abrir o alerta: ${error}`);
        }
    }
    sessionStorage.removeItem('alerta-swal');
});

function agendarAlertaSwal(alertaSwal) {
    sessionStorage.setItem('alerta-swal', JSON.stringify(alertaSwal));
}