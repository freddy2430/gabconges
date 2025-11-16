<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Manually load the AuthController to check roles
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../utils/security.php';
$authController = new AuthController();

// Define $activePage if not set
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'congésGAB'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column p-3">
                    <h5 class="text-white mb-4">
                        <i class="bi bi-person me-2"></i>
                        <?php echo $authController->isAdmin() ? 'Administration' : 'Mon Espace'; ?>
                    </h5>
                    <nav class="nav nav-pills flex-column">
                        <a href="index.php?action=home" class="nav-link <?php echo ($activePage === 'home') ? 'active' : ''; ?>">
                            <i class="bi bi-globe me-2"></i>Accueil
                        </a>
                        <hr class="text-white-50">
                        
                        <?php if ($authController->isAdmin()): ?>
                            <a href="index.php?action=admin_dashboard" class="nav-link <?php echo ($activePage === 'admin_dashboard') ? 'active' : ''; ?>">
                                <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                            </a>
                            <a href="index.php?action=users" class="nav-link <?php echo ($activePage === 'users') ? 'active' : ''; ?>">
                                <i class="bi bi-people me-2"></i>Utilisateurs
                            </a>
                            <a href="index.php?action=leave_types" class="nav-link <?php echo ($activePage === 'leave_types') ? 'active' : ''; ?>">
                                <i class="bi bi-bookmarks me-2"></i>Types de congés
                            </a>
                            <a href="index.php?action=leave_requests" class="nav-link <?php echo ($activePage === 'leave_requests') ? 'active' : ''; ?>">
                                <i class="bi bi-calendar-check me-2"></i>Toutes les demandes
                            </a>
                        <?php else: ?>
                            <a href="index.php?action=dashboard" class="nav-link <?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>">
                                <i class="bi bi-house me-2"></i>Tableau de bord
                            </a>
                            <a href="index.php?action=my_requests" class="nav-link <?php echo ($activePage === 'my_requests') ? 'active' : ''; ?>">
                                <i class="bi bi-calendar-check me-2"></i>Mes demandes
                            </a>
                            <a href="index.php?action=leave_request_create" class="nav-link <?php echo ($activePage === 'leave_request_create') ? 'active' : ''; ?>">
                                <i class="bi bi-plus-circle me-2"></i>Nouvelle demande
                            </a>
                        <?php endif; ?>
                        
                        <hr class="text-white-50">
                        <a href="index.php?action=logout" class="nav-link text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="container-fluid p-4">
