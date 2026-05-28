<?php
session_start();
require_once '../../config/db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id'])) {
    header('Location: ../../Login/login.php');
    exit();
}

// Obtener la fecha seleccionada o usar la fecha de hoy
if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
} else {
    $fecha = date('Y-m-d');
}

$hoy = date('Y-m-d');

// No permitir fechas anteriores a hoy
if ($fecha < $hoy) {
    $fecha = $hoy;
}

// Fechas para las flechas de navegación
$fecha_anterior = date('Y-m-d', strtotime($fecha . ' -1 day'));
$fecha_siguiente = date('Y-m-d', strtotime($fecha . ' +1 day'));

// Horas disponibles
$horas = ['11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'];

// Pistas de tenis disponibles
$pistas = [
    ['id' => 11, 'nombre' => 'Pista Cubierta 1', 'imagen' => 'teniscubierto.jpg'],
    ['id' => 12, 'nombre' => 'Pista Cubierta 2', 'imagen' => 'teniscubierto.jpg'],
    ['id' => 13, 'nombre' => 'Pista Exterior 1', 'imagen' => 'tenisexterior.png'],
    ['id' => 14, 'nombre' => 'Pista Exterior 2', 'imagen' => 'tenisexterior.png'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas Tenis</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+NZ+Basic:wght@100..400&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="logos">
            <a href="../../Principal/principal.php">
                <img src="logo.png" alt="Logo" class="logo">
            </a>
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
                <span class="saldo"><?php echo $_SESSION['saldo_monedas']; ?></span>
            </div>
            <?php
            if (isset($_SESSION['id'])) {
                echo '<a href="../../MiCuenta/micuenta.php" class="login-button">Hola, ' . $_SESSION['nombre'] . '</a>';
                echo '<a href="../../logout.php" class="cerrar">Cerrar Sesión</a>';
            } else {
                echo '<a href="../../Login/login.php" class="login-button">Acceder</a>';
            }
            ?>
        </nav>
    </header>

    <main class="reservas">
        <h1 class="titulo-deporte">TENIS</h1>

        <?php
        // Recorrer todas las pistas y mostrar su disponibilidad
        foreach ($pistas as $pista) {
            echo '<details class="panel">';
            echo '<summary class="panel-header">';
            echo '<img src="' . $pista['imagen'] . '" alt="' . $pista['nombre'] . '">';
            echo '<div class="header-info"><span class="titulo">' . $pista['nombre'] . '</span><span class="precio">10€/hora</span></div>';
            echo '</summary>';

            // Selector de fecha
            echo '<div class="selector-fecha">';
            if ($fecha > $hoy) {
                echo '<a href="?fecha=' . $fecha_anterior . '" class="flecha">&#8249;</a>';
            } else {
                echo '<span class="flecha" style="opacity:0.3;">&#8249;</span>';
            }
            echo '<span class="fecha">' . date('d/m/Y', strtotime($fecha)) . '</span>';
            echo '<a href="?fecha=' . $fecha_siguiente . '" class="flecha">&#8250;</a>';
            echo '</div>';
            echo '<p class="estado">Disponible <span style="color:green;">&#128994;</span> | Ocupado <span style="color:red;">&#128308;</span></p>';
            echo '<div class="contenido"><div class="horario-grid">';

            // Comprobar disponibilidad de cada hora
            foreach ($horas as $hora) {
                $id_pista_actual = $pista['id'];
                $hora_completa = $hora . ':00';
                $consulta = $pdo->query("SELECT COUNT(*) FROM reservas WHERE id_pista = $id_pista_actual AND fecha = '$fecha' AND hora_inicio = '$hora_completa'");
                $ocupado = $consulta->fetchColumn() > 0;
                if ($ocupado) {
                    echo '<div class="slot ocupado">' . $hora . '</div>';
                } else {
                    echo '<a href="formulario-tenis.php?id_pista=' . $pista['id'] . '&fecha=' . $fecha . '&hora=' . $hora . '" class="slot disponible">' . $hora . '</a>';
                }
            }
            echo '</div></div></details>';
        }
        ?>
    </main>
</body>
</html>