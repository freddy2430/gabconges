<?php
/**
 * Contrôleur de gestion des utilisateurs
 * Application de congésGAB
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $pdo;
    private $userModel;
    private $authController;

    public function __construct() {
        $this->pdo = connectDB();
        $this->userModel = new User($this->pdo);
        $this->authController = new AuthController();
    }

    /**
     * Vérifier si l'utilisateur actuel est administrateur
     */
    private function requireAdmin() {
        if (!$this->authController->isAdmin()) {
            $_SESSION['error'] = "Accès refusé. Droits d'administrateur requis.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function index() {
        $this->requireAdmin();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $users = $this->userModel->getAll($limit, $offset);
        $totalUsers = $this->userModel->count();
        $totalPages = ceil($totalUsers / $limit);

        require_once __DIR__ . '/../views/users/index.php';
    }

    /**
     * Afficher le formulaire de création d'utilisateur
     */
    public function showCreateForm() {
        $this->requireAdmin();
        require_once __DIR__ . '/../views/users/create.php';
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function create() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showCreateForm();
            return;
        }

        // Récupération et nettoyage des données
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'role' => $_POST['role'] ?? 'employee',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Validation des données
        $errors = $this->validateUserData($data, true);

        if (!empty($errors)) {
            $_SESSION['create_user_errors'] = $errors;
            $_SESSION['create_user_data'] = $data;
            header('Location: index.php?action=user_create');
            exit;
        }

        // Préparation des données pour la création
        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => $data['role'],
            'is_active' => $data['is_active']
        ];

        // Création de l'utilisateur
        if ($this->userModel->create($userData)) {
            $_SESSION['success'] = "Utilisateur créé avec succès.";
            header('Location: index.php?action=users');
            exit;
        } else {
            $_SESSION['create_user_error'] = "Erreur lors de la création de l'utilisateur.";
            $_SESSION['create_user_data'] = $data;
            header('Location: index.php?action=user_create');
            exit;
        }
    }

    /**
     * Afficher le formulaire d'édition d'utilisateur
     */
    public function edit($id) {
        $this->requireAdmin();

        if (!$id) {
            $_SESSION['error'] = "ID utilisateur manquant.";
            header('Location: index.php?action=users');
            exit;
        }

        $user = $this->userModel->getById($id);

        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            header('Location: index.php?action=users');
            exit;
        }

        require_once __DIR__ . '/../views/users/edit.php';
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update($id) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->edit($id);
            return;
        }

        if (!$id) {
            $_SESSION['error'] = "ID utilisateur manquant.";
            header('Location: index.php?action=users');
            exit;
        }

        // Récupération et nettoyage des données
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'role' => $_POST['role'] ?? 'employee',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Validation des données
        $errors = $this->validateUserData($data, false);

        if (!empty($errors)) {
            $_SESSION['edit_user_errors'] = $errors;
            $_SESSION['edit_user_data'] = $data;
            $_SESSION['edit_user_id'] = $id;
            header("Location: index.php?action=user_edit&id=$id");
            exit;
        }

        // Mise à jour de l'utilisateur
        if ($this->userModel->update($id, $data)) {
            $_SESSION['success'] = "Utilisateur mis à jour avec succès.";
            header('Location: index.php?action=users');
            exit;
        } else {
            $_SESSION['edit_user_error'] = "Erreur lors de la mise à jour de l'utilisateur.";
            $_SESSION['edit_user_data'] = $data;
            $_SESSION['edit_user_id'] = $id;
            header("Location: index.php?action=user_edit&id=$id");
            exit;
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function delete($id) {
        $this->requireAdmin();

        if (!$id) {
            $_SESSION['error'] = "ID utilisateur manquant.";
            header('Location: index.php?action=users');
            exit;
        }

        // Empêcher la suppression de son propre compte
        $currentUser = $this->authController->getCurrentUser();
        if ($currentUser['id'] == $id) {
            $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            header('Location: index.php?action=users');
            exit;
        }

        $user = $this->userModel->getById($id);

        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            header('Location: index.php?action=users');
            exit;
        }

        // Confirmation de suppression
        if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
            $_SESSION['delete_user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['delete_user_id'] = $id;
            header("Location: index.php?action=user_edit&id=$id&delete=confirm");
            exit;
        }

        // Suppression de l'utilisateur
        if ($this->userModel->delete($id)) {
            $_SESSION['success'] = "Utilisateur supprimé avec succès.";
            header('Location: index.php?action=users');
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur.";
            header('Location: index.php?action=users');
            exit;
        }
    }

    /**
     * Basculer le statut actif/inactif d'un utilisateur
     */
    public function toggleStatus($id) {
        $this->requireAdmin();

        if (!$id) {
            $_SESSION['error'] = "ID utilisateur manquant.";
            header('Location: index.php?action=users');
            exit;
        }

        // Empêcher la désactivation de son propre compte
        $currentUser = $this->authController->getCurrentUser();
        if ($currentUser['id'] == $id) {
            $_SESSION['error'] = "Vous ne pouvez pas désactiver votre propre compte.";
            header('Location: index.php?action=users');
            exit;
        }

        $user = $this->userModel->getById($id);

        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            header('Location: index.php?action=users');
            exit;
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $data = [
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role'],
            'is_active' => $newStatus
        ];

        if ($this->userModel->update($id, $data)) {
            $statusText = $newStatus ? 'activé' : 'désactivé';
            $_SESSION['success'] = "Utilisateur $statusText avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la modification du statut.";
        }

        header('Location: index.php?action=users');
        exit;
    }

    /**
     * Valider les données utilisateur
     * @param array $data Données à valider
     * @param bool $isCreate True si création, false si modification
     * @return array Erreurs de validation
     */
    private function validateUserData($data, $isCreate = true) {
        $errors = [];

        // Validation du nom d'utilisateur
        if (empty($data['username'])) {
            $errors[] = "Le nom d'utilisateur est requis.";
        } elseif (strlen($data['username']) < 3) {
            $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores.";
        } elseif ($this->userModel->usernameExists($data['username'], $isCreate ? null : $_POST['user_id'])) {
            $errors[] = "Ce nom d'utilisateur est déjà utilisé.";
        }

        // Validation de l'email
        if (empty($data['email'])) {
            $errors[] = "L'email est requis.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        } elseif ($this->userModel->emailExists($data['email'], $isCreate ? null : $_POST['user_id'])) {
            $errors[] = "Cet email est déjà utilisé.";
        }

        // Validation du mot de passe (uniquement pour la création)
        if ($isCreate) {
            if (empty($data['password'])) {
                $errors[] = "Le mot de passe est requis.";
            } elseif (strlen($data['password']) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $data['password'])) {
                $errors[] = "Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre.";
            }

            if ($data['password'] !== $data['confirm_password']) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }
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

        // Validation du rôle
        if (!in_array($data['role'], ['admin', 'employee'])) {
            $errors[] = "Rôle invalide.";
        }

        return $errors;
    }
}