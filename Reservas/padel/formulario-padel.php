<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['id'])) {
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
    $alquiler_raqueta = isset($_POST['pala']) ? 1 : 0;
    $id_pista = $_POST['id_pista'];

    // Precio en euros (para pagar con tarjeta, bizum o efectivo)
    $precio_euros = ($duracion_horas * 20) + ($alquiler_pelotas ? 8 : 0) + ($alquiler_raqueta ? 5 : 0);
    // Precio en BMVCoins (150 coins por hora de pádel)
    $precio_coins = $duracion_horas * 150;

    // Verificar si el usuario tiene suficientes monedas para pagar con BMVCoins
    if ($tipo_pago === 'bmvcoins') {
        $stmt = $pdo->prepare('SELECT saldo_monedas FROM usuarios WHERE id = ?');
        $stmt->execute([$id_usuario]);
        $saldo_monedas = $stmt->fetchColumn();

        if ($saldo_monedas < $precio_coins) {
            echo "<script>alert('No tienes suficientes BMVCoins para realizar esta reserva.');</script>";
            exit();
        }
    }

    $precio_total = ($tipo_pago === 'bmvcoins') ? $precio_coins : $precio_euros;

    // Insertar la reserva en la base de datos
    $stmt = $pdo->prepare('INSERT INTO reservas (id_usuario, fecha, hora_inicio, duracion_horas, tipo_pago, alquiler_pelotas, alquiler_raqueta, precio_total, id_pista) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if ($stmt->execute([$id_usuario, $fecha, $hora_inicio, $duracion_horas, $tipo_pago, $alquiler_pelotas, $alquiler_raqueta, $precio_total, $id_pista])) {
        if ($tipo_pago === 'bmvcoins') {
            // Descontar las monedas del usuario
            $stmt = $pdo->prepare('UPDATE usuarios SET saldo_monedas = saldo_monedas - ? WHERE id = ?');
            $stmt->execute([$precio_coins, $id_usuario]);
            $_SESSION['saldo_monedas'] -= $precio_coins;
        } else {
            // Ganar la mitad del precio en euros como BMVCoins
            $monedas_ganadas = (int)($precio_euros / 2);
            $pdo->prepare('UPDATE usuarios SET saldo_monedas = saldo_monedas + ? WHERE id = ?')->execute([$monedas_ganadas, $id_usuario]);
            $_SESSION['saldo_monedas'] += $monedas_ganadas;
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

        <h1>Formulario de Reserva - Pádel</h1>

        <form action="formulario-padel.php" method="post" class="formulario">

            <input type="hidden" name="id_pista" value="<?= isset($_GET['id_pista']) ? $_GET['id_pista'] : '' ?>">

            <div class="grupo">
                <label>Nombre Completo:</label>
                <input type="text" value="<?= htmlspecialchars($_SESSION['nombre']) ?>" readonly>
            </div>

            <div class="grupo-fecha">
                <label>Fecha:</label>
                <input type="date" name="fecha" value="<?= isset($_GET['fecha']) ? $_GET['fecha'] : '' ?>" readonly required>
            </div>

            <div class="grupo">
                <label>Hora de inicio:</label>
                <input type="time" name="hora_inicio" value="<?= isset($_GET['hora']) ? $_GET['hora'] : '' ?>" readonly required>
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
                    <label><input type="checkbox" name="pala" value="1"> Pala (5€)</label>
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
