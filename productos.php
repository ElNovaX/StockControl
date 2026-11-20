<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$error = "";
$esAdmin = (($_SESSION["usuario_rol"] ?? "") === "admin");

// Registrar nuevo producto
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "registrar") {
    $codigo = trim($_POST["codigo_barras"] ?? "");
    $nombre = trim($_POST["nombre"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
    $pCompra = (float)($_POST["precio_compra"] ?? 0);
    $pVenta = (float)($_POST["precio_venta"] ?? 0);
    $stock = (int)($_POST["stock"] ?? 0);
    $stockMin = (int)($_POST["stock_minimo"] ?? 0);

    $sqlInsert = "INSERT INTO productos (codigo_barras, nombre, categoria, precio_compra, precio_venta, stock, stock_minimo)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sqlInsert);
    $stmt->bind_param("sssddii", $codigo, $nombre, $categoria, $pCompra, $pVenta, $stock, $stockMin);

    if ($stmt->execute()) {
        $mensaje = "Producto registrado correctamente.";
    } else {
        $error = "No se pudo registrar el producto (revisa que el código de barras no esté repetido).";
    }
    $stmt->close();
}

// Consultar productos
$productos = [];
$res = $conexion->query("SELECT id, codigo_barras, nombre, categoria, precio_compra, precio_venta, stock, stock_minimo FROM productos ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $productos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>StockControl - Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 mb-4 shadow">
        <span class="navbar-brand mb-0 h1">StockControl - Papelería</span>
        <div class="d-flex align-items-center">
            <span class="text-light me-3">
                <strong><?= htmlspecialchars($_SESSION["usuario_nombre"]) ?></strong>
                <span class="badge bg-secondary ms-1"><?= htmlspecialchars($_SESSION["usuario_rol"]) ?></span>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>
    </nav>

    <div class="container mb-5">
        <?php if ($mensaje !== ""): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        <?php if ($error !== ""): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">Registrar nuevo producto</div>
            <div class="card-body">
                <form method="POST" action="productos.php" class="row g-3">
                    <input type="hidden" name="accion" value="registrar">
                    <div class="col-md-3">
                        <label class="form-label">Código de barras</label>
                        <input type="text" name="codigo_barras" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Categoría</label>
                        <input type="text" name="categoria" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock mínimo</label>
                        <input type="number" name="stock_minimo" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Precio compra ($)</label>
                        <input type="number" step="0.01" min="0" name="precio_compra" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Precio venta ($)</label>
                        <input type="number" step="0.01" min="0" name="precio_venta" class="form-control" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">Guardar producto</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-secondary text-white">Inventario actual</div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <?php if ($esAdmin): ?>
                                <th>P. Compra</th>
                            <?php endif; ?>
                            <th>P. Venta</th>
                            <th>Stock</th>
                            <th>Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                            <?php $bajoStock = ((int)$p["stock"] <= (int)$p["stock_minimo"]); ?>
                            <tr>
                                <td><?= (int)$p["id"] ?></td>
                                <td><?= htmlspecialchars($p["codigo_barras"]) ?></td>
                                <td><?= htmlspecialchars($p["nombre"]) ?></td>
                                <td><?= htmlspecialchars($p["categoria"]) ?></td>
                                <?php if ($esAdmin): ?>
                                    <td>$<?= number_format((float)$p["precio_compra"], 2) ?></td>
                                <?php endif; ?>
                                <td>$<?= number_format((float)$p["precio_venta"], 2) ?></td>
                                <td>
                                    <span class="badge <?= $bajoStock ? 'bg-danger' : 'bg-success' ?>">
                                        <?= (int)$p["stock"] ?>
                                    </span>
                                </td>
                                <td><?= (int)$p["stock_minimo"] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>