<?php
/**
 * Contrôleur d'authentification
 * Application de congésGAB
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $pdo;
    private $userModel;

    public function __construct() {
        $this->pdo = connectDB();
        $this->userModel = new User($this->pdo);
    }

    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm() {
        // Rediriger si déjà connecté
        if ($this->isLoggedIn()) {
            $this->redirectBasedOnRole();
            return;
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Traiter la connexion
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLoginForm();
            return;
        }

        // Récupération et nettoyage des données
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validation des données
        $errors = [];

        if (empty($username)) {
            $errors[] = "Le nom d'utilisateur est requis.";
        }

        if (empty($password)) {
            $errors[] = "Le mot de passe est requis.";
        }

        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_username'] = $username;
            header('Location: index.php?action=login');
            exit;
        }

        // Tentative d'authentification
        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            // Authentification réussie
            $this->setUserSession($user, $remember);

            // Redirection selon le rôle
            $this->redirectBasedOnRole();
        } else {
            // Échec de l'authentification
            $_SESSION['login_error'] = "Nom d'utilisateur ou mot de passe incorrect.";
            $_SESSION['login_username'] = $username;
            header('Location: index.php?action=login');
            exit;
        }
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegisterForm() {
        // Rediriger si déjà connecté
        if ($this->isLoggedIn()) {
            $this->redirectBasedOnRole();
            return;
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Traiter l'inscription
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegisterForm();
            return;
        }

        // Récupération et nettoyage des données
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? '')
        ];

        // Validation des données
        $errors = $this->validateRegistration($data);

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_data'] = $data;
            header('Location: index.php?action=register');
            exit;
        }

        // Préparation des données pour la création
        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => 'employee', // Rôle par défaut
            'is_active' => 1
        ];

        // Création de l'utilisateur
        if ($this->userModel->create($userData)) {
            $_SESSION['register_success'] = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";
            header('Location: index.php?action=login');
            exit;
        } else {
            $_SESSION['register_error'] = "Erreur lors de la création du compte. Veuillez réessayer.";
            $_SESSION['register_data'] = $data;
            header('Location: index.php?action=register');
            exit;
        }
    }

    /**
     * Déconnexion
     */
    public function logout() {
        // Démarrer la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destruction de la session
        $_SESSION = [];
        session_destroy();

        // Suppression du cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Redirection vers la page d'accueil
        header('Location: index.php?action=home');
        exit;
    }

    /**
     * Vérifier si l'utilisateur est connecté
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['username']);
    }

    /**
     * Vérifier si l'utilisateur actuel est un administrateur
     * @return bool
     */
    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Récupérer l'utilisateur actuel
     * @return array|null Informations utilisateur ou null
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['user_email'],
            'first_name' => $_SESSION['user_first_name'],
            'last_name' => $_SESSION['user_last_name'],
            'role' => $_SESSION['user_role']
        ];
    }

    /**
     * Valider les données d'inscription
     * @param array $data Données à valider
     * @return array Erreurs de validation
     */
    private function validateRegistration($data) {
        $errors = [];

        // Validation du nom d'utilisateur
        if (empty($data['username'])) {
            $errors[] = "Le nom d'utilisateur est requis.";
        } elseif (strlen($data['username']) < 3) {
            $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores.";
        } elseif ($this->userModel->usernameExists($data['username'])) {
            $errors[] = "Ce nom d'utilisateur est déjà utilisé.";
        }

        // Validation de l'email
        if (empty($data['email'])) {
            $errors[] = "L'email est requis.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        } elseif ($this->userModel->emailExists($data['email'])) {
            $errors[] = "Cet email est déjà utilisé.";
        }

        // Validation du mot de passe
        if (empty($data['password'])) {
            $errors[] = "Le mot de passe est requis.";
        } elseif (strlen($data['password']) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $data['password'])) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre.";
        }

        // Validation de la confirmation du mot de passe
        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        // Validation du prénom
        if (empty($data['first_name'])) {
            $errors[] = "Le prénom est requis.";
        } elseif (strlen($data['first_name']) < 2) {
            $errors[] = "Le prénom doit contenir au moins 2 caractères.";
        }

        // Validation du nom de famille
        if (empty($data['last_name'])) {
            $errors[] = "Le nom de famille est requis.";
        } elseif (strlen($data['last_name']) < 2) {
            $errors[] = "Le nom de famille doit contenir au moins 2 caractères.";
        }

        return $errors;
    }

    /**
     * Définir les variables de session utilisateur
     * @param array $user Informations utilisateur
     * @param bool $remember Se souvenir de l'utilisateur
     */
    private function setUserSession($user, $remember = false) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_first_name'] = $user['first_name'];
        $_SESSION['user_last_name'] = $user['last_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();

        // Session plus longue si "se souvenir de moi"
        // La gestion de la durée de vie de la session est maintenant centralisée dans public/index.php
        // Pour une fonctionnalité "se souvenir de moi" persistante, une approche différente (avec des tokens de connexion) serait nécessaire.
    }

    /**
     * Rediriger selon le rôle de l'utilisateur
     */
    private function redirectBasedOnRole() {
        if ($this->isAdmin()) {
            header('Location: index.php?action=admin_dashboard');
        } else {
            header('Location: index.php?action=dashboard');
        }
        exit;
    }
}