<?php
/**
 * Contrôleur de gestion des demandes de congés
 * Application de congésGAB
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/LeaveType.php';

class LeaveRequestController {
    private $pdo;
    private $leaveRequestModel;
    private $leaveTypeModel;
    private $authController;

    public function __construct() {
        $this->pdo = connectDB();
        $this->leaveRequestModel = new LeaveRequest($this->pdo);
        $this->leaveTypeModel = new LeaveType($this->pdo);
        $this->authController = new AuthController();
    }

    /**
     * Afficher la liste des demandes de congés (Admin)
     */
    public function index() {
        $this->requireAdmin();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? null;

        $leaveRequests = $this->leaveRequestModel->getAll($limit, $offset, $status);
        $totalRequests = $this->leaveRequestModel->count($status);
        $totalPages = ceil($totalRequests / $limit);

        require_once __DIR__ . '/../views/leave_requests/index.php';
    }

    /**
     * Afficher la liste des demandes de l'utilisateur connecté
     */
    public function myRequests() {
        $currentUser = $this->authController->getCurrentUser();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $leaveRequests = $this->leaveRequestModel->getByUserId($currentUser['id'], $limit, $offset);
        $totalRequests = count($this->leaveRequestModel->getByUserId($currentUser['id']));
        $totalPages = ceil($totalRequests / $limit);

        require_once __DIR__ . '/../views/leave_requests/my_requests.php';
    }

    /**
     * Afficher le formulaire de création de demande de congé
     */
    public function showCreateForm() {
        $currentUser = $this->authController->getCurrentUser();
        $leaveTypes = $this->leaveTypeModel->getAllActive();

        require_once __DIR__ . '/../views/leave_requests/create.php';
    }

    /**
     * Créer une nouvelle demande de congé
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showCreateForm();
            return;
        }

        $currentUser = $this->authController->getCurrentUser();

        // Récupération et nettoyage des données
        $data = [
            'user_id' => $currentUser['id'],
            'leave_type_id' => (int)($_POST['leave_type_id'] ?? 0),
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'reason' => trim($_POST['reason'] ?? ''),
            'status' => 'pending',
            'requested_days' => 0
        ];

        // Validation des données
        $errors = $this->validateLeaveRequestData($data);

        if (!empty($errors)) {
            $_SESSION['create_request_errors'] = $errors;
            $_SESSION['create_request_data'] = $data;
            header('Location: index.php?action=leave_request_create');
            exit;
        }

        // Calcul des jours ouvrés
        $data['requested_days'] = calculateWorkingDays($data['start_date'], $data['end_date']);

        // Vérification des chevauchements
        if ($this->leaveRequestModel->checkOverlap($currentUser['id'], $data['start_date'], $data['end_date'])) {
            $_SESSION['create_request_errors'] = ["Vous avez déjà un congé approuvé sur cette période."];
            $_SESSION['create_request_data'] = $data;
            header('Location: index.php?action=leave_request_create');
            exit;
        }

        // Création de la demande
        if ($this->leaveRequestModel->create($data)) {
            // Envoi de l'e-mail de notification à l'administrateur
            
            // 1. Charger les fichiers PHPMailer manuellement
            require_once __DIR__ . '/../libs/PHPMailer.php';
            require_once __DIR__ . '/../libs/SMTP.php';
            require_once __DIR__ . '/../libs/Exception.php';

            // 2. Utiliser les namespaces
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {
                // 3. Configuration
                $mailConfig = require __DIR__ . '/../config/mail.php';

                // Activer le debug pour voir les erreurs
                $mail->SMTPDebug = 2; // 0 pour désactiver, 2 pour voir le dialogue client-serveur
                $mail->Debugoutput = 'error_log'; // Envoie le log dans le fichier d'erreur de PHP

                $mail->isSMTP();
                $mail->Host       = $mailConfig['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $mailConfig['username'];
                $mail->Password   = $mailConfig['password'];
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = $mailConfig['port'];
                $mail->CharSet    = 'UTF-8';

                // 4. Destinataires
                $mail->setFrom($mailConfig['from_address'], $mailConfig['from_name']);
                $mail->addAddress('admin@example.com', 'Admin'); // IMPORTANT : Remplacer par l'email de l'admin

                // 5. Contenu de l'email
                $mail->isHTML(true);
                $mail->Subject = 'Nouvelle demande de congé : ' . $currentUser['first_name'] . ' ' . $currentUser['last_name'];
                $mail->Body    = "Bonjour,<br><br>Une nouvelle demande de congé a été soumise par <b>" . $currentUser['first_name'] . " " . $currentUser['last_name'] . "</b>.<br>" . 
                                 "<ul>" . 
                                 "<li><b>Période :</b> du " . $data['start_date'] . " au " . $data['end_date'] . "</li>" . 
                                 "<li><b>Motif :</b> " . $data['reason'] . "</li>" . 
                                 "</ul>" . 
                                 "<p>Veuillez vous connecter à l'application pour la traiter.</p>";
                $mail->AltBody = "Bonjour,\n\nUne nouvelle demande de congé a été soumise par " . $currentUser['first_name'] . " " . $currentUser['last_name'] . ".\n" . 
                                 "Période : du " . $data['start_date'] . " au " . $data['end_date'] . ".\n" . 
                                 "Motif : " . $data['reason'] . "\n\n" . 
                                 "Veuillez vous connecter à l'application pour la traiter.";

                $mail->send();

            } catch (Exception $e) {
                // Ne pas bloquer l'utilisateur si l'email échoue, mais logger l'erreur
                // pour le développeur.
                error_log("L'email de notification n'a pas pu être envoyé. Erreur: " . $mail->ErrorInfo);
            }

            $_SESSION['success'] = "Demande de congé créée avec succès. Elle est en attente d'approbation.";
            header('Location: index.php?action=my_requests');
            exit;
        } else {
            $_SESSION['create_request_error'] = "Erreur lors de la création de la demande.";
            $_SESSION['create_request_data'] = $data;
            header('Location: index.php?action=leave_request_create');
            exit;
        }
    }

    /**
     * Afficher le formulaire d'édition de demande de congé
     */
    public function edit($id) {
        if (!$id) {
            $_SESSION['error'] = "ID de la demande manquant.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        $currentUser = $this->authController->getCurrentUser();
        $request = $this->leaveRequestModel->getById($id);

        if (!$request) {
            $_SESSION['error'] = "Demande non trouvée.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        // Vérifier que l'utilisateur est propriétaire de la demande ou admin
        if ($request['user_id'] !== $currentUser['id'] && !$this->authController->isAdmin()) {
            $_SESSION['error'] = "Accès refusé.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        // Ne permettre l'édition que des demandes en attente
        if ($request['status'] !== 'pending') {
            $_SESSION['error'] = "Seules les demandes en attente peuvent être modifiées.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        $leaveTypes = $this->leaveTypeModel->getAllActive();

        require_once __DIR__ . '/../views/leave_requests/edit.php';
    }

    /**
     * Mettre à jour une demande de congé
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->edit($id);
            return;
        }

        if (!$id) {
            $_SESSION['error'] = "ID de la demande manquant.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        $currentUser = $this->authController->getCurrentUser();
        $request = $this->leaveRequestModel->getById($id);

        if (!$request) {
            $_SESSION['error'] = "Demande non trouvée.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        // Vérifier que l'utilisateur est propriétaire de la demande ou admin
        if ($request['user_id'] !== $currentUser['id'] && !$this->authController->isAdmin()) {
            $_SESSION['error'] = "Accès refusé.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        // Récupération et nettoyage des données
        $data = [
            'leave_type_id' => (int)($_POST['leave_type_id'] ?? 0),
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'reason' => trim($_POST['reason'] ?? ''),
            'requested_days' => 0
        ];

        // Validation des données
        $errors = $this->validateLeaveRequestData($data);

        if (!empty($errors)) {
            $_SESSION['edit_request_errors'] = $errors;
            $_SESSION['edit_request_data'] = $data;
            $_SESSION['edit_request_id'] = $id;
            header("Location: index.php?action=leave_request_edit&id=$id");
            exit;
        }

        // Calcul des jours ouvrés
        $data['requested_days'] = calculateWorkingDays($data['start_date'], $data['end_date']);

        // Vérification des chevauchements (en excluant la demande actuelle)
        if ($this->leaveRequestModel->checkOverlap($currentUser['id'], $data['start_date'], $data['end_date'], $id)) {
            $_SESSION['edit_request_errors'] = ["Vous avez déjà un congé approuvé sur cette période."];
            $_SESSION['edit_request_data'] = $data;
            $_SESSION['edit_request_id'] = $id;
            header("Location: index.php?action=leave_request_edit&id=$id");
            exit;
        }

        // Mise à jour de la demande
        if ($this->leaveRequestModel->update($id, $data)) {
            $_SESSION['success'] = "Demande de congé mise à jour avec succès.";
            header('Location: index.php?action=my_requests');
            exit;
        } else {
            $_SESSION['edit_request_error'] = "Erreur lors de la mise à jour de la demande.";
            $_SESSION['edit_request_data'] = $data;
            $_SESSION['edit_request_id'] = $id;
            header("Location: index.php?action=leave_request_edit&id=$id");
            exit;
        }
    }

    /**
     * Approuver une demande de congé (Admin uniquement)
     */
    public function approve($id) {
        $this->requireAdmin();

        if (!$id) {
            $_SESSION['error'] = "ID de la demande manquant.";
            header('Location: index.php?action=leave_requests');
            exit;
        }

        $request = $this->leaveRequestModel->getById($id);

        if (!$request) {
            $_SESSION['error'] = "Demande non trouvée.";
            header('Location: index.php?action=leave_requests');
            exit;
        }

        $currentUser = $this->authController->getCurrentUser();

        if ($this->leaveRequestModel->approve($id, $currentUser['id'])) {
            $_SESSION['success'] = "Demande approuvée avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de l'approbation de la demande.";
        }

        header('Location: index.php?action=leave_requests');
        exit;
    }

    /**
     * Rejeter une demande de congé (Admin uniquement)
     */
    public function reject($id) {
        $this->requireAdmin();

        if (!$id) {
            $_SESSION['error'] = "ID de la demande manquant.";
            header('Location: index.php?action=leave_requests');
            exit;
        }

        $request = $this->leaveRequestModel->getById($id);

        if (!$request) {
            $_SESSION['error'] = "Demande non trouvée.";
            header('Location: index.php?action=leave_requests');
            exit;
        }

        $reason = trim($_POST['rejection_reason'] ?? '');
        $currentUser = $this->authController->getCurrentUser();

        if ($this->leaveRequestModel->reject($id, $currentUser['id'], $reason)) {
            $_SESSION['success'] = "Demande rejetée avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors du rejet de la demande.";
        }

        header('Location: index.php?action=leave_requests');
        exit;
    }

    /**
     * Supprimer une demande de congé
     */
    public function delete($id) {
        if (!$id) {
            $_SESSION['error'] = "ID de la demande manquant.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        $currentUser = $this->authController->getCurrentUser();
        $request = $this->leaveRequestModel->getById($id);

        if (!$request) {
            $_SESSION['error'] = "Demande non trouvée.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        // Vérifier que l'utilisateur est propriétaire de la demande ou admin
        if ($request['user_id'] !== $currentUser['id'] && !$this->authController->isAdmin()) {
            $_SESSION['error'] = "Accès refusé.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        // Ne permettre la suppression que des demandes en attente ou rejetées
        if ($request['status'] === 'approved') {
            $_SESSION['error'] = "Les demandes approuvées ne peuvent pas être supprimées.";
            header('Location: index.php?action=my_requests');
            exit;
        }

        if ($this->leaveRequestModel->delete($id)) {
            $_SESSION['success'] = "Demande supprimée avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression de la demande.";
        }

        header('Location: index.php?action=my_requests');
        exit;
    }

    /**
     * Afficher les détails d'une demande (Admin)
     */
    public function show($id) {
        $this->requireAdmin();

        if (!$id) {
            $_SESSION['error'] = "ID de la demande manquant.";
            header('Location: index.php?action=leave_requests');
            exit;
        }

        $request = $this->leaveRequestModel->getById($id);

        if (!$request) {
            $_SESSION['error'] = "Demande non trouvée.";
            header('Location: index.php?action=leave_requests');
            exit;
        }

        require_once __DIR__ . '/../views/leave_requests/show.php';
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
     * Valider les données de demande de congé
     * @param array $data Données à valider
     * @return array Erreurs de validation
     */
    private function validateLeaveRequestData($data) {
        $errors = [];

        // Validation du type de congé
        if (empty($data['leave_type_id'])) {
            $errors[] = "Le type de congé est requis.";
        }

        // Validation des dates
        if (empty($data['start_date'])) {
            $errors[] = "La date de début est requise.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['start_date'])) {
            $errors[] = "Format de date de début invalide.";
        } elseif (new DateTime($data['start_date']) < new DateTime('today')) {
            $errors[] = "La date de début ne peut pas être dans le passé.";
        }

        if (empty($data['end_date'])) {
            $errors[] = "La date de fin est requise.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['end_date'])) {
            $errors[] = "Format de date de fin invalide.";
        } elseif (new DateTime($data['end_date']) < new DateTime($data['start_date'])) {
            $errors[] = "La date de fin doit être après la date de début.";
        }

        // Validation du motif
        if (empty($data['reason'])) {
            $errors[] = "Le motif est requis.";
        } elseif (strlen($data['reason']) < 10) {
            $errors[] = "Le motif doit contenir au moins 10 caractères.";
        } elseif (strlen($data['reason']) > 500) {
            $errors[] = "Le motif ne peut pas dépasser 500 caractères.";
        }

        return $errors;
    }
}