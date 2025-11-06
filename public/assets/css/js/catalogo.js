// public/assets/js/catalogo.js
document.addEventListener('DOMContentLoaded', function () {
    var filtrosForm = document.getElementById('filtrosForm');
    if (!filtrosForm) return;
    filtrosForm.addEventListener('submit', function () {
        // evento submit - puedes añadir analytics
    });
    var selects = filtrosForm.querySelectorAll('select');
    selects.forEach(function (sel) {
        sel.addEventListener('change', function () {
            filtrosForm.submit();
        });
    });
});