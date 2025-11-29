<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - Maranatha Aragua</title>
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
                <a href="nuestro ministerio.php" class="nav-link">Nuestro Ministerio<a>
                <a href="evangelismo.php" class="nav-link">EEC</a> 
                <a href="galeria.php" class="nav-link">Galería</a> 
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

    <main>
    <!-- ===========================
    HERO – CALENDARIO
=========================== -->
<section class="events-hero">
    <div class="container text-center">
        <h1 class="hero-title anim-up">Descubre Nuestros Eventos</h1>
        <p class="hero-subtitle anim-up"></p>

        <img src="img/calendario principal.jpeg" 
            alt="Imagen principal de calendario" 
            class="hero-image-banner img-fluid rounded shadow anim-up anim-pulse zoom-trigger" 
            data-image="img/calendario principal.jpeg">
    </div>
</section>

<!-- ===========================
    CALENDARIO 3D FLIP
=========================== -->
<section class="section-padding" id="calendario-eventos">
    <div class="container">
        <h2 class="section-title">Te invitamos a Nuestros Eventos</h2>
        <p class="text-center mb-5 lead">
            
        </p>

        <div class="eventos-grid">

            <!-- ENERO -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">ENERO</div>
                    <div class="evento-back">
                        <img src="img/enero.webp" alt="Eventos Enero">
                    </div>
                </div>
            </div>

            <!-- FEBRERO -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">FEBRERO</div>
                    <div class="evento-back">
                        <img src="img/febrero.webp" alt="Eventos Febrero">
                    </div>
                </div>
            </div>

            <!-- MARZO -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">MARZO</div>
                    <div class="evento-back">
                        <img src="img/marzo.webp" alt="Eventos Marzo">
                    </div>
                </div>
            </div>

            <!-- ABRIL -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">ABRIL</div>
                    <div class="evento-back">
                        <img src="img/abril.jpg" alt="Eventos Abril">
                    </div>
                </div>
            </div>

            <!-- MAYO -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">MAYO</div>
                    <div class="evento-back">
                        <img src="img/mayo.jpg" alt="Eventos Mayo">
                    </div>
                </div>
            </div>

            <!-- JUNIO -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">JUNIO</div>
                    <div class="evento-back">
                        <img src="img/junio.jpg" alt="Eventos Junio">
                    </div>
                </div>
            </div>

            <!-- JULIO -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">JULIO</div>
                    <div class="evento-back">
                        <img src="img/julio.png" alt="Eventos Julio">
                    </div>
                </div>
            </div>

            <!-- AGOSTO -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">AGOSTO</div>
                    <div class="evento-back">
                        <img src="img/agosto.jpg" alt="Eventos Agosto">
                    </div>
                </div>
            </div>

            <!-- SEPTIEMBRE -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">SEPTIEMBRE</div>
                    <div class="evento-back">
                        <img src="img/septiembre.jpg" alt="Eventos Septiembre">
                    </div>
                </div>
            </div>

            <!-- OCTUBRE -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">OCTUBRE</div>
                    <div class="evento-back">
                        <img src="img/octubre.png" alt="Eventos Octubre">
                    </div>
                </div>
            </div>

            <!-- NOVIEMBRE -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">NOVIEMBRE</div>
                    <div class="evento-back">
                        <img src="img/noviembre.jpg" alt="Eventos Noviembre">
                    </div>
                </div>
            </div>

            <!-- DICIEMBRE -->
            <div class="evento-card">
                <div class="evento-inner">
                    <div class="evento-front">DICIEMBRE</div>
                    <div class="evento-back">
                        <img src="img/diciembre.png" alt="Eventos Diciembre">
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- 🔥 OVERLAY CORRECTO PARA EL ZOOM -->
<div id="fullscreenOverlay" class="fullscreen-overlay">
    <button id="closeOverlay" class="close-button">&times;</button>
    <img id="fullscreenImage" src="" alt="Imagen ampliada">
</div>


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
    <script src="scripts.js"></script>
</body>
</html>