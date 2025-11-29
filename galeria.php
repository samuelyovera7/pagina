<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería - Maranatha Aragua</title>
    <link rel="shortcut icon" href="img/logo pequeño.jpeg"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="main-header">
        <div class="container header-content">
            <a href="index.php" class="logo">
                <img src="img/Logo Maranatha Aragua.png" alt="Logo Maranatha Aragua" class="logo-img">
            </a>
            <nav class="main-nav">
                <a href="nuestro ministerio.php" class="nav-link">Nuestro Ministerio</a>
                <a href="evangelismo.php" class="nav-link">EEC</a>
                <a href="eventos.php" class="nav-link">Eventos</a>
                <a href="radiomaranatharagua.php" class="nav-link">Radionline</a>
                <a href="#" class="nav-link cta-outline" id="contactanosBtn">CONTÁCTANOS</a> 
            </nav>
            <button class="menu-toggle" aria-label="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <div id="contact-modal" class="custom-modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h3 class="modal-title">Elige una opción para continuar</h3>      
        <div class="options-container selector-panel">
            <button class="btn-option" id="btnNuevos">Nuevos Usuarios<br><span>Quiero Ser Contactado</span></button>
            <button class="btn-option" id="btnMiembros">Miembros Activos<br><span>Asisto a la Iglesia</span></button>
        </div>

        <div id="formNuevos" class="modal-form-selection d-none">
            <h4 class="formulario-titulo">Formulario para Nuevos Usuarios</h4>
            <form id="formNuevosForm" action="procesar.php" method="post">
                <input type="hidden" name="tipo_formulario" value="contactos">
                <div class="form-group">
                    <label for="nombreApellido" class="form-label">Nombre y Apellido:</label>
                    <input type="text" id="nombreApellido" name="nombreApellido" class="form-control" placeholder="Juan Pérez" required>
                </div>
                <div class="form-group">
                    <label for="edad" class="form-label">Edad:</label>
                    <input type="number" id="edad" name="edad" class="form-control" min="1" max="110" placeholder="30" required>
                </div>
                <div class="form-group">
                    <label for="direccion" class="form-label">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" placeholder="calle, comunidad, ciudad, estado, pais" required>
                </div>

                <div class="form-group phone-wrapper">
                    <label for="telefonoNumero" class="form-label">Teléfono:</label>
                    <div class="phone-input-group">
                        <div id="prefijoSelector" class="prefijo-display" role="button" aria-haspopup="listbox" aria-expanded="false">
                            <img id="prefijoBandera" src="https://flagcdn.com/ve.svg" class="prefijo-flag" alt="Bandera">
                            <span id="prefijoTexto">+58</span>
                            <span class="arrow">▾</span>
                        </div>
                        <input type="tel" id="telefonoNumero" class="form-control" placeholder="Ej: 4121234567" required>
                    </div>
                    <input type="hidden" id="telefonoFinal" name="telefono">
                    <div id="prefijoDropdown" class="prefijo-dropdown" role="listbox" aria-label="Seleccionar país">
                        <input id="prefijoBuscar" class="prefijo-search" type="search" placeholder="Buscar país…" aria-label="Buscar país">
                        <div id="prefijoLista" class="prefijo-list" tabindex="0"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="correo" class="form-label">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" class="form-control" placeholder="jesus@gmail.com">
                </div>
                <div class="form-group">
                    <label for="peticionOracion" class="form-label">Petición de Oración:</label>
                    <select id="peticionOracion" name="peticionOracion" class="form-select" required>
                        <option value="">Selecciona una opción</option>
                        <option value="salud">Salud</option>
                        <option value="familia">Familia</option>
                        <option value="trabajo">Trabajo</option>
                        <option value="estudios">Estudios</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div id="peticionOracionOtro" class="form-group d-none">
                    <label for="peticionOracionOtroTexto" class="form-label">Especifica tu petición:</label>
                    <textarea id="peticionOracionOtroTexto" name="peticionOracionOtroTexto" class="form-control" placeholder="describe tu petición"></textarea>
                </div>
                <div class="form-group">
                    <label for="comoEnteraste" class="form-label">¿Cómo te enteraste?</label>
                    <select id="comoEnteraste" name="comoEnteraste" class="form-select" required>
                        <option value="">Selecciona una opción</option>
                        <option value="redes_sociales">Redes Sociales</option>
                        <option value="amigo">Amigo/Familiar</option>
                        <option value="evento">Evento/Evangelismo</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div id="otroCampo" class="form-group d-none">
                    <label for="comoEnterasteOtro" class="form-label">Comentanos:</label>
                    <input type="text" id="comoEnterasteOtro" name="comoEnterasteOtro" class="form-control" placeholder="¿donde te enteraste de nosotros?">
                </div>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </div>

        <div id="formMiembros" class="modal-form-selection d-none">
            <h4 class="formulario-titulo">Formulario para Miembros Activos</h4>
            <form id="formMiembrosForm" action="procesar.php" method="post">
                <input type="hidden" name="tipo_formulario" value="miembros">
                <input type="hidden" name="csrf_token">
                <div class="form-group">
                    <label for="nombreApellidoMiembro" class="form-label">Nombre y Apellido:</label>
                    <input type="text" id="nombreApellidoMiembro" name="nombreApellidoMiembro" class="form-control" placeholder="José Pinto" required>
                </div>
                <div class="form-group">
                    <label for="edadMiembro" class="form-label">Edad:</label>
                    <input type="number" id="edadMiembro" name="edadMiembro" class="form-control" min="1" max="120" placeholder="30" required>
                </div>
                <div class="form-group">
                    <label for="telefonoMiembro" class="form-label">Teléfono:</label>
                    <div class="input-group">
                        <span class="input-group-text">+58</span>
                        <input type="tel" id="telefonoMiembro" name="telefonoMiembro" 
                            class="form-control" placeholder="Ej: XXXXXXXXX" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="areaServicio" class="form-label">Área de Servicio:</label>
                    <select id="areaServicio" name="areaServicio" class="form-control" required>
                        <option value="" selected disabled>¿En cuál área sirves?</option>
                        <option value="Audiovisuales / Redes">Audiovisuales / Redes</option>
                        <option value="Sonido">Sonido</option>
                        <option value="Ujier">Ujier</option>
                        <option value="Protocolo">Protocolo</option>
                        <option value="Seguridad">Seguridad</option>
                        <option value="Maranatha Genesis">Maranatha Génesis</option>
                        <option value="Visitante Activo">Visitante Activo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="direccionMiembro" class="form-label">Dirección:</label>
                    <input type="text" id="direccionMiembro" name="direccionMiembro" class="form-control" placeholder="escribenos tu dirección completa" required>
                </div>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </div>
    </div>
    </div>


    <section id="galeria" class="gallery-section container" aria-label="Galería de Maranatha">

    <!-- TÍTULO DE LA SECCIÓN -->
    <div class="gallery-title">NUESTRA GALERÍA</div>
    
    <div class="gallery-subtitle">
        Podrás encontrar fotos y videos de nuestros servicios y actividades evangelísticas.
    </div>

    <div class="gallery-top-bar">

        <!-- BOTÓN DE TEMA -->
        <button id="themeToggle" class="theme-toggle-btn" aria-pressed="false">
            <i class="fas fa-moon theme-icon"></i>
        </button>

            <nav class="gallery-tabs" role="tablist" aria-label="Filtrar galería">
            <button type="button" class="tab active" data-filter="all" data-target="#galeria">Todos</button>

            <button type="button" class="tab" data-filter="inicios" data-target="#sec-inicios">Inicios</button>

            <button type="button" class="tab" data-filter="dominicales" data-target="#sec-dominicales">Servicios Dominicales</button>

            <button type="button" class="tab" data-filter="nocturnos" data-target="#sec-evangelismos">Evangelismos</button>

            <button type="button" class="tab" data-filter="gesto" data-target="#sec-gesto">Gesto de Amor</button>
        </nav>
    </section>


    <!-- ===========================
     SECCIÓN: INICIOS
=========================== -->

<!-- SUBTÍTULO + VERSÍCULO -->
<div id="sec-inicios" class="gallery-section-title">

    <h2 class="gallery-section-name">Inicios</h2>
    <p class="gallery-verse">
         “Si son fieles en las cosas pequeñas, serán fieles en las grandes;
        pero si son deshonestos en las cosas pequeñas, no actuarán con honradez en las responsabilidades más grandes.” LUCAS 16:10
    </p>
</div>

<!-- BLOQUE DE GALERÍA - INICIOS -->
<div class="gallery-section-block galeria-inicio" aria-label="Inicios">

    <!-- Foto 1 -->
<article class="gallery-item grid-small" data-category="inicios" data-anim="slideUp">
    <a class="media-button" data-type="image" data-image="img/inicios/inicios 1.JPG" href="#">
        <img src="img/inicios/inicios 1.JPG" alt="Inicios 1" loading="lazy">
    </a>
</article>

<!-- Foto 2 -->
<article class="gallery-item grid-small" data-category="inicios" data-anim="fadeIn">
    <a class="media-button" data-type="image" data-image="img/inicios/inicios 2.JPG" href="#">
        <img src="img/inicios/inicios 2.JPG" alt="Inicios 2" loading="lazy">
    </a>
</article>

<!-- Foto 3 -->
<article class="gallery-item grid-large" data-category="inicios" data-anim="slideLeft">
    <a class="media-button" data-type="image" data-image="img/inicios/inicios 3.JPG" href="#">
        <img src="img/inicios/inicios 3.JPG" alt="Inicios 3" loading="lazy">
    </a>
</article>

<!-- Foto 4 -->
<article class="gallery-item grid-small" data-category="inicios" data-anim="slideDown">
    <a class="media-button" data-type="image" data-image="img/inicios/inicios 4.JPG" href="#">
        <img src="img/inicios/inicios 4.JPG" alt="Inicios 4" loading="lazy">
    </a>
</article>

<!-- Foto 5 -->
<article class="gallery-item grid-small" data-category="inicios" data-anim="fadeIn">
    <a class="media-button" data-type="image" data-image="img/inicios/inicios 5.JPG" href="#">
        <img src="img/inicios/inicios 5.JPG" alt="Inicios 5" loading="lazy">
    </a>
</article>

<!-- Foto 6 -->
<article class="gallery-item grid-small" data-category="inicios" data-anim="slideRight">
    <a class="media-button" data-type="image" data-image="img/inicios/inicios 6.JPG" href="#">
        <img src="img/inicios/inicios 6.JPG" alt="Inicios 6" loading="lazy">
    </a>
</article>

<!-- Foto 7 -->
<article class="gallery-item grid-small" data-category="inicios" data-anim="scaleUp">
    <a class="media-button" data-type="image" data-image="img/inicios/inicios 7.JPG" href="#">
        <img src="img/inicios/inicios 7.JPG" alt="Inicios 7" loading="lazy">
    </a>
</article>
</div>

    <!-- SECCIÓN: SERVICIOS DOMINICALES -->
<div id="sec-dominicales" class="gallery-section-title">

    <div class="gallery-section-name">SERVICIOS DOMINICALES</div>
    <div class="gallery-verse">
        “Lucas 16:10 — Si son fieles en las cosas pequeñas,
        serán fieles en las grandes.”
    </div>
</div>

<div class="gallery-section-block">

    <!-- 1 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideUp">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 2.JPG">
            <img src="img/aniversario/imagen 2.JPG" loading="lazy">
        </a>
    </article>

    <!-- 2 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 3.JPG">
            <img src="img/aniversario/imagen 3.JPG" loading="lazy">
        </a>
    </article>

    <!-- 3 VIDEO -->
    <article class="gallery-item grid-large" data-category="dominicales" data-anim="scaleUp">
        <a class="media-button" data-type="video" data-video="img/aniversario/video 1.MOV">
            <video src="img/aniversario/video 1.MOV" muted autoplay loop></video>
        </a>
    </article>

    <!-- 4 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideLeft">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 4.JPG">
            <img src="img/aniversario/imagen 4.JPG" loading="lazy">
        </a>
    </article>

    <!-- 5 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideDown">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 5.JPG">
            <img src="img/aniversario/imagen 5.JPG" loading="lazy">
        </a>
    </article>

    <!-- 6 VIDEO -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideRight">
        <a class="media-button" data-type="video" data-video="img/aniversario/video 2.MOV">
            <video src="img/aniversario/video 2.MOV" muted autoplay loop></video>
        </a>
    </article>

    <!-- 7 IMG -->
    <article class="gallery-item grid-large" data-category="dominicales" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 6.JPG">
            <img src="img/aniversario/imagen 6.JPG" loading="lazy">
        </a>
    </article>

    <!-- 8 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="scaleUp">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 7.JPG">
            <img src="img/aniversario/imagen 7.JPG" loading="lazy">
        </a>
    </article>

    <!-- 9 VIDEO -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideUp">
        <a class="media-button" data-type="video" data-video="img/aniversario/video 3.MOV">
            <video src="img/aniversario/video 3.MOV" muted autoplay loop></video>
        </a>
    </article>

    <!-- 10 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideLeft">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 8.JPG">
            <img src="img/aniversario/imagen 8.JPG" loading="lazy">
        </a>
    </article>

    <!-- 11 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 9.JPG">
            <img src="img/aniversario/imagen 9.JPG" loading="lazy">
        </a>
    </article>

    <!-- 12 VIDEO -->
    <article class="gallery-item grid-large" data-category="dominicales" data-anim="scaleUp">
        <a class="media-button" data-type="video" data-video="img/aniversario/video 4.MOV">
            <video src="img/aniversario/video 4.MOV" muted autoplay loop></video>
        </a>
    </article>

    <!-- 13 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideRight">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 10.JPG">
            <img src="img/aniversario/imagen 10.JPG" loading="lazy">
        </a>
    </article>

    <!-- 14 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 11.JPG">
            <img src="img/aniversario/imagen 11.JPG" loading="lazy">
        </a>
    </article>

    <!-- 15 VIDEO -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideDown">
        <a class="media-button" data-type="video" data-video="img/aniversario/video 5.MOV">
            <video src="img/aniversario/video 5.MOV" muted autoplay loop></video>
        </a>
    </article>

    <!-- 16 IMG -->
    <article class="gallery-item grid-large" data-category="dominicales" data-anim="scaleUp">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 12.JPG">
            <img src="img/aniversario/imagen 12.JPG" loading="lazy">
        </a>
    </article>

    <!-- 17 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideLeft">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 13.JPG">
            <img src="img/aniversario/imagen 13.JPG" loading="lazy">
        </a>
    </article>

    <!-- 18 VIDEO -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="fadeIn">
        <a class="media-button" data-type="video" data-video="img/aniversario/video 6.MOV">
            <video src="img/aniversario/video 6.MOV" muted autoplay loop></video>
        </a>
    </article>

    <!-- 19 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideUp">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 14.JPG">
            <img src="img/aniversario/imagen 14.JPG" loading="lazy">
        </a>
    </article>

    <!-- 20 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideRight">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 15.JPG">
            <img src="img/aniversario/imagen 15.JPG" loading="lazy">
        </a>
    </article>

    <!-- 21 VIDEO -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="scaleUp">
        <a class="media-button" data-type="video" data-video="img/aniversario/video 7.MOV">
            <video src="img/aniversario/video 7.MOV" muted autoplay loop></video>
        </a>
    </article>

    <!-- 22 IMG -->
    <article class="gallery-item grid-large" data-category="dominicales" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 16.JPG">
            <img src="img/aniversario/imagen 16.JPG" loading="lazy">
        </a>
    </article>

    <!-- 23 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideLeft">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 17.JPG">
            <img src="img/aniversario/imagen 17.JPG" loading="lazy">
        </a>
    </article>

    <!-- 24 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideDown">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 18.JPG">
            <img src="img/aniversario/imagen 18.JPG" loading="lazy">
        </a>
    </article>

    <!-- 25 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideRight">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 19.JPG">
            <img src="img/aniversario/imagen 19.JPG" loading="lazy">
        </a>
    </article>

    <!-- 26 VIDEO -->
    <article class="gallery-item grid-large" data-category="dominicales" data-anim="scaleUp">
        <a class="media-button" data-type="video" data-video="img/aniversario/video 8.MOV">
            <video src="img/aniversario/video 8.MOV" muted autoplay loop></video>
        </a>
    </article>

    <!-- 27 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 20.JPG">
            <img src="img/aniversario/imagen 20.JPG" loading="lazy">
        </a>
    </article>

    <!-- 28 IMG -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideLeft">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 21.JPG">
            <img src="img/aniversario/imagen 21.JPG" loading="lazy">
        </a>
    </article>
    <!-- 29 IMG (21 de 24) -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="slideUp">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 23.JPG">
            <img src="img/aniversario/imagen 23.JPG" loading="lazy">
        </a>
    </article>

    <!-- 30 IMG (22 de 24) -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 24.JPG">
            <img src="img/aniversario/imagen 24.JPG" loading="lazy">
        </a>
    </article>

    <!-- 32 IMG (24 de 24 / sección completada) -->
    <article class="gallery-item grid-small" data-category="dominicales" data-anim="scaleUp">
        <a class="media-button" data-type="image" data-image="img/aniversario/imagen 25.JPG">
            <img src="img/aniversario/imagen 25.JPG" loading="lazy">
        </a>
    </article>

</div>

    <!-- SECCIÓN: EVANGELISMOS -->
<div id="sec-evangelismos" class="gallery-section-title">

    <div class="gallery-section-name">EVANGELISMOS</div>
    <div class="gallery-verse">
        “Lucas 16:10 — Si son fieles en las cosas pequeñas,
        serán fieles en las grandes.”
    </div>
</div>

<div class="gallery-section-block">

    <!-- 1 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="slideUp">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 1.jpg">
            <img src="img/evangelismos/imagen 1.jpg" loading="lazy">
        </a>
    </article>

    <!-- 2 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 2.jpg">
            <img src="img/evangelismos/imagen 2.jpg" loading="lazy">
        </a>
    </article>

    <!-- 3 IMG -->
    <article class="gallery-item grid-large" data-category="nocturnos" data-anim="slideLeft">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 3.jpg">
            <img src="img/evangelismos/imagen 3.jpg" loading="lazy">
        </a>
    </article>

    <!-- 4 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="slideRight">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 4.jpg">
            <img src="img/evangelismos/imagen 4.jpg" loading="lazy">
        </a>
    </article>

    <!-- 5 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="slideDown">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 5.jpg">
            <img src="img/evangelismos/imagen 5.jpg" loading="lazy">
        </a>
    </article>

    <!-- 6 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="scaleUp">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 6.jpg">
            <img src="img/evangelismos/imagen 6.jpg" loading="lazy">
        </a>
    </article>

    <!-- 7 IMG -->
    <article class="gallery-item grid-large" data-category="nocturnos" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 7.jpg">
            <img src="img/evangelismos/imagen 7.jpg" loading="lazy">
        </a>
    </article>

    <!-- 8 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="slideLeft">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 8.jpg">
            <img src="img/evangelismos/imagen 8.jpg" loading="lazy">
        </a>
    </article>

    <!-- 9 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 9.jpg">
            <img src="img/evangelismos/imagen 9.jpg" loading="lazy">
        </a>
    </article>

    <!-- 10 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="slideRight">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 10.jpg">
            <img src="img/evangelismos/imagen 10.jpg" loading="lazy">
        </a>
    </article>

    <!-- 11 IMG -->
    <article class="gallery-item grid-large" data-category="nocturnos" data-anim="scaleUp">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 11.jpg">
            <img src="img/evangelismos/imagen 11.jpg" loading="lazy">
        </a>
    </article>

    <!-- 12 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="slideDown">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 12.jpg">
            <img src="img/evangelismos/imagen 12.jpg" loading="lazy">
        </a>
    </article>

    <!-- 13 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="slideUp">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 13.jpg">
            <img src="img/evangelismos/imagen 13.jpg" loading="lazy">
        </a>
    </article>

    <!-- 14 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 14.jpg">
            <img src="img/evangelismos/imagen 14.jpg" loading="lazy">
        </a>
    </article>

    <!-- 15 IMG -->
    <article class="gallery-item grid-large" data-category="nocturnos" data-anim="slideLeft">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 15.jpg">
            <img src="img/evangelismos/imagen 15.jpg" loading="lazy">
        </a>
    </article>

    <!-- 16 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="slideRight">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 16.jpg">
            <img src="img/evangelismos/imagen 16.jpg" loading="lazy">
        </a>
    </article>

    <!-- 17 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="scaleUp">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 17.jpg">
            <img src="img/evangelismos/imagen 17.jpg" loading="lazy">
        </a>
    </article>

    <!-- 18 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="slideUp">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 18.jpg">
            <img src="img/evangelismos/imagen 18.jpg" loading="lazy">
        </a>
    </article>

    <!-- 19 IMG -->
    <article class="gallery-item grid-small" data-category="nocturnos" data-anim="fadeIn">
        <a class="media-button" data-type="image" data-image="img/evangelismos/imagen 19.jpg">
            <img src="img/evangelismos/imagen 19.jpg" loading="lazy">
        </a>
    </article>

    <!-- 20 VIDEO -->
    <article class="gallery-item grid-large" data-category="nocturnos" data-anim="scaleUp">
        <a class="media-button" data-type="video" data-video="videos/evangelismos/video.mp4">
            <video src="img/evangelismos/video.mp4" muted autoplay loop></video>
        </a>
    </article>

</div>
<!-- SECCIÓN: GESTO DE AMOR -->
<div id="sec-gesto" class="gallery-section-title">

    <div class="gallery-section-name">GESTO DE AMOR</div>
    <div class="gallery-verse">
        💙 Muy pronto estaremos mostrando fotos y videos de esta hermosa área de servicio.<br>
        Aún no hemos creado esta sección.<br>Muy pronto estará disponible.
    </div>
</div>
</section>


<footer class="main-footer">
        <div class="container footer-grid">
            <div class="footer-col logo-col">
                <a href="index.php" class="logo-footer">Maranatha Aragua</a>
                <p>¡MÁS QUE UNA IGLESIA, <br>SOMOS UNA FAMILIA!</p>
            </div>
            <div class="footer-col">
                <h4>¡COMUNÍCATE CON NOSOTROS!</h4>
                <ul>
                    <li><a href="mailto:maranatharagua@gmail.com">maranatharagua@gmail.com</a></li>
                    <li><a href="https://wa.me/qr/DUVWAZFVB5H5K1">+584243565052</a></li>
                </ul>
            </div>
            <div class="footer-col social-col">
                <h4>¡SÍGUENOS!</h4>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                    <a href="#"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>
        </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Maranatha Aragua. Todos los derechos reservados.</p>
    </div>
    <div class="oculta">
   <a href="login.php" class="boton-oculto">Ir a Administrador acceso </a>
  </div>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    // Galería: scroll suave y activo en pestañas con offset por header
    document.addEventListener('DOMContentLoaded', function () {
        var tabs = document.querySelectorAll('.gallery-tabs .tab');
        var header = document.querySelector('.main-header');
        var headerOffset = header ? header.offsetHeight : 0;

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                // Marcar activo
                tabs.forEach(function(t){ t.classList.remove('active'); });
                this.classList.add('active');
                // Scroll al target (con offset para header fijo)
                var target = this.getAttribute('data-target');
                if (target) {
                    var el = document.querySelector(target);
                    if (el) {
                        var rect = el.getBoundingClientRect();
                        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        var top = rect.top + scrollTop - headerOffset - 12; // 12px extra de margen
                        window.scrollTo({ top: top, behavior: 'smooth' });
                    }
                }
            });
            // Allow keyboard activation (Enter/Space)
            tab.addEventListener('keydown', function(e){
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    });
    </script>
    <script src="scripts.js"></script>
</body>
</html>