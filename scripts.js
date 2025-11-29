/* ==================================== */
/* 0. RETORNO AUTOMÁTICO AL INICIO      */
/* ==================================== */
// Asegura que la página siempre se cargue en la parte superior.
// 14. Evitar que el navegador recuerde la posición de scroll
window.history.scrollRestoration = "manual";

// 15. Forzar el scroll al inicio en la carga completa de la página
window.addEventListener("load", () => {
    // Un pequeño retraso para asegurar que el DOM ha terminado de cargarse.
    setTimeout(() => {
        window.scrollTo({ top: 0, left: 0 });
    }, 50);
});

document.addEventListener('DOMContentLoaded', () => {
    // Esto es ahora solo un respaldo para un retorno rápido.
    window.scrollTo(0, 0);

/* ==================================== */
/* 1. ANIMACIÓN DE ENTRADA (Al cargar)  */
/* ==================================== */

    // Aplica la clase 'is-visible' a los elementos del hero al cargar la página
    const heroElements = document.querySelectorAll('.hero-content .fade-in-up');
    setTimeout(() => {
        heroElements.forEach(el => el.classList.add('is-visible'));
    }, 100); // Pequeño retraso para asegurar que CSS ya esté cargado

/* ==================================== */
/* 2. ANIMACIONES AL HACER SCROLL (Activación permanente) */
/* ==================================== */

    // Configuración del observador: Mantenemos el elemento visible, pero ajustamos la lógica.
    const observerOptions = {
        root: null, // El viewport
        rootMargin: '0px',
        threshold: 0.1 // 10% del elemento visible
    };

    // Función que se ejecuta cuando la visibilidad de un elemento cambia
    const observerCallback = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Si el elemento ENTRA en la vista, añadimos la clase de animación
                entry.target.classList.add('is-visible');
            } else {
                // Si el elemento SALE de la vista, eliminamos la clase.
                // Esto permite que la animación se repita la próxima vez que entre.
                entry.target.classList.remove('is-visible');
            }
        });
    };

    // Crear el Intersection Observer
    const observer = new IntersectionObserver(observerCallback, observerOptions);

    // Obtener todos los elementos que se pueden animar
    const animatableElements = document.querySelectorAll(
        '.reveal, .reveal-left, .reveal-right, .reveal-zoom'
    );

    // Observar cada elemento. No usamos unobserve, por lo que siempre están activos.
    animatableElements.forEach(el => observer.observe(el));


/* ==================================== */
/* 3. MENÚ DE NAVEGACIÓN (Móvil)        */
/* ==================================== */

    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    const navLinks = document.querySelectorAll('.main-nav .nav-link');

    // Función para alternar el menú
    menuToggle.addEventListener('click', () => {
        mainNav.classList.toggle('is-open');
        // Cambiar el ícono de hamburguesa a X y viceversa
        const icon = menuToggle.querySelector('i');
        if (mainNav.classList.contains('is-open')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });

    // Cerrar el menú después de hacer click en un enlace (útil en móvil)
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (mainNav.classList.contains('is-open')) {
                mainNav.classList.remove('is-open');
                menuToggle.querySelector('i').classList.remove('fa-times');
                menuToggle.querySelector('i').classList.add('fa-bars');
            }
        });
    });

/* ==================================== */
/* 4. LÓGICA DEL MODAL DE CONTACTO      */
/* ==================================== */

const contactanosBtn = document.getElementById('contactanosBtn'); 
const modal = document.getElementById('contact-modal');
const closeBtn = document.querySelector('.custom-modal .close-btn');
const selectorPanel = document.querySelector('.selector-panel');
const btnNuevos = document.getElementById('btnNuevos');
const btnMiembros = document.getElementById('btnMiembros');
const formNuevos = document.getElementById('formNuevos');
const formMiembros = document.getElementById('formMiembros');

// --- Funciones del Modal ---
function openModal(e) {
    e.preventDefault(); 
    modal.style.display = 'block';
    
    // Mostrar la pantalla de selección por defecto
    selectorPanel.classList.remove('d-none');
    formNuevos.classList.add('d-none');
    formMiembros.classList.add('d-none');
}

function closeModal() {
    modal.style.display = 'none';
}

// Event Listeners para abrir/cerrar
if (contactanosBtn) contactanosBtn.addEventListener('click', openModal);
if (closeBtn) closeBtn.addEventListener('click', closeModal);

// Cerrar al hacer click fuera del modal
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        closeModal();
    }
});

// --- Lógica de Selección de Formulario ---
if (btnNuevos) {
    btnNuevos.addEventListener('click', () => {
        selectorPanel.classList.add('d-none');
        formNuevos.classList.remove('d-none');
        // Inicializar el selector de teléfono de "Nuevos Usuarios" al mostrar el formulario
        if (typeof initializePhoneSelector === 'function') {
            initializePhoneSelector(); 
        }
    });
}

if (btnMiembros) {
    btnMiembros.addEventListener('click', () => {
        selectorPanel.classList.add('d-none');
        formMiembros.classList.remove('d-none');
    });
}


// --- Lógica para campos condicionales (Petición de Oración y Cómo te Enteraste) ---
const peticionOracionSelect = document.getElementById('peticionOracion');
const peticionOracionOtroDiv = document.getElementById('peticionOracionOtro');
const comoEnterasteSelect = document.getElementById('comoEnteraste');
const otroCampoDiv = document.getElementById('otroCampo');

if (peticionOracionSelect) {
    peticionOracionSelect.addEventListener('change', (e) => {
        if (e.target.value === 'otro') {
            peticionOracionOtroDiv.classList.remove('d-none');
        } else {
            peticionOracionOtroDiv.classList.add('d-none');
        }
    });
}

if (comoEnterasteSelect) {
    comoEnterasteSelect.addEventListener('change', (e) => {
        if (e.target.value === 'otro') {
            otroCampoDiv.classList.remove('d-none');
        } else {
            otroCampoDiv.classList.add('d-none');
        }
    });
}

/* ==================================== */
/* 5. ANIMACIÓN ACTIVA DEL FOOTER       */
/* ==================================== */

    // 1. Obtener el elemento footer
    const footer = document.querySelector('.main-footer');

    // 2. Aplicar la animación CSS directamente con JavaScript
    if (footer) {
        footer.style.animation = 'floatUpAndDown 4s ease-in-out infinite';
    }


/* ==================================== */
/* 6. LÓGICA DE SELECCIÓN DE BANDERA (MODAL: Nuevos Usuarios) */
/* (Con el sistema de búsqueda y banderas que solicitaste) */
/* ==================================== */

/* ===========================================================
   PREFIJOS - VALIDACIÓN - TELÉFONO FINAL (Actualizado)
   ============================================================ */
const prefijos = [
  { nombre: "Afganistán", abbr: "AF", prefijo: "+93", min: 9, max: 9, bandera: "https://flagcdn.com/af.svg" },
  { nombre: "Albania", abbr: "AL", prefijo: "+355", min: 8, max: 9, bandera: "https://flagcdn.com/al.svg" },
  { nombre: "Alemania", abbr: "DE", prefijo: "+49", min: 8, max: 12, bandera: "https://flagcdn.com/de.svg" },
  { nombre: "Andorra", abbr: "AD", prefijo: "+376", min: 6, max: 9, bandera: "https://flagcdn.com/ad.svg" },
  { nombre: "Angola", abbr: "AO", prefijo: "+244", min: 9, max: 9, bandera: "https://flagcdn.com/ao.svg" },
  { nombre: "Antigua y Barbuda", abbr: "AG", prefijo: "+1-268", min: 7, max: 7, bandera: "https://flagcdn.com/ag.svg" },
  { nombre: "Arabia Saudita", abbr: "SA", prefijo: "+966", min: 9, max: 9, bandera: "https://flagcdn.com/sa.svg" },
  { nombre: "Argelia", abbr: "DZ", prefijo: "+213", min: 9, max: 9, bandera: "https://flagcdn.com/dz.svg" },
  { nombre: "Argentina", abbr: "AR", prefijo: "+54", min: 10, max: 11, bandera: "https://flagcdn.com/ar.svg" },
  { nombre: "Armenia", abbr: "AM", prefijo: "+374", min: 8, max: 8, bandera: "https://flagcdn.com/am.svg" },
  { nombre: "Australia", abbr: "AU", prefijo: "+61", min: 8, max: 9, bandera: "https://flagcdn.com/au.svg" },
  { nombre: "Austria", abbr: "AT", prefijo: "+43", min: 4, max: 13, bandera: "https://flagcdn.com/at.svg" },
  { nombre: "Azerbaiyán", abbr: "AZ", prefijo: "+994", min: 9, max: 9, bandera: "https://flagcdn.com/az.svg" },
  { nombre: "Bahamas", abbr: "BS", prefijo: "+1-242", min: 7, max: 7, bandera: "https://flagcdn.com/bs.svg" },
  { nombre: "Bangladés", abbr: "BD", prefijo: "+880", min: 10, max: 10, bandera: "https://flagcdn.com/bd.svg" },
  { nombre: "Barbados", abbr: "BB", prefijo: "+1-246", min: 7, max: 7, bandera: "https://flagcdn.com/bb.svg" },
  { nombre: "Baréin", abbr: "BH", prefijo: "+973", min: 8, max: 8, bandera: "https://flagcdn.com/bh.svg" },
  { nombre: "Bélgica", abbr: "BE", prefijo: "+32", min: 8, max: 9, bandera: "https://flagcdn.com/be.svg" },
  { nombre: "Belice", abbr: "BZ", prefijo: "+501", min: 7, max: 7, bandera: "https://flagcdn.com/bz.svg" },
  { nombre: "Benín", abbr: "BJ", prefijo: "+229", min: 8, max: 8, bandera: "https://flagcdn.com/bj.svg" },
  { nombre: "Bielorrusia", abbr: "BY", prefijo: "+375", min: 9, max: 9, bandera: "https://flagcdn.com/by.svg" },
  { nombre: "Bolivia", abbr: "BO", prefijo: "+591", min: 8, max: 8, bandera: "https://flagcdn.com/bo.svg" },
  { nombre: "Bosnia y Herzegovina", abbr: "BA", prefijo: "+387", min: 8, max: 8, bandera: "https://flagcdn.com/ba.svg" },
  { nombre: "Botsuana", abbr: "BW", prefijo: "+267", min: 7, max: 8, bandera: "https://flagcdn.com/bw.svg" },
  { nombre: "Brasil", abbr: "BR", prefijo: "+55", min: 10, max: 11, bandera: "https://flagcdn.com/br.svg" },
  { nombre: "Brunéi", abbr: "BN", prefijo: "+673", min: 7, max: 7, bandera: "https://flagcdn.com/bn.svg" },
  { nombre: "Bulgaria", abbr: "BG", prefijo: "+359", min: 8, max: 9, bandera: "https://flagcdn.com/bg.svg" },
  { nombre: "Burkina Faso", abbr: "BF", prefijo: "+226", min: 8, max: 8, bandera: "https://flagcdn.com/bf.svg" },
  { nombre: "Burundi", abbr: "BI", prefijo: "+257", min: 8, max: 8, bandera: "https://flagcdn.com/bi.svg" },
  { nombre: "Cabo Verde", abbr: "CV", prefijo: "+238", min: 7, max: 7, bandera: "https://flagcdn.com/cv.svg" },
  { nombre: "Camboya", abbr: "KH", prefijo: "+855", min: 8, max: 9, bandera: "https://flagcdn.com/kh.svg" },
  { nombre: "Camerún", abbr: "CM", prefijo: "+237", min: 9, max: 9, bandera: "https://flagcdn.com/cm.svg" },
  { nombre: "Canadá", abbr: "CA", prefijo: "+1", min: 10, max: 10, bandera: "https://flagcdn.com/ca.svg" },
  { nombre: "Catar", abbr: "QA", prefijo: "+974", min: 8, max: 8, bandera: "https://flagcdn.com/qa.svg" },
  { nombre: "Chad", abbr: "TD", prefijo: "+235", min: 8, max: 8, bandera: "https://flagcdn.com/td.svg" },
  { nombre: "Chile", abbr: "CL", prefijo: "+56", min: 8, max: 9, bandera: "https://flagcdn.com/cl.svg" },
  { nombre: "China", abbr: "CN", prefijo: "+86", min: 8, max: 11, bandera: "https://flagcdn.com/cn.svg" },
  { nombre: "Chipre", abbr: "CY", prefijo: "+357", min: 8, max: 8, bandera: "https://flagcdn.com/cy.svg" },
  { nombre: "Colombia", abbr: "CO", prefijo: "+57", min: 8, max: 10, bandera: "https://flagcdn.com/co.svg" },
  { nombre: "Comoras", abbr: "KM", prefijo: "+269", min: 7, max: 7, bandera: "https://flagcdn.com/km.svg" },
  { nombre: "Congo", abbr: "CG", prefijo: "+242", min: 9, max: 9, bandera: "https://flagcdn.com/cg.svg" },
  { nombre: "Corea del Norte", abbr: "KP", prefijo: "+850", min: 7, max: 9, bandera: "https://flagcdn.com/kp.svg" },
  { nombre: "Corea del Sur", abbr: "KR", prefijo: "+82", min: 7, max: 10, bandera: "https://flagcdn.com/kr.svg" },
  { nombre: "Costa Rica", abbr: "CR", prefijo: "+506", min: 8, max: 8, bandera: "https://flagcdn.com/cr.svg" },
  { nombre: "Croacia", abbr: "HR", prefijo: "+385", min: 8, max: 9, bandera: "https://flagcdn.com/hr.svg" },
  { nombre: "Cuba", abbr: "CU", prefijo: "+53", min: 6, max: 8, bandera: "https://flagcdn.com/cu.svg" },
  { nombre: "Dinamarca", abbr: "DK", prefijo: "+45", min: 8, max: 8, bandera: "https://flagcdn.com/dk.svg" },
  { nombre: "Dominica", abbr: "DM", prefijo: "+1-767", min: 7, max: 7, bandera: "https://flagcdn.com/dm.svg" },
  { nombre: "Ecuador", abbr: "EC", prefijo: "+593", min: 8, max: 9, bandera: "https://flagcdn.com/ec.svg" },
  { nombre: "Egipto", abbr: "EG", prefijo: "+20", min: 8, max: 10, bandera: "https://flagcdn.com/eg.svg" },
  { nombre: "El Salvador", abbr: "SV", prefijo: "+503", min: 8, max: 8, bandera: "https://flagcdn.com/sv.svg" },
  { nombre: "Emiratos Árabes Unidos", abbr: "AE", prefijo: "+971", min: 8, max: 9, bandera: "https://flagcdn.com/ae.svg" },
  { nombre: "Eritrea", abbr: "ER", prefijo: "+291", min: 7, max: 7, bandera: "https://flagcdn.com/er.svg" },
  { nombre: "Eslovaquia", abbr: "SK", prefijo: "+421", min: 8, max: 9, bandera: "https://flagcdn.com/sk.svg" },
  { nombre: "Eslovenia", abbr: "SI", prefijo: "+386", min: 8, max: 8, bandera: "https://flagcdn.com/si.svg" },
  { nombre: "España", abbr: "ES", prefijo: "+34", min: 9, max: 9, bandera: "https://flagcdn.com/es.svg" },
  { nombre: "Estados Unidos", abbr: "US", prefijo: "+1", min: 10, max: 10, bandera: "https://flagcdn.com/us.svg" },
  { nombre: "Estonia", abbr: "EE", prefijo: "+372", min: 7, max: 8, bandera: "https://flagcdn.com/ee.svg" },
  { nombre: "Etiopía", abbr: "ET", prefijo: "+251", min: 9, max: 9, bandera: "https://flagcdn.com/et.svg" },
  { nombre: "Filipinas", abbr: "PH", prefijo: "+63", min: 7, max: 10, bandera: "https://flagcdn.com/ph.svg" },
  { nombre: "Finlandia", abbr: "FI", prefijo: "+358", min: 5, max: 12, bandera: "https://flagcdn.com/fi.svg" },
  { nombre: "Fiyi", abbr: "FJ", prefijo: "+679", min: 7, max: 7, bandera: "https://flagcdn.com/fj.svg" },
  { nombre: "Francia", abbr: "FR", prefijo: "+33", min: 9, max: 9, bandera: "https://flagcdn.com/fr.svg" },
  { nombre: "Gabón", abbr: "GA", prefijo: "+241", min: 7, max: 8, bandera: "https://flagcdn.com/ga.svg" },
  { nombre: "Gambia", abbr: "GM", prefijo: "+220", min: 7, max: 7, bandera: "https://flagcdn.com/gm.svg" },
  { nombre: "Georgia", abbr: "GE", prefijo: "+995", min: 8, max: 9, bandera: "https://flagcdn.com/ge.svg" },
  { nombre: "Ghana", abbr: "GH", prefijo: "+233", min: 9, max: 9, bandera: "https://flagcdn.com/gh.svg" },
  { nombre: "Granada", abbr: "GD", prefijo: "+1-473", min: 7, max: 7, bandera: "https://flagcdn.com/gd.svg" },
  { nombre: "Grecia", abbr: "GR", prefijo: "+30", min: 10, max: 10, bandera: "https://flagcdn.com/gr.svg" },
  { nombre: "Guatemala", abbr: "GT", prefijo: "+502", min: 8, max: 8, bandera: "https://flagcdn.com/gt.svg" },
  { nombre: "Guinea", abbr: "GN", prefijo: "+224", min: 8, max: 9, bandera: "https://flagcdn.com/gn.svg" },
  { nombre: "Guinea-Bisáu", abbr: "GW", prefijo: "+245", min: 7, max: 9, bandera: "https://flagcdn.com/gw.svg" },
  { nombre: "Guyana", abbr: "GY", prefijo: "+592", min: 7, max: 7, bandera: "https://flagcdn.com/gy.svg" },
  { nombre: "Haití", abbr: "HT", prefijo: "+509", min: 8, max: 8, bandera: "https://flagcdn.com/ht.svg" },
  { nombre: "Honduras", abbr: "HN", prefijo: "+504", min: 8, max: 8, bandera: "https://flagcdn.com/hn.svg" },
  { nombre: "Hungría", abbr: "HU", prefijo: "+36", min: 8, max: 9, bandera: "https://flagcdn.com/hu.svg" },
  { nombre: "India", abbr: "IN", prefijo: "+91", min: 10, max: 10, bandera: "https://flagcdn.com/in.svg" },
  { nombre: "Indonesia", abbr: "ID", prefijo: "+62", min: 8, max: 13, bandera: "https://flagcdn.com/id.svg" },
  { nombre: "Irak", abbr: "IQ", prefijo: "+964", min: 8, max: 10, bandera: "https://flagcdn.com/iq.svg" },
  { nombre: "Irán", abbr: "IR", prefijo: "+98", min: 10, max: 10, bandera: "https://flagcdn.com/ir.svg" },
  { nombre: "Irlanda", abbr: "IE", prefijo: "+353", min: 7, max: 10, bandera: "https://flagcdn.com/ie.svg" },
  { nombre: "Islandia", abbr: "IS", prefijo: "+354", min: 7, max: 9, bandera: "https://flagcdn.com/is.svg" },
  { nombre: "Islas Cook", abbr: "CK", prefijo: "+682", min: 5, max: 5, bandera: "https://flagcdn.com/ck.svg" },
  { nombre: "Islas Salomón", abbr: "SB", prefijo: "+677", min: 5, max: 7, bandera: "https://flagcdn.com/sb.svg" },
  { nombre: "Israel", abbr: "IL", prefijo: "+972", min: 8, max: 9, bandera: "https://flagcdn.com/il.svg" },
  { nombre: "Italia", abbr: "IT", prefijo: "+39", min: 9, max: 10, bandera: "https://flagcdn.com/it.svg" },
  { nombre: "Jamaica", abbr: "JM", prefijo: "+1-876", min: 7, max: 7, bandera: "https://flagcdn.com/jm.svg" },
  { nombre: "Japón", abbr: "JP", prefijo: "+81", min: 8, max: 10, bandera: "https://flagcdn.com/jp.svg" },
  { nombre: "Jordania", abbr: "JO", prefijo: "+962", min: 8, max: 9, bandera: "https://flagcdn.com/jo.svg" },
  { nombre: "Kazajistán", abbr: "KZ", prefijo: "+7", min: 10, max: 10, bandera: "https://flagcdn.com/kz.svg" },
  { nombre: "Kenia", abbr: "KE", prefijo: "+254", min: 9, max: 9, bandera: "https://flagcdn.com/ke.svg" },
  { nombre: "Kirguistán", abbr: "KG", prefijo: "+996", min: 9, max: 9, bandera: "https://flagcdn.com/kg.svg" },
  { nombre: "Kiribati", abbr: "KI", prefijo: "+686", min: 5, max: 5, bandera: "https://flagcdn.com/ki.svg" },
  { nombre: "Kuwait", abbr: "KW", prefijo: "+965", min: 8, max: 8, bandera: "https://flagcdn.com/kw.svg" },
  { nombre: "Laos", abbr: "LA", prefijo: "+856", min: 8, max: 8, bandera: "https://flagcdn.com/la.svg" },
  { nombre: "Lesoto", abbr: "LS", prefijo: "+266", min: 8, max: 8, bandera: "https://flagcdn.com/ls.svg" },
  { nombre: "Letonia", abbr: "LV", prefijo: "+371", min: 8, max: 8, bandera: "https://flagcdn.com/lv.svg" },
  { nombre: "Líbano", abbr: "LB", prefijo: "+961", min: 7, max: 8, bandera: "https://flagcdn.com/lb.svg" },
  { nombre: "Liberia", abbr: "LR", prefijo: "+231", min: 7, max: 8, bandera: "https://flagcdn.com/lr.svg" },
  { nombre: "Libia", abbr: "LY", prefijo: "+218", min: 9, max: 9, bandera: "https://flagcdn.com/ly.svg" },
  { nombre: "Liechtenstein", abbr: "LI", prefijo: "+423", min: 7, max: 9, bandera: "https://flagcdn.com/li.svg" },
  { nombre: "Lituania", abbr: "LT", prefijo: "+370", min: 8, max: 8, bandera: "https://flagcdn.com/lt.svg" },
  { nombre: "Luxemburgo", abbr: "LU", prefijo: "+352", min: 4, max: 9, bandera: "https://flagcdn.com/lu.svg" },
  { nombre: "Madagascar", abbr: "MG", prefijo: "+261", min: 9, max: 9, bandera: "https://flagcdn.com/mg.svg" },
  { nombre: "Malasia", abbr: "MY", prefijo: "+60", min: 7, max: 10, bandera: "https://flagcdn.com/my.svg" },
  { nombre: "Malaui", abbr: "MW", prefijo: "+265", min: 7, max: 9, bandera: "https://flagcdn.com/mw.svg" },
  { nombre: "Maldivas", abbr: "MV", prefijo: "+960", min: 7, max: 7, bandera: "https://flagcdn.com/mv.svg" },
  { nombre: "Malí", abbr: "ML", prefijo: "+223", min: 8, max: 8, bandera: "https://flagcdn.com/ml.svg" },
  { nombre: "Malta", abbr: "MT", prefijo: "+356", min: 8, max: 8, bandera: "https://flagcdn.com/mt.svg" },
  { nombre: "Marruecos", abbr: "MA", prefijo: "+212", min: 9, max: 9, bandera: "https://flagcdn.com/ma.svg" },
  { nombre: "Marshall", abbr: "MH", prefijo: "+692", min: 7, max: 7, bandera: "https://flagcdn.com/mh.svg" },
  { nombre: "Mauricio", abbr: "MU", prefijo: "+230", min: 7, max: 8, bandera: "https://flagcdn.com/mu.svg" },
  { nombre: "Mauritania", abbr: "MR", prefijo: "+222", min: 8, max: 8, bandera: "https://flagcdn.com/mr.svg" },
  { nombre: "México", abbr: "MX", prefijo: "+52", min: 10, max: 10, bandera: "https://flagcdn.com/mx.svg" },
  { nombre: "Micronesia", abbr: "FM", prefijo: "+691", min: 7, max: 7, bandera: "https://flagcdn.com/fm.svg" },
  { nombre: "Moldavia", abbr: "MD", prefijo: "+373", min: 8, max: 8, bandera: "https://flagcdn.com/md.svg" },
  { nombre: "Mónaco", abbr: "MC", prefijo: "+377", min: 8, max: 9, bandera: "https://flagcdn.com/mc.svg" },
  { nombre: "Mongolia", abbr: "MN", prefijo: "+976", min: 8, max: 8, bandera: "https://flagcdn.com/mn.svg" },
  { nombre: "Montenegro", abbr: "ME", prefijo: "+382", min: 8, max: 8, bandera: "https://flagcdn.com/me.svg" },
  { nombre: "Mozambique", abbr: "MZ", prefijo: "+258", min: 8, max: 9, bandera: "https://flagcdn.com/mz.svg" },
  { nombre: "Namibia", abbr: "NA", prefijo: "+264", min: 7, max: 9, bandera: "https://flagcdn.com/na.svg" },
  { nombre: "Nauru", abbr: "NR", prefijo: "+674", min: 5, max: 7, bandera: "https://flagcdn.com/nr.svg" },
  { nombre: "Nepal", abbr: "NP", prefijo: "+977", min: 8, max: 10, bandera: "https://flagcdn.com/np.svg" },
  { nombre: "Nicaragua", abbr: "NI", prefijo: "+505", min: 8, max: 8, bandera: "https://flagcdn.com/ni.svg" },
  { nombre: "Níger", abbr: "NE", prefijo: "+227", min: 8, max: 8, bandera: "https://flagcdn.com/ne.svg" },
  { nombre: "Nigeria", abbr: "NG", prefijo: "+234", min: 8, max: 10, bandera: "https://flagcdn.com/ng.svg" },
  { nombre: "Noruega", abbr: "NO", prefijo: "+47", min: 8, max: 8, bandera: "https://flagcdn.com/no.svg" },
  { nombre: "Nueva Zelanda", abbr: "NZ", prefijo: "+64", min: 8, max: 10, bandera: "https://flagcdn.com/nz.svg" },
  { nombre: "Omán", abbr: "OM", prefijo: "+968", min: 8, max: 8, bandera: "https://flagcdn.com/om.svg" },
  { nombre: "Países Bajos", abbr: "NL", prefijo: "+31", min: 9, max: 9, bandera: "https://flagcdn.com/nl.svg" },
  { nombre: "Pakistán", abbr: "PK", prefijo: "+92", min: 9, max: 10, bandera: "https://flagcdn.com/pk.svg" },
  { nombre: "Palaos", abbr: "PW", prefijo: "+680", min: 7, max: 7, bandera: "https://flagcdn.com/pw.svg" },
  { nombre: "Panamá", abbr: "PA", prefijo: "+507", min: 8, max: 8, bandera: "https://flagcdn.com/pa.svg" },
  { nombre: "Papúa Nueva Guinea", abbr: "PG", prefijo: "+675", min: 7, max: 8, bandera: "https://flagcdn.com/pg.svg" },
  { nombre: "Paraguay", abbr: "PY", prefijo: "+595", min: 9, max: 9, bandera: "https://flagcdn.com/py.svg" },
  { nombre: "Perú", abbr: "PE", prefijo: "+51", min: 8, max: 9, bandera: "https://flagcdn.com/pe.svg" },
  { nombre: "Polonia", abbr: "PL", prefijo: "+48", min: 9, max: 9, bandera: "https://flagcdn.com/pl.svg" },
  { nombre: "Portugal", abbr: "PT", prefijo: "+351", min: 9, max: 9, bandera: "https://flagcdn.com/pt.svg" },
  { nombre: "Reino Unido", abbr: "GB", prefijo: "+44", min: 9, max: 10, bandera: "https://flagcdn.com/gb.svg" },
  { nombre: "República Centroafricana", abbr: "CF", prefijo: "+236", min: 8, max: 8, bandera: "https://flagcdn.com/cf.svg" },
  { nombre: "República Checa", abbr: "CZ", prefijo: "+420", min: 9, max: 9, bandera: "https://flagcdn.com/cz.svg" },
  { nombre: "República del Congo", abbr: "CD", prefijo: "+243", min: 7, max: 9, bandera: "https://flagcdn.com/cd.svg" },
  { nombre: "República Dominicana", abbr: "DO", prefijo: "+1-809", min: 7, max: 7, bandera: "https://flagcdn.com/do.svg" },
  { nombre: "Ruanda", abbr: "RW", prefijo: "+250", min: 8, max: 8, bandera: "https://flagcdn.com/rw.svg" },
  { nombre: "Rumania", abbr: "RO", prefijo: "+40", min: 9, max: 9, bandera: "https://flagcdn.com/ro.svg" },
  { nombre: "Rusia", abbr: "RU", prefijo: "+7", min: 10, max: 10, bandera: "https://flagcdn.com/ru.svg" },
  { nombre: "Samoa", abbr: "WS", prefijo: "+685", min: 5, max: 7, bandera: "https://flagcdn.com/ws.svg" },
  { nombre: "San Cristóbal y Nieves", abbr: "KN", prefijo: "+1-869", min: 7, max: 7, bandera: "https://flagcdn.com/kn.svg" },
  { nombre: "San Marino", abbr: "SM", prefijo: "+378", min: 6, max: 10, bandera: "https://flagcdn.com/sm.svg" },
  { nombre: "San Vicente y las Granadinas", abbr: "VC", prefijo: "+1-784", min: 7, max: 7, bandera: "https://flagcdn.com/vc.svg" },
  { nombre: "Santa Lucía", abbr: "LC", prefijo: "+1-758", min: 7, max: 7, bandera: "https://flagcdn.com/lc.svg" },
  { nombre: "Santo Tomé y Príncipe", abbr: "ST", prefijo: "+239", min: 7, max: 7, bandera: "https://flagcdn.com/st.svg" },
  { nombre: "Senegal", abbr: "SN", prefijo: "+221", min: 9, max: 9, bandera: "https://flagcdn.com/sn.svg" },
  { nombre: "Serbia", abbr: "RS", prefijo: "+381", min: 8, max: 9, bandera: "https://flagcdn.com/rs.svg" },
  { nombre: "Seychelles", abbr: "SC", prefijo: "+248", min: 7, max: 7, bandera: "https://flagcdn.com/sc.svg" },
  { nombre: "Sierra Leona", abbr: "SL", prefijo: "+232", min: 8, max: 8, bandera: "https://flagcdn.com/sl.svg" },
  { nombre: "Singapur", abbr: "SG", prefijo: "+65", min: 8, max: 8, bandera: "https://flagcdn.com/sg.svg" },
  { nombre: "Siria", abbr: "SY", prefijo: "+963", min: 7, max: 10, bandera: "https://flagcdn.com/sy.svg" },
  { nombre: "Somalia", abbr: "SO", prefijo: "+252", min: 7, max: 9, bandera: "https://flagcdn.com/so.svg" },
  { nombre: "Sri Lanka", abbr: "LK", prefijo: "+94", min: 9, max: 10, bandera: "https://flagcdn.com/lk.svg" },
  { nombre: "Sudáfrica", abbr: "ZA", prefijo: "+27", min: 9, max: 9, bandera: "https://flagcdn.com/za.svg" },
  { nombre: "Sudán", abbr: "SD", prefijo: "+249", min: 9, max: 9, bandera: "https://flagcdn.com/sd.svg" },
  { nombre: "Sudán del Sur", abbr: "SS", prefijo: "+211", min: 7, max: 9, bandera: "https://flagcdn.com/ss.svg" },
  { nombre: "Suecia", abbr: "SE", prefijo: "+46", min: 6, max: 13, bandera: "https://flagcdn.com/se.svg" },
  { nombre: "Suiza", abbr: "CH", prefijo: "+41", min: 9, max: 9, bandera: "https://flagcdn.com/ch.svg" },
  { nombre: "Surinam", abbr: "SR", prefijo: "+597", min: 6, max: 7, bandera: "https://flagcdn.com/sr.svg" },
  { nombre: "Tailandia", abbr: "TH", prefijo: "+66", min: 8, max: 9, bandera: "https://flagcdn.com/th.svg" },
  { nombre: "Tanzania", abbr: "TZ", prefijo: "+255", min: 9, max: 9, bandera: "https://flagcdn.com/tz.svg" },
  { nombre: "Tayikistán", abbr: "TJ", prefijo: "+992", min: 9, max: 9, bandera: "https://flagcdn.com/tj.svg" },
  { nombre: "Timor Oriental", abbr: "TL", prefijo: "+670", min: 7, max: 8, bandera: "https://flagcdn.com/tl.svg" },
  { nombre: "Togo", abbr: "TG", prefijo: "+228", min: 8, max: 8, bandera: "https://flagcdn.com/tg.svg" },
  { nombre: "Tonga", abbr: "TO", prefijo: "+676", min: 5, max: 7, bandera: "https://flagcdn.com/to.svg" },
  { nombre: "Trinidad y Tobago", abbr: "TT", prefijo: "+1-868", min: 7, max: 7, bandera: "https://flagcdn.com/tt.svg" },
  { nombre: "Túnez", abbr: "TN", prefijo: "+216", min: 8, max: 8, bandera: "https://flagcdn.com/tn.svg" },
  { nombre: "Turkmenistán", abbr: "TM", prefijo: "+993", min: 8, max: 8, bandera: "https://flagcdn.com/tm.svg" },
  { nombre: "Turquía", abbr: "TR", prefijo: "+90", min: 10, max: 10, bandera: "https://flagcdn.com/tr.svg" },
  { nombre: "Tuvalu", abbr: "TV", prefijo: "+688", min: 5, max: 5, bandera: "https://flagcdn.com/tv.svg" },
  { nombre: "Ucrania", abbr: "UA", prefijo: "+380", min: 9, max: 9, bandera: "https://flagcdn.com/ua.svg" },
  { nombre: "Uganda", abbr: "UG", prefijo: "+256", min: 9, max: 9, bandera: "https://flagcdn.com/ug.svg" },
  { nombre: "Uruguay", abbr: "UY", prefijo: "+598", min: 8, max: 9, bandera: "https://flagcdn.com/uy.svg" },
  { nombre: "Uzbekistán", abbr: "UZ", prefijo: "+998", min: 9, max: 9, bandera: "https://flagcdn.com/uz.svg" },
  { nombre: "Vanuatu", abbr: "VU", prefijo: "+678", min: 5, max: 7, bandera: "https://flagcdn.com/vu.svg" },
  { nombre: "Vaticano", abbr: "VA", prefijo: "+379", min: 8, max: 10, bandera: "https://flagcdn.com/va.svg" },
  { nombre: "Venezuela", abbr: "VE", prefijo: "+58", min: 10, max: 10, bandera: "https://flagcdn.com/ve.svg" },
  { nombre: "Vietnam", abbr: "VN", prefijo: "+84", min: 8, max: 10, bandera: "https://flagcdn.com/vn.svg" },
  { nombre: "Yemen", abbr: "YE", prefijo: "+967", min: 7, max: 9, bandera: "https://flagcdn.com/ye.svg" },
  { nombre: "Yibuti", abbr: "DJ", prefijo: "+253", min: 8, max: 8, bandera: "https://flagcdn.com/dj.svg" },
  { nombre: "Zambia", abbr: "ZM", prefijo: "+260", min: 9, max: 9, bandera: "https://flagcdn.com/zm.svg" },
  { nombre: "Zimbabue", abbr: "ZW", prefijo: "+263", min: 7, max: 9, bandera: "https://flagcdn.com/zw.svg" }
];

// Inicia con Venezuela por defecto
let currentPrefijo = prefijos.find(c => c.prefijo === '+58') || prefijos[0];

function initializePhoneSelector() {
    const prefijoSelector = document.getElementById('prefijoSelector');
    const prefijoBandera = document.getElementById('prefijoBandera');
    const prefijoTexto = document.getElementById('prefijoTexto');
    const prefijoDropdown = document.getElementById('prefijoDropdown');
    const prefijoBuscar = document.getElementById('prefijoBuscar');
    const prefijoLista = document.getElementById('prefijoLista');
    const telefonoNumero = document.getElementById('telefonoNumero');
    const telefonoFinal = document.getElementById('telefonoFinal');

    // Salir si los elementos no están en el DOM (cuando el formulario está oculto)
    if (!prefijoSelector) return;

    // 1. Inicializar la visualización del prefijo
    function updatePrefijoDisplay() {
        if (prefijoBandera) prefijoBandera.src = currentPrefijo.bandera;
        if (prefijoBandera) prefijoBandera.alt = currentPrefijo.nombre;
        if (prefijoTexto) prefijoTexto.textContent = currentPrefijo.prefijo;
        // 🚨 CLAVE: Actualizar el campo oculto con el prefijo y el número
        if (telefonoFinal) telefonoFinal.value = currentPrefijo.prefijo + (telefonoNumero ? telefonoNumero.value.replace(/\s/g, '') : '');
    }

    // 2. Lógica para abrir/cerrar el dropdown
    prefijoSelector.onclick = () => {
        const isExpanded = prefijoSelector.getAttribute('aria-expanded') === 'true';
        // Solo si el dropdown está en el DOM
        if (prefijoDropdown) {
            prefijoDropdown.style.display = isExpanded ? 'none' : 'block';
        }
        prefijoSelector.setAttribute('aria-expanded', !isExpanded);
        if (!isExpanded) {
            renderCountryList('');
            if (prefijoBuscar) prefijoBuscar.focus();
        }
    };

    // 3. Renderizar la lista de países (con filtro)
    function renderCountryList(filter) {
        if (!prefijoLista) return;
        prefijoLista.innerHTML = '';
        const lowerFilter = filter.toLowerCase();

        prefijos
            .filter(country => 
                country.nombre.toLowerCase().includes(lowerFilter) || 
                country.prefijo.includes(lowerFilter)
            )
            .forEach(country => {
                const item = document.createElement('div');
                item.className = 'prefijo-item';
                item.setAttribute('role', 'option');
                item.setAttribute('aria-selected', country.prefijo === currentPrefijo.prefijo);
                item.innerHTML = `
                    <img src="https://flagcdn.com/${country.bandera.split('/').pop()}" class="prefijo-flag" alt="${country.nombre}">
                    <span class="name">${country.nombre}</span>
                    <span class="code">${country.prefijo}</span>
                `;
                item.addEventListener('click', () => {
                    currentPrefijo = country;
                    updatePrefijoDisplay(); 
                    if (prefijoDropdown) prefijoDropdown.style.display = 'none';
                    prefijoSelector.setAttribute('aria-expanded', 'false');
                    if (telefonoNumero) telefonoNumero.focus();
                });
                prefijoLista.appendChild(item);
            });
    }

    // 4. Filtrar la lista al escribir
    if (prefijoBuscar) {
        prefijoBuscar.addEventListener('input', (e) => {
            renderCountryList(e.target.value);
        });
    }

    // 5. Unir el prefijo y el número en el campo oculto al escribir
    if (telefonoNumero) {
        telefonoNumero.addEventListener('input', updatePrefijoDisplay);
    }
    
    // 6. Cerrar el dropdown al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (prefijoDropdown && !prefijoSelector.contains(e.target) && !prefijoDropdown.contains(e.target) && modal.contains(e.target)) {
            prefijoDropdown.style.display = 'none';
            prefijoSelector.setAttribute('aria-expanded', 'false');
        }
    });

    // Inicialización al cargar el formulario
    updatePrefijoDisplay();
}
});

/* ===============================
7.🔥 CONTADOR DE IMPACTO (Real 0→4M)
================================== */

function animateCounter(element, start, end, duration, type = "normal") {
    let startTime = null;

    function update(currentTime) {
        if (!startTime) startTime = currentTime;

        const progress = Math.min((currentTime - startTime) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);

        // TIPOS:
        // normal = número normal con +
        // million = número grande que termina en +XM
        if (type === "million") {
            element.textContent = "+" + value.toLocaleString() + "";
            if (progress === 1) {
                element.textContent = "+4M";
            }
        } else {
            element.textContent = "+" + value.toLocaleString();
        }

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

let countersActivated = false;

function startCounters() {
    const section = document.querySelector(".impact-section");
    // Si la sección no existe (ej. no está en la página actual), salir.
    if (!section) return; 

    const position = section.getBoundingClientRect().top;

    // Activar al bajar (cuando la parte superior de la sección está en la vista)
    if (position < window.innerHeight - 100 && !countersActivated) {
        countersActivated = true;

        const countries = document.getElementById("counter-countries");
        const churches  = document.getElementById("counter-churches");
        const souls     = document.getElementById("counter-souls");

        animateCounter(countries, 0, 42, 4000, "normal");
        animateCounter(churches, 0, 1250, 4000, "normal");
        animateCounter(souls, 0, 4000000, 4000, "million"); // ← Corre desde 0 hasta 4,000,000 real
    }

    // Reiniciar al subir (cuando la sección sale por completo de la vista por arriba)
    if (position > window.innerHeight) {
        countersActivated = false;

        // Asegurarse de que los elementos existan antes de asignar textContent
        const countries = document.getElementById("counter-countries");
        const churches  = document.getElementById("counter-churches");
        const souls     = document.getElementById("counter-souls");

        if (countries) countries.textContent = "+0";
        if (churches) churches.textContent  = "+0";
        if (souls) souls.textContent     = "+0";
    }
}

window.addEventListener("scroll", startCounters);

/* =============================== */
/* 8. ANIMACIONES LÍDERES          */
/* =============================== */

    const leadersCards = document.querySelectorAll(
        '.leader-card-left, .leader-card-right'
    );

    const leadersObserverOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.2 // Se activa cuando el 20% del elemento es visible
    };

    const leadersObserverCallback = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                // Si solo quieres que se anime una vez, descomenta la siguiente línea:
                // observer.unobserve(entry.target); 
            } else {
                // Permite la repetición de la animación al salir de la vista
                entry.target.classList.remove('is-visible');
            }
        });
    };

    const leadersObserver = new IntersectionObserver(leadersObserverCallback, leadersObserverOptions);

    // Observar cada tarjeta
    leadersCards.forEach(card => leadersObserver.observe(card));

/* ======================================= */
/* 9. ANIMACIONES SECCIÓN DONACIÓN (ANIMACIÓN REPETITIVA) */
/* ======================================= */

// Selecciona todos los elementos con la clase scroll-animate en la sección de donación
const donationElements = document.querySelectorAll('.donation-section .scroll-animate');

// Crea un nuevo observador
const donationObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        // Si el elemento es visible (está intersectando con el viewport)
        if (entry.isIntersecting) {
            // 1. EL ELEMENTO ENTRA: Añadimos la clase para animar.
            entry.target.classList.add('is-visible');
            
            // IMPORTANTE: Eliminamos 'donationObserver.unobserve(entry.target);'
            // para que la animación se pueda repetir al volver a entrar.
            
        } else {
            // 2. EL ELEMENTO SALE: Quitamos la clase para que vuelva a su estado inicial invisible.
            // Esto permite que la animación se active de nuevo la próxima vez que entre.
            entry.target.classList.remove('is-visible');
        }
    });
}, {
    // La animación se activa cuando el 10% del elemento entra en la vista
    threshold: 0.1
});

// Observar todos los elementos seleccionados
donationElements.forEach(element => {
    donationObserver.observe(element);
});

// ⭐ FIX CRÍTICO PARA CARGA INICIAL (Retraso Seguro): 
// Asegura que los elementos visibles en la carga inicial se muestren inmediatamente.
window.addEventListener('load', () => {
    setTimeout(() => {
        donationElements.forEach(element => {
            const rect = element.getBoundingClientRect();
            // Si la parte superior del elemento es visible en la ventana Y aún no está visible
            if (rect.top < window.innerHeight && !element.classList.contains('is-visible')) {
                element.classList.add('is-visible');
            }
        });
    }, 300); // 300ms de retraso para evitar conflictos de timing con el scroll-to-top.
});

/* ==================================== */
/* 10. FUNCIONALIDAD DE ZOOM DE CUENTAS */
/* ==================================== */

document.addEventListener('DOMContentLoaded', () => {

    // 🔥 CAMBIO IMPORTANTE:
    // Antes: const buttons = document.querySelectorAll('.account-button');
    // Ahora: todas las imágenes que tengan data-image abrirán el zoom
    const buttons = document.querySelectorAll('[data-image]');

    const fullscreenOverlay = document.getElementById('fullscreenOverlay'); 
    const fullscreenImage = document.getElementById('fullscreenImage');
    const closeOverlay = document.getElementById('closeOverlay');

    if (buttons.length > 0 && fullscreenOverlay && fullscreenImage && closeOverlay) {
        
        // ABRIR ZOOM
        buttons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const imageUrl = button.getAttribute('data-image');

                if (imageUrl) {
                    fullscreenImage.src = imageUrl;
                    fullscreenOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        // CERRAR ZOOM – botón X
        closeOverlay.addEventListener('click', () => {
            fullscreenOverlay.classList.remove('active');
            fullscreenImage.src = '';
            document.body.style.overflow = '';
        });

        // Cerrar al hacer click afuera
        fullscreenOverlay.addEventListener('click', (e) => {
            if (e.target === fullscreenOverlay) {
                fullscreenOverlay.classList.remove('active');
                fullscreenImage.src = '';
                document.body.style.overflow = '';
            }
        });

        // Cerrar con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && fullscreenOverlay.classList.contains('active')) {
                fullscreenOverlay.classList.remove('active');
                fullscreenImage.src = '';
                document.body.style.overflow = '';
            }
        });
    }
});

/* ==================================== */
/* 11. ANIMACIONES AL HACER SCROLL (Activación permanente) */
/* ==================================== */

    // Configuración del Intersection Observer para la animación al hacer scroll
    const options = {
        root: null, // El viewport es el elemento raíz
        rootMargin: '0px',
        threshold: 0.2 // El elemento se activa cuando el 20% es visible
    };

    // Lista de todos los elementos que deben animarse, incluyendo los nuevos horarios.
    const targets = document.querySelectorAll(
        // Selectores de animaciones existentes (Ajusta si tienes más)
        '.reveal, ' +
        '.hero-content .fade-in-up, ' +
        '.leader-card-left, ' +
        '.leader-card-right, ' +
        
        // NUEVOS SELECTORES PARA LOS HORARIOS
        '.horario-item.reveal-left, ' +
        '.horario-item.reveal-bottom, ' +
        '.horario-item.reveal-right'
    );

    // Crea el observador
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Si el elemento es visible, añade la clase CSS 'is-visible'
                entry.target.classList.add('is-visible');
                // Deja de observar el elemento una vez que ha sido visible para no repetir la animación
                observer.unobserve(entry.target);
            }
        });
    }, options);

    // Observa todos los elementos definidos en 'targets'
    targets.forEach(target => {
        observer.observe(target);
    });

/* ==================================== */
/* 12. EVENTOS – CLICK PARA VOLTEAR Y QUEDARSE */
/* ==================================== */
document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".evento-card");
    let activeCard = null;

    cards.forEach(card => {
        card.addEventListener("click", () => {

            // si ya está volteada → regresarla
            if (card.classList.contains("flip")) {
                card.classList.remove("flip");
                if (activeCard === card) activeCard = null;
                return;
            }

            // si otra está volteada → cerrarla
            if (activeCard && activeCard !== card) {
                activeCard.classList.remove("flip");
            }

            // voltear esta
            card.classList.add("flip");
            activeCard = card;
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const animatedItems = document.querySelectorAll(
        ".anim-always-up, .anim-from-top-left, .anim-from-bottom, .anim-from-top-right"
    );

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("active");
                }
            });
        },
        { threshold: 0.2 }
    );

    animatedItems.forEach((item) => observer.observe(item));
});

/* =================================================
   GALERÍA — JS GLOBAL
   (Tabs, filtrado, lazy, ripple, fullscreen, videos autoplay, keyboard nav)
   ================================================= */

(function () {
  'use strict';

  const q = sel => document.querySelector(sel);
  const qa = sel => Array.from(document.querySelectorAll(sel));

  /* ---------------------------
     THEME toggle (FontAwesome icons)
  ----------------------------*/
  const themeToggle = q('#themeToggle');
  const themeIcon = themeToggle ? themeToggle.querySelector('.theme-icon') : null;

  function setTheme(mode) {
    if (mode === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
    else document.documentElement.removeAttribute('data-theme');

    if (themeIcon) {
      themeIcon.classList.remove('fa-moon','fa-sun');
      themeIcon.classList.add(mode === 'dark' ? 'fa-sun' : 'fa-moon');
      themeIcon.classList.add('animate');
      setTimeout(()=> themeIcon.classList.remove('animate'), 420);
    }
    localStorage.setItem('maranatha_theme', mode);
  }
  // load saved
  const savedTheme = localStorage.getItem('maranatha_theme') || 'light';
  setTheme(savedTheme);

  if (themeToggle) themeToggle.addEventListener('click', () => {
    const cur = localStorage.getItem('maranatha_theme') || 'light';
    setTheme(cur === 'dark' ? 'light' : 'dark');
  });

  /* ---------------------------
     TABS & FILTER
  ----------------------------*/
  const tabs = qa('.gallery-tabs .tab');
  const items = qa('.gallery-item');

  function filterBy(filter) {
    items.forEach(it => {
      const cat = it.dataset.category;
      if (filter === 'all' || cat === filter) {
        it.classList.remove('hide-item'); it.classList.add('show-item');
      } else {
        it.classList.remove('show-item'); it.classList.add('hide-item');
      }
    });
  }

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t=>t.classList.remove('active'));
      tab.classList.add('active');

      // bounce effect
      tab.classList.add('pressed');
      setTimeout(()=> tab.classList.remove('pressed'), 260);

      filterBy(tab.dataset.filter);
    });
  });

  // initial: show all
  filterBy('all');

  /* ---------------------------
     LAZY LOAD IMAGES
  ----------------------------*/
  qa('.gallery-item img').forEach(img=>{
    if (img.complete) img.classList.add('loaded');
    else img.addEventListener('load', ()=> img.classList.add('loaded'));
  });

  /* ---------------------------
     RIPPLE ON CLICK
  ----------------------------*/
  qa('.media-button').forEach(btn=>{
    btn.addEventListener('click', (e)=>{
      // create ripple
      const rect = btn.getBoundingClientRect();
      const r = document.createElement('span');
      r.className = 'ripple';
      r.style.left = (e.clientX - rect.left) + 'px';
      r.style.top = (e.clientY - rect.top) + 'px';
      btn.appendChild(r);
      setTimeout(()=> r.remove(), 700);
    });
  });

  /* ---------------------------
     FULLSCREEN (image/video) overlay
  ----------------------------*/
  const overlay = q('#fullscreenOverlay') || (function(){
    // if not on page, create minimal overlay
    const ov = document.createElement('div'); ov.id='fullscreenOverlay'; ov.className='fullscreen-overlay';
    ov.innerHTML = '<button id="closeOverlay" class="close-button" aria-label="Cerrar">×</button><img id="fullscreenImage" style="display:none;"><video id="fullscreenVideo" controls style="display:none;"></video>';
    document.body.appendChild(ov);
    return ov;
  })();

  const overlayImg = q('#fullscreenImage');
  const overlayVideo = q('#fullscreenVideo');
  const closeBtn = q('#closeOverlay');

  // Build a navigation array of media nodes (media-button elements)
  const mediaButtons = qa('.media-button');
  const mediaList = mediaButtons.map(btn => {
    return {
      el: btn,
      type: btn.dataset.type || (btn.dataset.video ? 'video' : 'image'),
      src: btn.dataset.type === 'video' ? btn.dataset.video || btn.dataset.src : btn.dataset.image || btn.dataset.src
    };
  });

  let currentIndex = 0;

  function openFullscreen(idx) {
    const item = mediaList[idx];
    if (!item) return;
    currentIndex = idx;
    if (item.type === 'video' || (item.src && (item.src.endsWith('.mp4') || item.src.endsWith('.MOV') || item.src.endsWith('.mov')))) {
      overlayImg.style.display='none';
      overlayVideo.style.display='block';
      // set src on video
      overlayVideo.src = item.src;
      overlayVideo.play().catch(()=>{});
    } else {
      // image
      overlayVideo.pause(); overlayVideo.src = '';
      overlayVideo.style.display='none';
      overlayImg.style.display='block';
      overlayImg.src = item.src;
    }
    overlay.classList.add('active');
    document.body.style.overflow='hidden';
  }

  // hook mediaButtons to open overlay and set current index
  mediaButtons.forEach((btn, i) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      currentIndex = mediaButtons.indexOf(btn);
      // sync mediaList srcs in case dataset uses different key names
      const dsImage = btn.dataset.image || btn.getAttribute('data-image');
      const dsVideo = btn.dataset.video || btn.getAttribute('data-video');
      if (dsImage) mediaList[currentIndex].src = dsImage;
      if (dsVideo) mediaList[currentIndex].src = dsVideo;
      openFullscreen(currentIndex);
    });
  });

  function closeFullscreen() {
    overlay.classList.remove('active');
    overlayImg.src = '';
    overlayImg.style.display='none';
    overlayVideo.pause();
    overlayVideo.src = '';
    overlayVideo.style.display='none';
    document.body.style.overflow='';
  }

  if (closeBtn) closeBtn.addEventListener('click', closeFullscreen);
  overlay.addEventListener('click', (e)=> { if (e.target === overlay) closeFullscreen(); });

  // keyboard nav inside overlay
  document.addEventListener('keydown', (e)=> {
    if (!overlay.classList.contains('active')) return;
    if (e.key === 'Escape') closeFullscreen();
    if (e.key === 'ArrowRight') { currentIndex = (currentIndex+1) % mediaList.length; openFullscreen(currentIndex); }
    if (e.key === 'ArrowLeft')  { currentIndex = (currentIndex-1+mediaList.length) % mediaList.length; openFullscreen(currentIndex); }
  });

  /* ---------------------------
    AUTOPLAY/Pausar videos by viewport (IntersectionObserver)
----------------------------*/
const vids = qa('.gallery-item video');
if ('IntersectionObserver' in window && vids.length) {
  const vObs = new IntersectionObserver((entries)=>{
    entries.forEach(en=>{
      const v = en.target;
      // Asegúrate de que el video esté silenciado para el autoplay inicial
      if (!v.muted) v.muted = true; // <--- AÑADE ESTA LÍNEA
      if (en.isIntersecting && en.intersectionRatio > 0.45) {
        v.play().catch(()=>{
          console.warn("Autoplay de video falló. Asegúrate de que está silenciado o el usuario ha interactuado.", v);
        });
        v.classList.add('playing');
      } else {
        v.pause();
        v.classList.remove('playing');
      }
    });
  }, { threshold:[0.45] });

  vids.forEach(v=>{
      v.pause();
      v.muted = true; // <--- AÑADE ESTA LÍNEA para silenciarlos al iniciar
      vObs.observe(v);
  });
} else {
  vids.forEach(v=> v.muted = true);
}
  /* ---------------------------
     Apply animation classes based on data-anim
  ----------------------------*/
  qa('.gallery-item').forEach(it => {
    const anim = it.dataset.anim || it.getAttribute('data-anim');
    if (!anim) return;
    // map to class
    const cls = 'anim-' + anim;
    it.classList.add(cls);
  });

  /* ---------------------------
     Small stagger reveal on load
  ----------------------------*/
  window.addEventListener('load', ()=>{
    const visibleItems = qa('.gallery-item').filter(i => !i.classList.contains('hide-item'));
    visibleItems.forEach((el, idx) => setTimeout(()=> el.classList.add('show-item'), idx * 45));
  });
})();

/* ==================================================
     GRID-SPAN CALCULATOR - make CSS Grid behave like masonry
     Calculates row span so items with variable heights don't overlap
     Reads `grid-auto-rows` and `row-gap` from CSS to avoid hardcoded values
     ================================================== */

function _parsePx(val) {
    if (!val) return NaN;
    return parseFloat(val);
}

function _debounce(fn, wait) {
    let t;
    return function(...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), wait);
    };
}

function resizeGalleryGrid() {
    const grids = document.querySelectorAll('.gallery-section-block, .gallery-grid, .gallery-section');
    if (!grids.length) return;

    grids.forEach(g => {
        const style = window.getComputedStyle(g);
        const cssRow = style.getPropertyValue('grid-auto-rows') || style.getPropertyValue('gridAutoRows');
        const cssRowGap = style.getPropertyValue('row-gap') || style.getPropertyValue('grid-row-gap') || style.getPropertyValue('gap');

        const rowHeight = _parsePx(cssRow) || 120; // fallback to 120
        const rowGap = _parsePx(cssRowGap) || 18;   // fallback to 18

        const items = Array.from(g.querySelectorAll('.gallery-item'));

        items.forEach(item => item.style.gridRowEnd = null);

        items.forEach(item => {
            const h = item.getBoundingClientRect().height;
            if (!h || isNaN(h) || h <= 0) {
                // nothing to measure yet
                return;
            }
            const span = Math.ceil((h + rowGap) / (rowHeight + rowGap));
            item.style.gridRowEnd = 'span ' + Math.max(span, 1);
        });

        // Debug visual toggle: ?debug_grid=1
        try {
            const params = new URLSearchParams(window.location.search);
            if (params.get('debug_grid') === '1') g.classList.add('gallery-debug-outline');
        } catch (e) { /* ignore */ }
    });
}

function watchMediaAndResize() {
    const media = document.querySelectorAll('.gallery-item img, .gallery-item video');
    if (!media.length) {
        resizeGalleryGrid();
        return;
    }

    media.forEach(m => {
        const tag = (m.tagName || '').toLowerCase();
        if (tag === 'img') {
            if (m.complete) resizeGalleryGrid();
            else m.addEventListener('load', resizeGalleryGrid, { once: true });
        } else if (tag === 'video') {
            if (m.readyState >= 2) resizeGalleryGrid();
            m.addEventListener('loadedmetadata', resizeGalleryGrid, { once: true });
            m.addEventListener('loadeddata', resizeGalleryGrid, { once: true });
        }
    });

    // Fallback in case some medias are cached but events didn't fire
    setTimeout(resizeGalleryGrid, 600);
}

// Observe mutations inside the gallery to recalc when items change
function observeGalleryMutations() {
    const grids = document.querySelectorAll('.gallery-section-block, .gallery-grid, .gallery-section');
    grids.forEach(g => {
        if (g._galleryObserver) return;
        const mo = new MutationObserver(_debounce(() => { resizeGalleryGrid(); watchMediaAndResize(); }, 120));
        mo.observe(g, { childList: true, subtree: true, attributes: true, attributeFilter: ['class', 'style', 'src'] });
        g._galleryObserver = mo;
    });
}

const _debouncedResize = _debounce(() => resizeGalleryGrid(), 120);
window.addEventListener('load', () => {
    resizeGalleryGrid();
    watchMediaAndResize();
    observeGalleryMutations();
});
window.addEventListener('resize', _debouncedResize);


/* ============================
   RADIO ONLINE — PLAYER
============================ */

const radioAudio = document.getElementById("radioAudio");
const radioPlayBtn = document.getElementById("radioPlayBtn");
const radioStatus = document.getElementById("radioStatus");

if (radioPlayBtn) {
    radioPlayBtn.addEventListener("click", () => {
        if (radioAudio.paused) {

            radioAudio.play();
            radioPlayBtn.innerHTML = '<i class="fas fa-pause"></i>';
            radioStatus.textContent = "Reproduciendo...";

        } else {

            radioAudio.pause();
            radioPlayBtn.innerHTML = '<i class="fas fa-play"></i>';
            radioStatus.textContent = "Detenido";

        }
    });
}
/* ===========================================
RADIO — PLAYER FLOTANTE GLOBAL
=========================================== */

const radioStream = document.getElementById("globalRadioStream");
const floatBtn = document.getElementById("floatingPlayBtn");

if (floatBtn) {
    floatBtn.addEventListener("click", () => {
        if (radioStream.paused) {

            radioStream.play();
            floatBtn.innerHTML = '<i class="fas fa-pause"></i>';

        } else {

            radioStream.pause();
            floatBtn.innerHTML = '<i class="fas fa-play"></i>';

        }
    });
}
