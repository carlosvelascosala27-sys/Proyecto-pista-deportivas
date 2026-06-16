<?php
session_start();
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Principal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+NZ+Basic:wght@100..400&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header">
        <div class="logos">
            <a href="../Principal/index.php">
                <img src="logo.png" alt="Logo" class="logo">
            </a>
            <img src="espana.png" alt="Logo" class="logo2">
        </div>
        <nav class="nav1">
            
                <a href="../Principal/index.php" class="inicio">Inicio</a>
                <a href="../Torneos/torneos.php" class="torneos">Torneos</a>
                <a href="../Contacto/contacto.php" class="contacto">Contacto</a>
        </nav>
        <nav class="nav2">

            <div class="monedas">
                <img src="moneda.png" class="moneda">
                <span class="saldo"><?= isset($_SESSION['id']) ? htmlspecialchars($_SESSION['saldo_monedas'] ?? 0) : '' ?></span>
            </div>

            <?php if (isset($_SESSION['id'])) { ?>
                <a href="../MiCuenta/micuenta.php" class="login-button">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></a>
                <a href="../logout.php" class="cerrar">Cerrar SesiÃ³n</a>
            <?php } else { ?>
                <a href="../Login/index.php" class="login-button">Acceder</a>
            <?php } ?>

        </nav>
    </header>
    <section class="main">
        <h1>RESERVA TU PISTA EN A3PISTAS</h1>
        <div class="bloques"> 
            <h1>FUTBOL</h1> 
            <p>Contamos con unas instalaciones de futbol para todos los niveles.</p> 
            <img src="futbol-img.png" alt="Futbol">
            <button class="reservar-button"><a href="../Reservas/futbol/reservas-futbol.php">Reservar</a></button>
        </div>

        <div class="bloques"> 
            <h1>PÃDEL</h1> 
            <p>Contamos con unas instalaciones de pÃ¡del para todos los niveles.</p> 
            <img src="padel.png" alt="Padel">
            <button class="reservar-button"><a href="../Reservas/padel/reservas-padel.php">Reservar</a></button>
        </div>

        <div class="bloques"> 
            <h1>TENIS</h1> 
            <p>Contamos con unas instalaciones de tenis para todos los niveles.</p> 
            <img src="tenis.png" alt="Tenis">
            <button class="reservar-button"><a href="../Reservas/tenis/reservas-tenis.php">Reservar</a></button>
        </div>

        <div class="bloques">
            <h1>BALONCESTO</h1> 
            <p>Contamos con unas instalaciones de baloncesto para todos los niveles.</p> 
            <img src="baloncesto.png" alt="Baloncesto">
            <button class="reservar-button"><a href="../Reservas/baloncesto/reservas-baloncesto.php">Reservar</a></button>
        </div>
    </section>

    <section class="bienvenida">
        <h1>Bienvenido a A3Pistas</h1>
    </section>

    <section class="main-2">
        <h1>ENTRENAMIENTOS DEPORTIVOS</h1>
        <div class="adults">
            <img src="adultos.jpg" alt="Adultos">
            <h1>ADULTOS</h1>
            <p>En A3Pitas contamos con entrenamientos deportivos para adultos, los cuales pueden ser individuales o en grupo para mejorar tu tÃ©cnica. Se produce un entorno idÃ­lico para el desarrollo fÃ­sico y mental de los participantes.</p>
        </div>
        
        <div class="kids">
            <img src="niÃ±os.jpg" alt="NiÃ±os">
            <h1>NIÃ‘OS</h1>
            <p>En A3Pitas contamos con entrenamientos deportivos para jÃ³venes, los cuales pueden ser individuales o en grupo para mejorar su tÃ©cnica. Se produce un entorno idÃ­lico para el desarrollo fÃ­sico y mental de los participantes.</p>
        </div>
    </section>

    <section class="torneos-parte">
        <div class="contenido-torneos">
            <img src="torneos.png" alt="Torneos">

            <div class="texto-torneos">
                <h2>Torneos</h2>
                <p>En A3Pitas organizamos torneos deportivos para fomentar la competencia y el espÃ­ritu deportivo entre nuestros usuarios. Nuestros torneos son abiertos a jugadores de todos los niveles, desde principiantes hasta profesionales, y se llevan a cabo en nuestras instalaciones de alta calidad. Participar en nuestros torneos es una excelente manera de poner a prueba tus habilidades, conocer a otros entusiastas del deporte y disfrutar de una experiencia emocionante y competitiva.
                </p>
                <a href="../Torneos/torneos.php" class="btn-torneos">Disponibles</a>
            </div>
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
                    <li><a href="../Principal/index.php">Inicio</a></li>
                    <li><a href="../Torneos/torneos.php">Torneos</a></li>
                    <li><a href="../Entrenamientos/entrenamientos.php">Entrenamientos</a></li>
                    <li><a href="../Contacto/contacto.php">Contacto</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contacto</h4>
                <p>Alicante, EspaÃ±a</p>
                <p>+34 600 000 000</p>
                <p>info@a3pistas.com</p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>Â© 2025 A3Pitas. Todos los derechos reservados.</p>
        </div>
    </footer>


    


</html>
