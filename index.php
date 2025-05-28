<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POA Manager - Gestión de Planes Operativos Anuales</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header-inicio">
        <div class="logo-container">
            <i class="fas fa-chart-line logo-icon"></i>
            <h1>POA Manager</h1>
        </div>
        <div class="auth-buttons">
            <button id="theme-toggle" class="theme-toggle">
                <i class="fas fa-moon"></i>
                <i class="fas fa-sun"></i>
            </button>
            <a href="views/login.php" class="btn btn-login">Iniciar Sesión</a>
            <a href="views/registro.php" class="btn btn-register">Registrarse</a>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h2 class="title-1">¡Bienvenido a POA Manager!</h2>
            <h2 class="title-2">Gestiona tus Planes Operativos Anuales de manera eficiente</h2>
            <p>La herramienta definitiva para planificación, seguimiento y control de tus POAs</p>
            <div class="cta-buttons">
                <a href="views/registro.php" class="btn btn-primary">Comenzar ahora</a>
                <a href="#features" class="btn btn-secondary">Conocer más</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="assets/Img/Vista-previa.png" alt="Vista previa del dashboard">
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <h2 class="section-title">Características Principales</h2>
        <div class="features-container">
            <div class="feature-block">
                <div class="feature-image">
                    <img src="assets/Img/Vista-previa2.png" alt="Gestión de Planes">
                </div>
                <div class="feature-content">
                    <i class="fas fa-calendar-alt feature-icon"></i>
                    <h3>Gestión de Planes</h3>
                    <p>Crea, edita y da seguimiento a tus planes operativos anuales de manera sencilla.</p>
                </div>
            </div>

            <div class="feature-block">
                <div class="feature-image">
                    <img src="assets/Img/Vista-previa3.png" alt="Colaboración en Equipo">
                </div>
                <div class="feature-content">
                    <i class="fas fa-users feature-icon"></i>
                    <h3>Colaboración en Equipo</h3>
                    <p>Trabaja con tu equipo asignando responsabilidades y siguiendo el progreso.</p>
                </div>
            </div>

            <div class="feature-block">
                <div class="feature-image">
                    <img src="assets/Img/Vista-previa.png" alt="Reportes y Análisis">
                </div>
                <div class="feature-content">
                    <i class="fas fa-chart-pie feature-icon"></i>
                    <h3>Reportes y Análisis</h3>
                    <p>Genera reportes detallados y análisis del progreso de tus planes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <i class="fas fa-chart-line"></i>
                <span>POA Manager</span>
            </div>
            <div class="footer-links">
                <a href="#">Términos de servicio</a>
                <a href="#">Política de privacidad</a>
                <a href="mailto:soporte@poamanager.com">Contacto</a>
            </div>
        </div>
        <div class="footer-copyright">
            <p>&copy; <?= date('Y') ?> POA Manager. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="assets/js/theme.js"></script>
</body>
</html>