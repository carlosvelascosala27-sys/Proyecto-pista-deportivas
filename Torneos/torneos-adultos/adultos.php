<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Torneos Adultos</title>
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
                <a href="../../logout.php" class="cerrar">Cerrar Sesión</a>
            <?php else: ?>
                <a href="../../Login/login.php" class="login-button">Acceder</a>
            <?php endif; ?>

        </nav>
    </header>
    <section class="torneos-section">
        <div class="torneos-header">
            <h1>Torneos Adultos</h1>
            <p>Torneos dirigidos a jugadores adultos de todos los niveles, con organización y ambiente competitivo.
            </p>
        </div>
    </section>

    <section class="torneos-parte">
        
        <div class="contenedor-cards">
            <a href="#" class="card-torneo">
            <img src="cartel-futbol.png" alt="Torneo de Futbol">
                <div class="card-contenido">
                    <h3>Torneo Open A3Pistas - Futbol</h3>
                    <p>
                        Participa en nuestro torneo oficial de futbol. 
                        Competición para todos los niveles con premios y trofeos.
                    <strong>
                        PROXIMAMENTE SE ABRIRÁN LAS INSCRIPCIONES.
                    </strong>
                    </p>              
                </div>
            </a>

            <a href="#" class="card-torneo">
                <img src="cartel-padel.png" alt="Torneo de Pádel">
                <div class="card-contenido">
                    <h3>Torneo Primavera - Pádel</h3>
                    <p>
                        Torneo de pádel por parejas con fase de grupos y eliminatorias.
                        Ambiente competitivo y divertido.

                    <strong>
                        PROXIMAMENTE SE ABRIRÁN LAS INSCRIPCIONES.
                    </strong>
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

    