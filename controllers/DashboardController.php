<?php
/**
 * Contrôleur des tableaux de bord
 * Application de congésGAB
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/LeaveType.php';
require_once __DIR__ . '/../models/User.php';

class DashboardController {
    private $pdo;
    private $leaveRequestModel;
    private $leaveTypeModel;
    private $userModel;
    private $authController;

    public function __construct() {
        $this->pdo = connectDB();
        $this->leaveRequestModel = new LeaveRequest($this->pdo);
        $this->leaveTypeModel = new LeaveType($this->pdo);
        $this->userModel = new User($this->pdo);
        $this->authController = new AuthController();
    }

    /**
     * Afficher le tableau de bord employé
     */
    public function showEmployeeDashboard() {
        $currentUser = $this->authController->getCurrentUser();

        // Récupérer les statistiques de l'utilisateur
        $stats = $this->leaveRequestModel->getStats($currentUser['id']);

        // Récupérer les demandes récentes
        $recentRequests = $this->leaveRequestModel->getByUserId($currentUser['id'], 5);

        // Récupérer les types de congés disponibles
        $leaveTypes = $this->leaveTypeModel->getAllActive();

        // Calculer les jours de congés restants par type
        $remainingDays = $this->calculateRemainingDays($currentUser['id']);

        require_once __DIR__ . '/../views/dashboard/employee.php';
    }

    /**
     * Afficher le tableau de bord administrateur
     */
    public function showAdminDashboard() {
        $this->requireAdmin();

        // Statistiques générales
        $generalStats = $this->leaveRequestModel->getStats();

        // Demandes en attente
        $pendingRequests = $this->leaveRequestModel->getPendingRequests(5);

        // Demandes récentes
        $recentRequests = $this->leaveRequestModel->getRecentRequests(10);

        // Statistiques par type de congé
        $leaveTypeStats = $this->getLeaveTypeStats();

        // Statistiques par utilisateur
        $userStats = $this->getUserStats();

        require_once __DIR__ . '/../views/dashboard/admin.php';
    }

    /**
     * Calculer les jours de congés restants pour un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Jours restants par type de congé
     */
    private function calculateRemainingDays($userId) {
        $remainingDays = [];

        try {
            $leaveTypes = $this->leaveTypeModel->getAllActive();

            foreach ($leaveTypes as $type) {
                if ($type['max_days_per_year'] > 0) {
                    // Compter les jours déjà pris pour ce type cette année
                    $sql = "SELECT SUM(requested_days) as used_days
                            FROM leave_requests
                            WHERE user_id = :user_id
                            AND leave_type_id = :leave_type_id
                            AND status = 'approved'
                            AND YEAR(start_date) = YEAR(CURRENT_DATE())";

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindParam(':user_id', $userId);
                    $stmt->bindParam(':leave_type_id', $type['id']);
                    $stmt->execute();

                    $result = $stmt->fetch();
                    $usedDays = (int) $result['used_days'];
                    $remainingDays[$type['id']] = [
                        'type_name' => $type['name'],
                        'max_days' => $type['max_days_per_year'],
                        'used_days' => $usedDays,
                        'remaining_days' => $type['max_days_per_year'] - $usedDays
                    ];
                }
            }

        } catch(PDOException $e) {
            error_log("Erreur calcul jours restants : " . $e->getMessage());
        }

        return $remainingDays;
    }

    /**
     * Récupérer les statistiques par type de congé
     * @return array Statistiques par type
     */
    private function getLeaveTypeStats() {
        $stats = [];

        try {
            $sql = "SELECT lt.name,
                          COUNT(lr.id) as total_requests,
                          SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                          SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                          SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                          SUM(lr.requested_days) as total_days
                   FROM leave_types lt
                   LEFT JOIN leave_requests lr ON lt.id = lr.leave_type_id
                   GROUP BY lt.id, lt.name
                   ORDER BY total_requests DESC";

            $stmt = $this->pdo->query($sql);
            $stats = $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur statistiques types de congés : " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Récupérer les statistiques par utilisateur
     * @return array Statistiques par utilisateur
     */
    private function getUserStats() {
        $stats = [];

        try {
            $sql = "SELECT u.first_name, u.last_name, u.username,
                          COUNT(lr.id) as total_requests,
                          SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                          SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                          SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                          SUM(lr.requested_days) as total_days
                   FROM users u
                   LEFT JOIN leave_requests lr ON u.id = lr.user_id
                   WHERE u.is_active = 1
                   GROUP BY u.id, u.first_name, u.last_name, u.username
                   ORDER BY total_requests DESC
                   LIMIT 10";

            $stmt = $this->pdo->query($sql);
            $stats = $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur statistiques utilisateurs : " . $e->getMessage());
        }

        return $stats;
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
}