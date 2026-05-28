<?php
session_start();
require_once '../../config/db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id'])) {
    header('Location: ../../Login/login.php');
    exit();
}

// Procesar el formulario de reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_SESSION['id'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $duracion_horas = $_POST['duracion'];
    $tipo_pago = $_POST['pago'];
    $id_pista = $_POST['id_pista'];

    // Comprobar si se alquila el balón
    if (isset($_POST['balon'])) {
        $alquiler_balon = 1;
    } else {
        $alquiler_balon = 0;
    }

    // Calcular el precio total de la reserva
    $precio_euros = $duracion_horas * 6;
    if ($alquiler_balon == 1) {
        $precio_euros = $precio_euros + 3;
    }

    // Calcular el precio en BMVCoins (80 BMVCoins por hora de fútbol)
    $precio_coins = $duracion_horas * 80;

    // Verificar si el usuario tiene suficientes BMVCoins
    if ($tipo_pago == 'bmvcoins') {
        $resultado = $pdo->query("SELECT saldo_monedas FROM usuarios WHERE id = $id_usuario");
        $saldo_monedas = $resultado->fetchColumn();

        if ($saldo_monedas < $precio_coins) {
            echo "<script>alert('No tienes suficientes BMVCoins para realizar esta reserva.');</script>";
            exit();
        }
    }

    if ($tipo_pago == 'bmvcoins') {
        $precio_total = $precio_coins;
    } else {
        $precio_total = $precio_euros;
    }

    // Insertar la reserva en la base de datos
    $resultado = $pdo->query("INSERT INTO reservas (id_usuario, fecha, hora_inicio, duracion_horas, tipo_pago, alquiler_pelotas, alquiler_raqueta, precio_total, id_pista) VALUES ($id_usuario, '$fecha', '$hora_inicio', $duracion_horas, '$tipo_pago', $alquiler_balon, 0, $precio_total, $id_pista)");

    // Si el pago es con BMVCoins, descontar el saldo; si no, ganar monedas
    if ($resultado) {
        if ($tipo_pago == 'bmvcoins') {
            $pdo->query("UPDATE usuarios SET saldo_monedas = saldo_monedas - $precio_coins WHERE id = $id_usuario");
            $_SESSION['saldo_monedas'] -= $precio_coins;
        } else {
            $monedas_ganadas = (int)($precio_euros / 2);
            $pdo->query("UPDATE usuarios SET saldo_monedas = saldo_monedas + $monedas_ganadas WHERE id = $id_usuario");
            $_SESSION['saldo_monedas'] += $monedas_ganadas;
        }
        exit();
    } else {
        echo "<script>alert('Error al confirmar la reserva. Por favor, inténtalo de nuevo.');</script>";
    }
}

// Obtener los valores de id_pista, fecha y hora de la URL para prellenar el formulario
$id_pista_valor = '';
if (isset($_GET['id_pista'])) {
    $id_pista_valor = $_GET['id_pista'];
}

$fecha_valor = '';
if (isset($_GET['fecha'])) {
    $fecha_valor = $_GET['fecha'];
}

$hora_valor = '';
if (isset($_GET['hora'])) {
    $hora_valor = $_GET['hora'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas</title>
    <link rel="stylesheet" href="css/style2.css">
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

    <main class="formulario-container">

        <h1>Formulario de Reserva - Fútbol</h1>

        <form action="formulario-futbol.php" method="post" class="formulario">

            <input type="hidden" name="id_pista" value="<?php echo $id_pista_valor; ?>">

            <div class="grupo">
                <label>Nombre Completo:</label>
                <input type="text" value="<?php echo $_SESSION['nombre']; ?>" readonly>
            </div>

            <div class="grupo-fecha">
                <label>Fecha:</label>
                <input type="date" name="fecha" value="<?php echo $fecha_valor; ?>" readonly required>
            </div>

            <div class="grupo">
                <label>Hora de inicio:</label>
                <input type="time" name="hora_inicio" value="<?php echo $hora_valor; ?>" readonly required>
            </div>

            <div class="grupo">
                <label>Duración:</label>
                <select name="duracion" required>
                    <option value="1">1 hora</option>
                    <option value="1.5">1 hora y media</option>
                    <option value="2">2 horas</option>
                </select>
            </div>

            <div class="grupo">
                <label>Tipo de pago:</label>
                <div class="opciones">
                    <label><input type="radio" name="pago" value="tarjeta"> Tarjeta</label>
                    <label><input type="radio" name="pago" value="bizum"> Bizum</label>
                    <label><input type="radio" name="pago" value="efectivo"> Efectivo</label>
                    <label><input type="radio" name="pago" value="bmvcoins"> BMVCoins</label>
                </div>
            </div>

            <div class="grupo">
                <label>Alquiler de material:</label>
                <div class="opciones">
                    <label><input type="checkbox" name="balon" value="1"> Balón (3€)</label>
                </div>
            </div>

            <div class="grupo">
                <label>
                    <input type="checkbox" required>
                    Acepto la política de cancelación
                </label>
            </div>

            <button type="submit">Confirmar Reserva</button>

        </form>

        <p class="politica">
            Las reservas podrán cancelarse hasta 1 hora antes del inicio.
            Pasado ese tiempo no se permitirá la cancelación.
        </p>

    </main>
</body>
</html>