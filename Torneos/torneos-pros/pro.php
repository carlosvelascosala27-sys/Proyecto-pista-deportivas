<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Torneos Profesionales</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+NZ+Basic:wght@100..400&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header">
        <div class="logos">
            <img src="logo.png" alt="Logo" class="logo">
            <img src="espana.png" alt="Logo" class="logo2">
        </div>
        <nav class="nav1">
            
                <a href="../../Principal/principal.php" class="inicio">Inicio</a>
                <a href="../../Torneos/torneos.php" class="torneos">Torneos</a>
                <a href="../../Contacto/contacto.php" class="contacto">Contacto</a>
        </nav>
        <nav class="nav2">

            <div class="monedas">
                <img src="moneda.png" class="moneda">
                <span class="saldo"><?= $_SESSION['saldo_monedas'] ?? 0 ?></span>
            </div>

            <?php if (isset($_SESSION['id'])): ?>
                <span class="login-button">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
                <a href="../../MiCuenta/mi_cuenta.php" class="mi-cuenta">Mi Cuenta</a>
                <a href="../../logout.php" class="cerrar">Cerrar Sesión</a>
            <?php else: ?>
                <a href="../../Login/login.php" class="login-button">Acceder</a>
            <?php endif; ?>

        </nav>
    </header>
    <section class="torneos-section">
        <div class="torneos-header">
            <h1>Torneos Profesionales</h1>
            <p>Torneos dirigidos a jugadores profesionales de todos los niveles, con organización y ambiente competitivo.
            </p>
        </div>
    </section>

    <section class="torneos-parte">
        <div class="contenedor-cards">
            <a href="#" class="card-torneo">
                <img src="cartel-padel.png" alt="Torneo de Pádel">
                <div class="card-contenido">
                    <h3>Torneo Pro Pádel - A3Pistas</h3>
                    <p>
                        Campeonato profesional por parejas con fase previa y cuadro final.
                        Nivel competitivo y premios especiales.
                        <strong>PRÓXIMAMENTE SE ABRIRÁN LAS INSCRIPCIONES.</strong>
                    </p>
                              
                </div>
            </a>

            <a href="#" class="card-torneo">
                <img src="cartel-tenis.png" alt="Torneo de Tenis">
                <div class="card-contenido">
                    <h3>Torneo Pro Tenis - A3Pistas</h3>
                    <p>
                        Competición de alto nivel para jugadores avanzados.
                        Formato eliminatorio con árbitros oficiales y premios económicos.
                        <strong>PRÓXIMAMENTE SE ABRIRÁN LAS INSCRIPCIONES.</strong>
                    </p>
                </div>
            </a>

        </div>
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
                    <li><a href="../../Principal/principal.php">Inicio</a></li>
                    <li><a href="../../Torneos/torneos.php">Torneos</a></li>
                    <li><a href="../../Entrenamientos/entrenamientos.php">Entrenamientos</a></li>
                    <li><a href="../../Contacto/contacto.php">Contacto</a></li>
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

    