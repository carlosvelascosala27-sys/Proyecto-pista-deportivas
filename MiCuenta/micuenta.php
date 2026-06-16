<?php
session_start();
require_once '../config/db.php';

// Si no ha iniciado sesion, lo mandamos al login
if (!isset($_SESSION['id'])) {
    header('Location: ../Login/index.php');
    exit();
}

$id_usuario = $_SESSION['id'];
$hoy = date('Y-m-d');

// Comprobamos si se ha enviado un formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'];

    // Cancelar una reserva proxima
    if ($accion == 'cancelar') {
        $id_reserva = $_POST['id_reserva'];
        // Cambiamos el estado a cancelada en vez de borrarla
        $pdo->query("UPDATE reservas SET estado = 'cancelada' WHERE id = $id_reserva AND id_usuario = $id_usuario");
    }
}

// Cogemos los datos del usuario de la base de datos
$consulta_usuario = $pdo->query("SELECT nombre, email, saldo_monedas FROM usuarios WHERE id = $id_usuario");
$usuario = $consulta_usuario->fetch();

// Proximas reservas del usuario (de hoy en adelante y no canceladas)
$consulta_proximas = $pdo->query("SELECT reservas.*, pistas.nombre AS nombre_pista FROM reservas JOIN pistas ON reservas.id_pista = pistas.id WHERE reservas.id_usuario = $id_usuario AND reservas.fecha >= '$hoy' AND reservas.estado != 'cancelada' ORDER BY reservas.fecha ASC");
$proximas = $consulta_proximas->fetchAll();

// Historial de reservas pasadas
$consulta_historial = $pdo->query("SELECT reservas.*, pistas.nombre AS nombre_pista FROM reservas JOIN pistas ON reservas.id_pista = pistas.id WHERE reservas.id_usuario = $id_usuario AND reservas.fecha < '$hoy' ORDER BY reservas.fecha DESC");
$historial = $consulta_historial->fetchAll();

// Todas las reservas para el apartado de BMVCoins
$consulta_todas = $pdo->query("SELECT reservas.*, pistas.nombre AS nombre_pista FROM reservas JOIN pistas ON reservas.id_pista = pistas.id WHERE reservas.id_usuario = $id_usuario ORDER BY reservas.fecha DESC");
$todas = $consulta_todas->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Cuenta</title>
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
                <?php echo '<span class="saldo">' . $_SESSION['saldo_monedas'] . '</span>'; ?>
            </div>
            <?php
            if (isset($_SESSION['id'])) {
                echo '<span class="login-button">Hola, ' . htmlspecialchars($_SESSION['nombre']) . '</span>';
                echo '<a href="../logout.php" class="cerrar">Cerrar Sesión</a>';
            } else {
                echo '<a href="../Login/index.php" class="login-button">Acceder</a>';
            }
            ?>
        </nav>
    </header>

    <main class="micuenta-container">

        <section class="seccion">
            <h1>Mi Cuenta</h1>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
            <p><strong>BMVCoins:</strong> <?php echo $usuario['saldo_monedas']; ?> coins</p>
        </section>

        <section class="seccion">
            <h2>Próximas Reservas</h2>
            <?php
            if (count($proximas) == 0) {
                echo '<p class="sin-datos">No tienes reservas próximas.</p>';
            } else {
                echo '<table class="tabla-reservas">';
                echo '<thead><tr>';
                echo '<th>Pista</th><th>Fecha</th><th>Hora</th><th>Duración</th><th>Precio</th><th>Pago</th><th>Estado</th><th></th>';
                echo '</tr></thead><tbody>';
                // Recorremos las proximas reservas
                foreach ($proximas as $reserva) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($reserva['nombre_pista']) . '</td>';
                    echo '<td>' . $reserva['fecha'] . '</td>';
                    echo '<td>' . $reserva['hora_inicio'] . '</td>';
                    echo '<td>' . $reserva['duracion_horas'] . ' hora/s</td>';
                    echo '<td>' . $reserva['precio_total'] . '€</td>';
                    echo '<td>' . $reserva['tipo_pago'] . '</td>';
                    echo '<td>' . $reserva['estado'] . '</td>';
                    // Boton de cancelar que envia el id de la reserva por POST
                    echo '<td>';
                    echo '<form action="micuenta.php" method="post">';
                    echo '<input type="hidden" name="accion" value="cancelar">';
                    echo '<input type="hidden" name="id_reserva" value="' . $reserva['id'] . '">';
                    echo '<button type="submit" class="btn-cancelar">Cancelar</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }
            ?>
        </section>

        <section class="seccion">
            <h2>Historial</h2>
            <?php
            if (count($historial) == 0) {
                echo '<p class="sin-datos">No tienes reservas anteriores.</p>';
            } else {
                echo '<table class="tabla-reservas">';
                echo '<thead><tr>';
                echo '<th>Pista</th><th>Fecha</th><th>Hora</th><th>Duración</th><th>Precio</th><th>Pago</th><th>Estado</th>';
                echo '</tr></thead><tbody>';
                // Recorremos el historial de reservas pasadas
                foreach ($historial as $reserva) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($reserva['nombre_pista']) . '</td>';
                    echo '<td>' . $reserva['fecha'] . '</td>';
                    echo '<td>' . $reserva['hora_inicio'] . '</td>';
                    echo '<td>' . $reserva['duracion_horas'] . ' hora/s</td>';
                    echo '<td>' . $reserva['precio_total'] . '€</td>';
                    echo '<td>' . $reserva['tipo_pago'] . '</td>';
                    echo '<td>' . $reserva['estado'] . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }
            ?>
        </section>

        <section class="seccion">
            <h2>BMVCoins</h2>
            <p>Saldo actual: <strong><?php echo $usuario['saldo_monedas']; ?> coins</strong></p>
            <?php
            if (count($todas) == 0) {
                echo '<p class="sin-datos">No hay movimientos de BMVCoins.</p>';
            } else {
                echo '<table class="tabla-reservas">';
                echo '<thead><tr>';
                echo '<th>Fecha</th><th>Pista</th><th>Tipo</th><th>Coins</th>';
                echo '</tr></thead><tbody>';
                foreach ($todas as $reserva) {
                    echo '<tr>';
                    echo '<td>' . $reserva['fecha'] . '</td>';
                    echo '<td>' . htmlspecialchars($reserva['nombre_pista']) . '</td>';
                    // Si pago con coins se muestra como gasto, si no como ganancia
                    if ($reserva['tipo_pago'] == 'bmvcoins') {
                        echo '<td class="gasto">Pago con BMVCoins</td>';
                        echo '<td class="gasto">-' . $reserva['precio_total'] . ' coins</td>';
                    } else {
                        $coins_ganados = (int)($reserva['precio_total'] / 2);
                        echo '<td class="ganancia">Reserva pagada</td>';
                        echo '<td class="ganancia">+' . $coins_ganados . ' coins</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }
            ?>
        </section>

    </main>
</body>
</html>
