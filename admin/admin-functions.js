// admin/admin-functions.js
// Funciones útiles para todo el panel de administración

document.addEventListener("DOMContentLoaded", function () {

    // 1. Confirmación antes de guardar cambios
    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function (e) {
            if (!confirm("¿Estás seguro de que deseas guardar los cambios?")) {
                e.preventDefault();
            }
        });
    });

    // 2. Mensaje de "Guardado" bonito (opcional, queda muy pro)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("saved") === "1") {
        const toast = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
                <div class="toast align-items-center text-white bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            Cambios guardados correctamente
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>`;
        document.body.insertAdjacentHTML("beforeend", toast);
        new bootstrap.Toast(document.querySelector(".toast")).show();

        // Limpiar la URL sin recargar
        history.replaceState(null, null, window.location.pathname + window.location.search.split("&saved=1")[0]);
    }

    // 3. Atajo Ctrl+S removido: la edición de contenido web está deshabilitada.

    // 4. Mostrar/ocultar clave de sección al hacer clic en el título
    document.querySelectorAll(".card-header small.text-muted").forEach(el => {
        el.style.cursor = "pointer";
        el.title = "Haz clic para copiar la clave";
        el.addEventListener("click", function () {
            navigator.clipboard.writeText(this.textContent.replace("(Clave: ", "").replace(")", ""));
            alert("Clave copiada: " + this.textContent.match(/\((.*?)\)/)[1]);
        });
    });
});

// Evitar que el navegador muestre páginas protegidas desde el back/forward cache (bfcache)
window.addEventListener('pageshow', function (event) {
    // Si la página fue restaurada desde el cache de historial (persisted), recargar
    if (event.persisted) {
        window.location.reload();
    }
});