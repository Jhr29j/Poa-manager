<?php
if (!isset($_SESSION['usuario'])) {
    header("Location: /poa-manager/views/login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
$user = $_SESSION['usuario'];

// Generar iniciales
$iniciales = strtoupper(
    substr($user['primer_nombre'], 0, 1) . 
    substr($user['primer_apellido'] ?? '', 0, 1)
);

$rol = ucfirst($user['rol'] ?? 'lector');
?>

<aside class="sidebar">
    <a class="logo" href="/poa-manager/views/index.php">
        <i class="fas fa-chart-line"></i>
        <span>POA Manager</span>
    </a>

    <nav class="nav-menu">
        <ul>
            <li class="<?= ($current_page == 'inicio.php') ? 'active' : '' ?>">
                <a href="../inicio.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
            </li>
            <li class="<?= ($current_page == 'planes.php') ? 'active' : '' ?>">
                <a href="../planes.php"><i class="fas fa-calendar-alt"></i><span>Planes</span></a>
            </li>

            <li class="<?= ($current_page == 'todas_actividades.php') ? 'active' : '' ?>">
                <a href="../views/todas_actividades.php"><i class="fas fa-tasks"></i><span>Actividades</span></a>
            </li>
            
            <?php if (($user['rol'] ?? '') === 'administrador'): ?>
            <li class="<?= ($current_page == 'usuarios.php') ? 'active' : '' ?>">
                <a href="../usuarios.php"><i class="fas fa-users"></i><span>Usuarios</span></a>
            </li>
            <?php endif; ?>

        </ul>
    </nav>
    
    <div class="user-profile">
        <div class="avatar"><?= $iniciales ?></div>
        <div class="user-info">
            <span class="username"><?= htmlspecialchars($user['primer_nombre'] . ' ' . ($user['primer_apellido'] ?? '')) ?></span>
            <span class="role"><?= $rol ?></span>
        </div>
        <a href="/poa-manager/index.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</aside>
