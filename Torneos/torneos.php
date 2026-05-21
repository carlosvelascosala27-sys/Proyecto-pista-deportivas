<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Torneos</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+NZ+Basic:wght@100..400&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header">
        <div class="logos">
            <a href="../Principal/principal.html">
                <img src="logo.png" alt="Logo" class="logo">
            </a>
            <img src="espana.png" alt="Logo" class="logo2">
        </div>
        <nav class="nav1">
            
                <a href="../Principal/principal.html" class="inicio">Inicio</a>
                <a href="torneos.html" class="torneos">Torneos</a>
                <a href="../Contacto/contacto.html" class="contacto">Contacto</a>
        </nav>
        <nav class="nav2">

            <div class="monedas">
                <img src="moneda.png" class="moneda">
                <span class="saldo"><?= $_SESSION['saldo_monedas'] ?? 0 ?></span>
            </div>

            <?php if (isset($_SESSION['id'])): ?>
                <span class="login-button">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
                <a href="../logout.php" class="cerrar">Cerrar Sesión</a>
            <?php else: ?>
                <a href="../Login/login.php" class="login-button">Acceder</a>
            <?php endif; ?>

        </nav>
    </header>

    <section class="torneos-section">
        <div class="torneos-header">
            <h1>Torneos</h1>
            <p>Compite, mejora y demuestra tu nivel en A3Pistas</p>
        </div>
    </section>

    <section class="intro-torneos">
        <div class="intro-contenido">
            <div class="intro-titulo">
                <h2>Torneos para adultos y juniors</h2>
            </div>

            <div class="intro-texto">
                <p>En A3Pistas organizamos torneos deportivos para jugadores de todos los niveles. 
                    Vive la competición, mejora tu técnica y disfruta de una experiencia única.
                </p>
            </div>
        </div>
    </section>

    <section class="torneos-parte">
        
        <a href="torneos-adultos/adultos.html" class="tarjeta-adultos">
            <img src="adultos.jpeg" alt="Torneos Adultos">
            <h3>Adultos</h3>
            <p>Los jugadores inscritos vivirán una experiencia profesional única. (+18)</p>
        </a>

        <a href="torneos-juniors/juniors.html" class="tarjeta-juniors">
            <img src="juniors.jpeg" alt="Torneos Juniors">
            <div class="texto">
                <h3>Juniors</h3>
                <p>Competiciones internacionales de categoría juvenil. (-18)</p>
            </div>
        </a>

        <a href="torneos-pros/pro.html" class="tarjeta-pro">
            <img src="pros.jpeg" alt="Torneos Pro">
            <div class="texto">
                <h3>Pro</h3>
                <p>Torneos de alto nivel profesional. (Recomendado para jugadores avanzados)</p>
            </div>
        </a>

   </section>

   <footer class="footer">
        <div class="footer-contenido">

            <div class="footer-col">
                <h3>A3Pitas</h3>
                <p>Centro deportivo especializado en reservas, torneos y alquiler de pistas.</p>
            </div>

            <div class="footer-col">
                <h4>Enlaces</h4>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Torneos</a></li>
                    <li><a href="#">Entrenamientos</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contacto</h4>
                <p>Alicante, España</p>
                <p>+34 600 000 000</p>
                <p>info@a3pistas.com</p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© 2025 A3Pitas. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>