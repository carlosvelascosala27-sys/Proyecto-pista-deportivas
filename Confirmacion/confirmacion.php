<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['id'])) {
    header('Location: ../Login/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva Confirmada</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <main class="confirmacion-container">

        <div class="confirmacion-card">
            <h1>âœ” Reserva Confirmada</h1>

            <p class="mensaje">
                Gracias por reservar con nosotros.
            </p>

            <p class="mensaje">
                Gracias por elegir <strong>A3Pistas</strong>.
            </p>

            <div class="botones">
                <a href="../Principal/index.php" class="btn">Volver al inicio</a>
                <a href="../MiCuenta/micuenta.php" class="btn-secundario">Ver mis reservas</a>
            </div>
        </div>

    </main>

</body>
</html>
