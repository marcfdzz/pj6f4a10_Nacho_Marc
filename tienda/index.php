<?php
// tienda/index.php
require_once 'config.php';
require_once 'utilidades.php';
require_once RUTA_CLASES . '/Producto.php';

use Clases\Producto;

// Obtener productos para mostrar en la home
$productos = Producto::obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Deportes - Inicio</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>

    <!-- Header / Navegación -->
    <header>
        <div class="container header-content">
            <div class="logo">
                <h1>SPORT<span>VIBE</span></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php" class="active">Inicio</a></li>
                    <li><a href="compra/catalogo.php">Catálogo</a></li>
                    <?php if(isset($_SESSION['usuario'])): ?>
                        <li><a href="compra/area_clientes/">Mi Cuenta</a></li>
                        <li><a href="logout.php" class="btn-outline">Salir</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn-login">Iniciar Sesión</a></li>
                    <?php endif; ?>
                    <li>
                        <a href="compra/carrito.php" class="cart-icon">
                            🛒 <span class="badge">0</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h2>Tu rendimiento, <br>nuestra pasión.</h2>
            <p>Equipamiento deportivo de alta calidad para atletas exigentes.</p>
            <a href="compra/catalogo.php" class="btn btn-primary">Ver Catálogo</a>
        </div>
    </section>

    <!-- Productos Destacados -->
    <main class="container">
        <h3 class="section-title">Novedades</h3>
        <div class="grid-productos">
            <?php foreach($productos as $p): ?>
            <div class="card-producto">
                <div class="img-container">
                    <!-- Placeholder si no hay imagen real -->
                    <div class="img-placeholder"><?php echo strtoupper(substr($p->getNombre(), 0, 2)); ?></div>
                </div>
                <div class="info-producto">
                    <h4><?php echo htmlspecialchars($p->getNombre()); ?></h4>
                    <p class="precio"><?php echo number_format($p->getPrecio(), 2); ?> €</p>
                    <a href="compra/carrito.php?add=<?php echo $p->getId(); ?>" class="btn btn-sm">Añadir</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> SportVibe. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>
