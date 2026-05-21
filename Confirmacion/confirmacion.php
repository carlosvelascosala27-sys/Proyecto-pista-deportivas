<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['id'])) { // Si el usuario no ha iniciado sesión, redirigir a la página de inicio de sesión
    header('Location: ../../Login/login.php');
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
            <h1>✔ Reserva Confirmada</h1>

            <p class="mensaje">
                Gracias por reservar con nosotros.
            </p>

            <p class="mensaje">
                Gracias por elegir <strong>A3Pistas</strong>.
            </p>

            <div class="botones">
                <a href="../Principal/principal.php" class="btn">Volver al inicio</a>
                <a href="../Reservas/Reservas/reservas.php" class="btn-secundario">Ver mis reservas</a>
            </div>
        </div>

    </main>

</body>
</html>