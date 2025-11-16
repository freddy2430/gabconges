<?php
/**
 * Contrôleur de gestion des types de congés
 * Application de congésGAB
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/LeaveType.php';

class LeaveTypeController {
    private $pdo;
    private $leaveTypeModel;
    private $authController;

    public function __construct() {
        $this->pdo = connectDB();
        $this->leaveTypeModel = new LeaveType($this->pdo);
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
     * Afficher la liste des types de congés
     */
    public function index() {
        $this->requireAdmin();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $leaveTypes = $this->leaveTypeModel->getAll($limit, $offset);
        $totalTypes = $this->leaveTypeModel->count();
        $totalPages = ceil($totalTypes / $limit);

        require_once __DIR__ . '/../views/leave_types/index.php';
    }

    /**
     * Afficher le formulaire de création de type de congé
     */
    public function showCreateForm() {
        $this->requireAdmin();
        require_once __DIR__ . '/../views/leave_types/create.php';
    }

    /**
     * Créer un nouveau type de congé
     */
    public function create() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showCreateForm();
            return;
        }

        // Récupération et nettoyage des données
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'max_days_per_year' => (int)($_POST['max_days_per_year'] ?? 0),
            'requires_approval' => isset($_POST['requires_approval']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Validation des données
        $errors = $this->validateLeaveTypeData($data, true);

        if (!empty($errors)) {
            $_SESSION['create_leave_type_errors'] = $errors;
            $_SESSION['create_leave_type_data'] = $data;
            header('Location: index.php?action=leave_type_create');
            exit;
        }

        // Création du type de congé
        if ($this->leaveTypeModel->create($data)) {
            $_SESSION['success'] = "Type de congé créé avec succès.";
            header('Location: index.php?action=leave_types');
            exit;
        } else {
            $_SESSION['create_leave_type_error'] = "Erreur lors de la création du type de congé.";
            $_SESSION['create_leave_type_data'] = $data;
            header('Location: index.php?action=leave_type_create');
            exit;
        }
    }

    /**
     * Afficher le formulaire d'édition de type de congé
     */
    public function edit($id) {
        $this->requireAdmin();

        if (!$id) {
            $_SESSION['error'] = "ID du type de congé manquant.";
            header('Location: index.php?action=leave_types');
            exit;
        }

        $leaveType = $this->leaveTypeModel->getById($id);

        if (!$leaveType) {
            $_SESSION['error'] = "Type de congé non trouvé.";
            header('Location: index.php?action=leave_types');
            exit;
        }

        // Récupérer les statistiques d'utilisation
        $stats = $this->leaveTypeModel->getUsageStats($id);

        require_once __DIR__ . '/../views/leave_types/edit.php';
    }

    /**
     * Mettre à jour un type de congé
     */
    public function update($id) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->edit($id);
            return;
        }

        if (!$id) {
            $_SESSION['error'] = "ID du type de congé manquant.";
            header('Location: index.php?action=leave_types');
            exit;
        }

        // Récupération et nettoyage des données
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'max_days_per_year' => (int)($_POST['max_days_per_year'] ?? 0),
            'requires_approval' => isset($_POST['requires_approval']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Validation des données
        $errors = $this->validateLeaveTypeData($data, false);

        if (!empty($errors)) {
            $_SESSION['edit_leave_type_errors'] = $errors;
            $_SESSION['edit_leave_type_data'] = $data;
            $_SESSION['edit_leave_type_id'] = $id;
            header("Location: index.php?action=leave_type_edit&id=$id");
            exit;
        }

        // Mise à jour du type de congé
        if ($this->leaveTypeModel->update($id, $data)) {
            $_SESSION['success'] = "Type de congé mis à jour avec succès.";
            header('Location: index.php?action=leave_types');
            exit;
        } else {
            $_SESSION['edit_leave_type_error'] = "Erreur lors de la mise à jour du type de congé.";
            $_SESSION['edit_leave_type_data'] = $data;
            $_SESSION['edit_leave_type_id'] = $id;
            header("Location: index.php?action=leave_type_edit&id=$id");
            exit;
        }
    }

    /**
     * Supprimer un type de congé
     */
    public function delete($id) {
        $this->requireAdmin();

        if (!$id) {
            $_SESSION['error'] = "ID du type de congé manquant.";
            header('Location: index.php?action=leave_types');
            exit;
        }

        $leaveType = $this->leaveTypeModel->getById($id);

        if (!$leaveType) {
            $_SESSION['error'] = "Type de congé non trouvé.";
            header('Location: index.php?action=leave_types');
            exit;
        }

        // Confirmation de suppression
        if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
            $_SESSION['delete_leave_type_name'] = $leaveType['name'];
            $_SESSION['delete_leave_type_id'] = $id;
            header("Location: index.php?action=leave_type_edit&id=$id&delete=confirm");
            exit;
        }

        // Suppression du type de congé
        if ($this->leaveTypeModel->delete($id)) {
            $_SESSION['success'] = "Type de congé supprimé avec succès.";
            header('Location: index.php?action=leave_types');
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression du type de congé. Il est peut-être utilisé dans des demandes existantes.";
            header('Location: index.php?action=leave_types');
            exit;
        }
    }

    /**
     * Basculer le statut actif/inactif d'un type de congé
     */
    public function toggleStatus($id) {
        $this->requireAdmin();

        if (!$id) {
            $_SESSION['error'] = "ID du type de congé manquant.";
            header('Location: index.php?action=leave_types');
            exit;
        }

        $leaveType = $this->leaveTypeModel->getById($id);

        if (!$leaveType) {
            $_SESSION['error'] = "Type de congé non trouvé.";
            header('Location: index.php?action=leave_types');
            exit;
        }

        if ($this->leaveTypeModel->toggleStatus($id)) {
            $statusText = $leaveType['is_active'] ? 'désactivé' : 'activé';
            $_SESSION['success'] = "Type de congé $statusText avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la modification du statut.";
        }

        header('Location: index.php?action=leave_types');
        exit;
    }

    /**
     * Valider les données de type de congé
     * @param array $data Données à valider
     * @param bool $isCreate True si création, false si modification
     * @return array Erreurs de validation
     */
    private function validateLeaveTypeData($data, $isCreate = true) {
        $errors = [];

        // Validation du nom
        if (empty($data['name'])) {
            $errors[] = "Le nom du type de congé est requis.";
        } elseif (strlen($data['name']) < 2) {
            $errors[] = "Le nom du type de congé doit contenir au moins 2 caractères.";
        } elseif (strlen($data['name']) > 50) {
            $errors[] = "Le nom du type de congé ne peut pas dépasser 50 caractères.";
        } elseif ($this->leaveTypeModel->nameExists($data['name'], $isCreate ? null : $_POST['leave_type_id'])) {
            $errors[] = "Ce nom de type de congé est déjà utilisé.";
        }

        // Validation de la description
        if (strlen($data['description']) > 255) {
            $errors[] = "La description ne peut pas dépasser 255 caractères.";
        }

        // Validation du nombre maximum de jours
        if ($data['max_days_per_year'] < 0) {
            $errors[] = "Le nombre maximum de jours doit être positif ou nul.";
        } elseif ($data['max_days_per_year'] > 365) {
            $errors[] = "Le nombre maximum de jours ne peut pas dépasser 365.";
        }

        return $errors;
    }
}