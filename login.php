<?php
session_start();
require_once "conexion.php";

if (isset($_SESSION['usuario_id'])) {
    header("Location: productos.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Por favor completa todos los campos.";
    } else {
        $sql = "SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {
            // Validación directa (compatible con contraseñas guardadas como '123456')
            if ($password === $fila["password"]) {
                $_SESSION["usuario_id"] = $fila["id"];
                $_SESSION["usuario_nombre"] = $fila["nombre"];
                $_SESSION["usuario_rol"] = $fila["rol"];

                header("Location: productos.php");
                exit;
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>StockControl - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-4 text-primary">StockControl</h3>

        <?php if ($error !== ""): ?>
            <div class="alert alert-danger text-center py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control" required placeholder="admin@stockcontrol.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••">
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>
</body>
</html>