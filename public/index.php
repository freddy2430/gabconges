<?php

// FORCER L'AFFICHAGE DES ERREURS POUR LE DÉBOGAGE
define('ENVIRONMENT', 'production');

// Configuration sécurisée de la session (DOIT ÊTRE AVANT session_start())
session_set_cookie_params([
    'lifetime' => 3600, // 1 heure
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration des erreurs pour le développement
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ERROR | E_PARSE);
}

// Autoloader simple pour les classes
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/../controllers/' . $className . '.php',
        __DIR__ . '/../models/' . $className . '.php',
        __DIR__ . '/../config/' . $className . '.php'
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }
});

// Récupération de l'action demandée
$action = $_GET['action'] ?? 'home';

// Initialisation du contrôleur d'authentification
$authController = new AuthController();

// Vérification de l'authentification pour les pages protégées
$protectedActions = [
    'dashboard', 'admin_dashboard', 'logout', 'profile',
    'users', 'user_create', 'user_edit', 'user_delete',
    'leave_types', 'leave_type_create', 'leave_type_edit', 'leave_type_delete',
    'leave_requests', 'leave_request_create', 'leave_request_edit', 'leave_request_approve', 'leave_request_reject',
    'statistics', 'export'
];

if (in_array($action, $protectedActions) && !$authController->isLoggedIn()) {
    header('Location: index.php?action=login');
    exit;
}

// Routage vers les contrôleurs appropriés
try {
    switch ($action) {
        case 'home':
            require_once __DIR__ . '/../controllers/HomeController.php';
            $homeController = new HomeController();
            $homeController->index();
            break;

        // Actions d'authentification
        case 'login':
            $authController->login();
            break;

        case 'register':
            $authController->register();
            break;

        case 'logout':
            $authController->logout();
            break;

        // Actions utilisateur (à implémenter)
        case 'dashboard':
            // Tableau de bord employé
            require_once __DIR__ . '/../controllers/DashboardController.php';
            $dashboardController = new DashboardController();
            $dashboardController->showEmployeeDashboard();
            break;

        case 'admin_dashboard':
            // Tableau de bord administrateur
            require_once __DIR__ . '/../controllers/DashboardController.php';
            $dashboardController = new DashboardController();
            $dashboardController->showAdminDashboard();
            break;

        case 'users':
            // Gestion des utilisateurs
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->index();
            break;

        case 'user_create':
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->create();
            break;

        case 'user_edit':
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->edit($_GET['id'] ?? 0);
            break;

        case 'user_delete':
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->delete($_GET['id'] ?? 0);
            break;

        // Actions types de congés (à implémenter)
        case 'leave_types':
            require_once __DIR__ . '/../controllers/LeaveTypeController.php';
            $leaveTypeController = new LeaveTypeController();
            $leaveTypeController->index();
            break;

        case 'leave_type_create':
            require_once __DIR__ . '/../controllers/LeaveTypeController.php';
            $leaveTypeController = new LeaveTypeController();
            $leaveTypeController->create();
            break;

        case 'leave_type_edit':
            require_once __DIR__ . '/../controllers/LeaveTypeController.php';
            $leaveTypeController = new LeaveTypeController();
            $leaveTypeController->edit($_GET['id'] ?? 0);
            break;

        case 'leave_type_delete':
            require_once __DIR__ . '/../controllers/LeaveTypeController.php';
            $leaveTypeController = new LeaveTypeController();
            $leaveTypeController->delete($_GET['id'] ?? 0);
            break;

        // Actions demandes de congés (à implémenter)
        case 'leave_requests':
            require_once __DIR__ . '/../controllers/LeaveRequestController.php';
            $leaveRequestController = new LeaveRequestController();
            $leaveRequestController->index();
            break;

        case 'leave_request_create':
            require_once __DIR__ . '/../controllers/LeaveRequestController.php';
            $leaveRequestController = new LeaveRequestController();
            $leaveRequestController->create();
            break;

        case 'leave_request_edit':
            require_once __DIR__ . '/../controllers/LeaveRequestController.php';
            $leaveRequestController = new LeaveRequestController();
            $leaveRequestController->edit($_GET['id'] ?? 0);
            break;

        case 'leave_request_approve':
            require_once __DIR__ . '/../controllers/LeaveRequestController.php';
            $leaveRequestController = new LeaveRequestController();
            $leaveRequestController->approve($_GET['id'] ?? 0);
            break;

        case 'leave_request_reject':
            require_once __DIR__ . '/../controllers/LeaveRequestController.php';
            $leaveRequestController = new LeaveRequestController();
            $leaveRequestController->reject($_GET['id'] ?? 0);
            break;

        // Actions statistiques et export (à implémenter)
        case 'statistics':
            require_once __DIR__ . '/../controllers/StatisticsController.php';
            $statisticsController = new StatisticsController();
            $statisticsController->index();
            break;

        case 'export':
            require_once __DIR__ . '/../controllers/ExportController.php';
            $exportController = new ExportController();
            $exportController->export($_GET['format'] ?? 'csv', $_GET['type'] ?? 'all');
            break;

        // Action par défaut - page non trouvée ou redirection
        default:
            // On peut soit afficher une 404, soit rediriger vers l'accueil
            header('Location: index.php?action=home');
            exit;
    }

} catch (Exception $e) {
    // Gestion des erreurs
    error_log("Erreur dans l'application : " . $e->getMessage());

    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        die("Erreur : " . $e->getMessage());
    } else {
        // En production, afficher une page d'erreur générique
        require_once __DIR__ . '/../views/errors/500.php';
    }
}