import * as bootstrap from 'bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const confirmModalEl = document.getElementById('confirmModal');
    if (!confirmModalEl) return;

    const confirmModal = new bootstrap.Modal(confirmModalEl);
    const mensagemEl = document.getElementById('confirmModalMensagem');
    const botaoConfirmar = document.getElementById('confirmModalBotaoConfirmar');

    let formPendente = null;

    document.querySelectorAll('.form-confirmar').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            formPendente = form;
            mensagemEl.innerText = form.dataset.confirmMessage || 'Tem certeza que deseja continuar?';
            confirmModal.show();
        });
    });

    botaoConfirmar.addEventListener('click', function () {
        confirmModal.hide();
        if (formPendente) {
            formPendente.submit();
        }
    });
});
