# StockControl - Sistema de Control de Inventario y Papelería

Aplicación web para la gestión de inventario y control de acceso por roles en una papelería, conectada a una base de datos relacional en MySQL.

## Características principales
- **Autenticación y sesiones:** Inicio y cierre de sesión mediante `$_SESSION` (`login.php` y `logout.php`).
- **Control por roles:** Diferenciación entre usuarios `admin` y `empleado` (restringiendo la visualización del precio de compra según el rol).
- **Gestión de productos:** Registro de nuevos artículos e inventario con alertas visuales cuando el stock es menor o igual al stock mínimo (`productos.php`).
- **Consultas seguras:** Uso de `MySQLi` con consultas preparadas (`prepare` y `bind_param`).

## Tecnologías utilizadas
- **Backend / Lógica:** PHP con MySQLi
- **Frontend:** HTML5, CSS3 y Bootstrap 5
- **Base de datos:** MySQL (`stockcontrol_db` con tablas `usuarios`, `productos`, `ventas` y `detalle_ventas`)
- **Servidor local y editor:** XAMPP (Apache y MySQL) y Sublime Text
- **Control de versiones:** Git y GitHub

## Instalación y ejecución local
1. Clonar o copiar la carpeta `StockControl` dentro de `C:\xampp\htdocs\StockControl`.
2. Iniciar **Apache** y **MySQL** desde el panel de control de **XAMPP**.
3. Verificar que exista la base de datos `stockcontrol_db` con sus tablas (`usuarios`, `productos`, `ventas`, `detalle_ventas`) en phpMyAdmin.
4. Abrir en el navegador: `http://localhost/StockControl/login.php`.