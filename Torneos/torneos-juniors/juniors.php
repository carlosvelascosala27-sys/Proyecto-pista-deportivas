<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Torneos Juniors</title>
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
            
                <a href="../../Principal/index.php" class="inicio">Inicio</a>
                <a href="../../Torneos/torneos.php" class="torneos">Torneos</a>
                <a href="../../Contacto/contacto.php" class="contacto">Contacto</a>
        </nav>
        <nav class="nav2">

            <div class="monedas">
                <img src="moneda.png" class="moneda">
                <?php
                if (isset($_SESSION['saldo_monedas'])) {
                    echo '<span class="saldo">' . $_SESSION['saldo_monedas'] . '</span>';
                } else {
                    echo '<span class="saldo">0</span>';
                }
                ?>
            </div>
            <?php
            if (isset($_SESSION['id'])) {
                echo '<a href="../../MiCuenta/micuenta.php" class="login-button">Hola, ' . htmlspecialchars($_SESSION['nombre']) . '</a>';
                echo '<a href="../../logout.php" class="cerrar">Cerrar SesiÃ³n</a>';
            } else {
                echo '<a href="../../Login/index.php" class="login-button">Acceder</a>';
            }
            ?>

        </nav>
    </header>
    <section class="torneos-section">
        <div class="torneos-header">
            <h1>Torneos Juniors</h1>
            <p>Torneos dirigidos a jugadores jÃ³venes de todos los niveles, con organizaciÃ³n y ambiente competitivo.
            </p>
        </div>
    </section>

    <section class="torneos-parte">
        <div class="contenedor-cards">
            <a href="torneo_futbol_junior.html" class="card-torneo">
                <img src="cartel-junior.png" alt="Torneo Junior FÃºtbol">

                <div class="card-contenido">
                    <h3>Torneo Junior FÃºtbol</h3>

                    <p>
                        CompeticiÃ³n juvenil por equipos con fase de grupos y eliminatorias.
                        Ambiente deportivo y formativo.
                        <strong>PRÃ“XIMAMENTE SE ABRIRÃN LAS INSCRIPCIONES.</strong>
                    </p>
                </div>
            </a>

            <a href="torneo_tenis_junior.html" class="card-torneo">
                <img src="cartel-padel-tenis.png" alt="Torneo Junior Tenis">

                <div class="card-contenido">
                    <h3>Torneo Junior Tenis y PÃ¡del</h3>

                    <p>
                        Torneo individual juvenil con diferentes categorÃ­as por edad.
                        Sistema de competiciÃ³n oficial.
                        <strong>PRÃ“XIMAMENTE SE ABRIRÃN LAS INSCRIPCIONES.</strong>
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
                    <li><a href="../../Principal/index.php">Inicio</a></li>
                    <li><a href="../../Torneos/torneos.php">Torneos</a></li>
                    <li><a href="../../Entrenamientos/entrenamientos.php">Entrenamientos</a></li>
                    <li><a href="../../Contacto/contacto.php">Contacto</a></li>
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

</body>
</html>
