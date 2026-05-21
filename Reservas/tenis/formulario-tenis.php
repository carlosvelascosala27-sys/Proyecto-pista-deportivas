<?php
session_start();
require_once '../../config/db.php';
if (!isset($_SESSION['id'])) { // Si el usuario no ha iniciado sesión, redirigir a la página de inicio de sesión
    header('Location: ../../Login/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $duracion_horas = $_POST['duracion'];
    $tipo_pago = $_POST['pago'];
    $alquiler_pelotas = isset($_POST['pelotas']) ? 1 : 0;
    $alquiler_raqueta = isset($_POST['raqueta']) ? 1 : 0;
    $precio_total = ($duracion_horas * 10) + ($alquiler_pelotas ? 8 : 0) + ($alquiler_raqueta ? 5 : 0); // Costo base de 10€/hora
    $id_pista = 1; // ID de la pista de tenis, puedes ajustarlo según tu base de datos

    // Verificar si el usuario tiene suficientes monedas para pagar con BMVCoins
    if ($tipo_pago === 'bmvcoins') {
        $stmt = $pdo->prepare('SELECT saldo_monedas FROM usuarios WHERE id = ?');
        $stmt->execute([$id_usuario]);
        $saldo_monedas = $stmt->fetchColumn();

        if ($saldo_monedas < $precio_total) {
            echo "<script>alert('No tienes suficientes BMVCoins para realizar esta reserva.');</script>";
            exit();
        }
    }

    // Insertar la reserva en la base de datos
    $stmt = $pdo->prepare('INSERT INTO reservas (id_usuario, fecha, hora_inicio, duracion_horas, tipo_pago, alquiler_pelotas, alquiler_raqueta, precio_total, id_pista) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if ($stmt->execute([$id_usuario, $fecha, $hora_inicio, $duracion_horas, $tipo_pago, $alquiler_pelotas, $alquiler_raqueta, $precio_total, $id_pista])) {
        // Si el pago es con BMVCoins, descontar las monedas del usuario
        if ($tipo_pago === 'bmvcoins') {
            $stmt = $pdo->prepare('UPDATE usuarios SET saldo_monedas = saldo_monedas - ? WHERE id = ?');
            $stmt->execute([$precio_total, $id_usuario]);
        } else {
            // Aquí puedes agregar la lógica para procesar el pago con tarjeta, Bizum o efectivo
            // Por ejemplo, podrías redirigir a una página de pago o mostrar un mensaje de confirmación
            $monedas_ganadas = (int)$precio_total; // Por cada euro gastado, el usuario gana 1 moneda
            $pdo->prepare('UPDATE usuarios SET saldo_monedas = saldo_monedas + ? WHERE id = ?')->execute([$monedas_ganadas, $id_usuario]); // Actualizar el saldo de monedas en la base de datos
            $_SESSION['saldo_monedas'] += $monedas_ganadas; // Actualizar el saldo de monedas en la sesión
        }
        echo "<script>alert('Reserva confirmada exitosamente.'); window.location.href='../../Confirmacion/confirmacion.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al confirmar la reserva. Por favor, inténtalo de nuevo.');</script>";
    }
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

    <main class="formulario-container">

    <h1>Formulario de Reserva - Tenis</h1>

    <form action="formulario-tenis.php" method="post" class="formulario">

        <div class="grupo">
            <label>Nombre Completo:</label>
            <input type="text" value="<?= htmlspecialchars($_SESSION['nombre']) ?>" readonly>
        </div>

        <div class="grupo-fecha">
            <label>Fecha:</label>
            <input type="date" name="fecha" required>
        </div>

        <div class="grupo">
            <label>Hora de inicio:</label>
            <input type="time" name="hora_inicio" min="09:00" max="22:00" required>
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
                <label><input type="checkbox" name="pelotas" value="1"> Pelotas (8€)</label>
                <label><input type="checkbox" name="raqueta" value="1"> Raqueta (5€)</label>
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