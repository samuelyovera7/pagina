<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestro Ministerio - Maranatha Aragua</title>
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
                <a href="eventos.php" class="nav-link">Eventos</a>
                <a href="evangelismo.php" class="nav-link">EEC</a> 
                <a href="galeria.php" class="nav-link">Galeria</a>
                <a href="radiomaranatharagua.php" class="nav-link">Radionline</a>
                <a href="#" class="nav-link cta-outline" id="contactanosBtn">CONTÁCTANOS</a> 
            </nav>
            <button class="menu-toggle" aria-label="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <main>
        <section class="historia-section section-padding">
            <div class="container">
                <h2 class="section-title fade-in-up reveal delay-1">NUESTRA HISTORIA</h2>
                <div class="value-card fade-in-up reveal delay-2 historia-card">
                <p>
                La Iglesia Maranatha Aragua forma parte del Ministerio Mundial "Maranatha", <br>el cuál se fundó en la cuidad de Chicago en 1974. La Iglesia Marantha Venezuela, <br>se consolidó oficialmente en 1999, en Valencia, Edo Carabobo. Nuestra Iglesia, <br>establecida con el propósito de proclamar el mensaje de salvación y amor de Jesús<br> ha desempeñado un papel importante en la formación espirítual desde el 2017.
                </p>
                </div>
            </div>
        </section>

        <section id="mision-vision" class="py-5 bg-light section-padding">
            <div class="container">
                <h2 class="text-center display-5 section-title reveal fade-in-up">MISIÓN Y VISIÓN</h2>
                <div class="row pt-5">
                    <div class="col-md-6 mb-4 reveal fade-in-left">
                        <div class="card p-4 h-100 text-center shadow-lg border-0">
                            <i class="fas fa-bullseye fa-3x mb-3 text-primary"></i>
                            <h3 class="card-title fw-bold">Misión</h3>
                            <p class="card-text">Llenar la tierra con el conocimiento de la gloria de Dios. “Jesús se acercó y les habló diciendo: Toda potestad me es dada en el cielo y en la tierra. Por tanto, id, y haced discípulos a todas las naciones, bautizándolos en el nombre del Padre, y del Hijo, y del Espíritu Santo; enseñándoles que guarden todas las cosas que os he mandado; y he aquí yo estoy con vosotros todos los días, hasta el fin del mundo. Amén”. Mateo 28:18-20.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 reveal fade-in-right">
                        <div class="card p-4 h-100 text-center shadow-lg border-0">
                            <i class="fas fa-glasses fa-3x mb-3 text-primary"></i>
                            <h3 class="card-title fw-bold">Visión</h3>
                            <p class="card-text">Ser una iglesia cristiana de impacto y referencia espiritual en el estado, que refleje con excelencia la Gloria de Dios y promueva una transformación real y permanente en corazones, familias y comunidades del estado Aragua. Buscando extender el conocimiento de la Gloria de Dios hasta lo último de la tierra, hasta que Cristo sea conocido, amado y exaltado por Todos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

            <div class="container">
                <h1 class="fade-in-up">Nuestro Ministerio</h1>
                <p class="fade-in-up" style="animation-delay: 0.2s;">Conoce el corazón y la obra de Maranatha Aragua.</p>
            </div>
        </section>

        <section class="impact-section reveal-zoom animated-background-container" id="impacto">
            <div class="container">
                <h2 class="section-title">MINISTERIO MUNDIAL MARANATHA</h2>
                <div class="counter-grid">
                <div class="counter-item">
                <i class="fas fa-globe"></i>
                <div id="counter-countries" class="count-value">+0</div>
                <p>Países Alcanzados</p>
            </div>
            <div class="counter-item">
                <i class="fas fa-church"></i>
                <div id="counter-churches" class="count-value">+0</div>
                <p>Congregaciones</p>
            </div>
                <div class="counter-item">
                <i class="fas fa-users"></i>
                <div id="counter-souls" class="count-value">+0</div>
                <p>Almas Transformadas</p>
            </div>
            </div>
            </div>
            </div>
        </section>        
    </main>

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

    <section id="presencia" class="client-slider-section section-padding reveal">
    <div class="container" style="text-align: center;">
        <h2 class="section-title">PAÍSES PRINCIPALES</h2>
    </div>
    
    <div class="client-slider-track">
        <div class="client-logo-wrapper flag-es">
            <div class="country-flag-svg outline-es"></div> <p>España</p></div>
        <div class="client-logo-wrapper flag-mx">
            <div class="country-flag-svg outline-mx"></div> <p>México</p></div>
        <div class="client-logo-wrapper flag-co">
            <div class="country-flag-svg outline-co"></div> <p>Colombia</p></div>
        <div class="client-logo-wrapper flag-ar">
            <div class="country-flag-svg outline-ar"></div> <p>Argentina</p></div>
        <div class="client-logo-wrapper flag-us">
            <div class="country-flag-svg outline-us"></div> <p>Estados Unidos</p></div>
        <div class="client-logo-wrapper flag-ve">
            <div class="country-flag-svg outline-ve"></div> <p>Venezuela</p></div>
        <div class="client-logo-wrapper flag-ec">
            <div class="country-flag-svg outline-ec"></div> <p>Ecuador</p></div>
        <div class="client-logo-wrapper flag-pa">
            <div class="country-flag-svg outline-pa"></div> <p>Panamá</p></div>
        <div class="client-logo-wrapper flag-br">
            <div class="country-flag-svg outline-br"></div> <p>Brasil</p></div>
        <div class="client-logo-wrapper flag-cl">
            <div class="country-flag-svg outline-cl"></div> <p>Chile</p></div>
        <div class="client-logo-wrapper flag-cr">
            <div class="country-flag-svg outline-cr"></div> <p>Costa Rica</p></div>
        <!-- Repetición para animación infinita -->
        <div class="client-logo-wrapper flag-es"><div class="country-flag-svg outline-es"></div><p>España</p></div>
        <div class="client-logo-wrapper flag-mx"><div class="country-flag-svg outline-mx"></div><p>México</p></div>
        <div class="client-logo-wrapper flag-co"><div class="country-flag-svg outline-co"></div><p>Colombia</p></div>
        <div class="client-logo-wrapper flag-ar"><div class="country-flag-svg outline-ar"></div><p>Argentina</p></div>
        <div class="client-logo-wrapper flag-us"><div class="country-flag-svg outline-us"></div><p>Estados Unidos</p></div>
        <div class="client-logo-wrapper flag-ve"><div class="country-flag-svg outline-ve"></div><p>Venezuela</p></div>
        <div class="client-logo-wrapper flag-ec"><div class="country-flag-svg outline-ec"></div><p>Ecuador</p></div>
        <div class="client-logo-wrapper flag-pa"><div class="country-flag-svg outline-pa"></div><p>Panamá</p></div>
        <div class="client-logo-wrapper flag-br"><div class="country-flag-svg outline-br"></div><p>Brasil</p></div>
        <div class="client-logo-wrapper flag-cl"><div class="country-flag-svg outline-cl"></div><p>Chile</p></div>
        <div class="client-logo-wrapper flag-cr"><div class="country-flag-svg outline-cr"></div><p>Costa Rica</p></div>
    </div>
    </section>

    <section id="lideres" class="lideres-section container">
        <h2 class="section-title reveal-zoom">NUESTROS LÍDERES</h2>
        <div class="lideres-grid">
        <div class="leader-card leader-card-left">
            <img src="img/apostol nahum rosario.jpeg" alt="Nombre del Líder 1" class="leader-photo">
            <h3 class="leader-name">Nahúm Rosario</h3>
            <p class="leader-title">APÓSTOL</p>
            <p class="leader-bio">
                Fundador del Ministerio Mundial de Avivamiento Maranatha, en 1974, y el Centro Internacional Maranatha Panamá en 2013. El mayor propósito del Apóstol ha sido edificar a <br>la Iglesia, trayendo una verdadera revelación de <br>Jesús a los corazones.
            </p>
        </div>

        <div class="leader-card leader-card-right">
            <img src="img/javier y rebeca bertucci.jpeg" alt="Nombre del Líder 2" class="leader-photo">
            <h3 class="leader-name">Javier y Rebecca Bertucci</h3>
            <p class="leader-title">PASTORES</p>
            <p class="leader-bio">
                Principales Pastores y Creadores de Maranatha Venezuela, llevando el evangelio a nuestra nación.
            </p>
        </div>

        <div class="leader-card leader-card-left">
            <img src="img/pastor paco.jpeg" alt="Nombre del Líder 3" class="leader-photo">
            <h3 class="leader-name">Francisco Barrios (Paco)</h3>
            <p class="leader-title">PASTOR</p>
            <p class="leader-bio">
                Pastor Principal de la Iglesia Maranatha Venezuela, junto <br>al pastor Javier Bertucci, dispuesto siempre al servicio de la Iglesia y de todos.</p>
        </div>

        <div class="leader-card leader-card-right">
            <img src="img/pastora Zulmira.PNG" alt="Nombre del Líder 4" class="leader-photo">
            <h3 class="leader-name">Zulmira Rojas</h3>
            <p class="leader-title">PASTORA</p>
            <p class="leader-bio">
                Mujer dispuesta siempre a cumplir la voluntad de Dios, fiel servidora de Jesús, entregada con gran dedicación, pastora, guía <br>y madre espiritual de Maranatha Aragua.
            </p>
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

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN..." crossorigin="anonymous"></script>
    <script src="scripts.js"></script>
</body>
</html>