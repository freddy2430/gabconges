<?php
/**
 * Contrôleur des statistiques et exports
 * Application de congésGAB
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/LeaveType.php';
require_once __DIR__ . '/../models/User.php';

class StatisticsController {
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
     * Afficher la page des statistiques
     */
    public function index() {
        $this->requireAdmin();

        // Statistiques générales
        $generalStats = $this->leaveRequestModel->getStats();

        // Statistiques par type de congé
        $leaveTypeStats = $this->getLeaveTypeStats();

        // Statistiques par utilisateur
        $userStats = $this->getUserStats();

        // Statistiques par mois (pour les 12 derniers mois)
        $monthlyStats = $this->getMonthlyStats();

        // Filtres de période
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-6 months'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Statistiques filtrées par période
        $filteredStats = $this->getFilteredStats($startDate, $endDate);

        require_once __DIR__ . '/../views/statistics/index.php';
    }

    /**
     * Exporter les données
     */
    public function export($format = 'csv', $type = 'all') {
        $this->requireAdmin();

        // Récupération des données selon le type
        switch ($type) {
            case 'requests':
                $data = $this->getAllRequestsForExport();
                $filename = 'demandes_conges';
                break;
            case 'users':
                $data = $this->getAllUsersForExport();
                $filename = 'utilisateurs';
                break;
            case 'types':
                $data = $this->getAllLeaveTypesForExport();
                $filename = 'types_conges';
                break;
            default:
                $data = $this->getAllDataForExport();
                $filename = 'toutes_donnees';
        }

        if ($format === 'csv') {
            $this->exportToCSV($data, $filename);
        } elseif ($format === 'pdf') {
            $this->exportToPDF($data, $filename, $type);
        }
    }

    /**
     * Récupérer les statistiques par type de congé
     * @return array Statistiques par type
     */
    private function getLeaveTypeStats() {
        try {
            $sql = "SELECT lt.name,
                          COUNT(lr.id) as total_requests,
                          SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                          SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                          SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                          SUM(lr.requested_days) as total_days,
                          AVG(lr.requested_days) as avg_days
                   FROM leave_types lt
                   LEFT JOIN leave_requests lr ON lt.id = lr.leave_type_id
                   GROUP BY lt.id, lt.name
                   ORDER BY total_requests DESC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur statistiques types de congés : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les statistiques par utilisateur
     * @return array Statistiques par utilisateur
     */
    private function getUserStats() {
        try {
            $sql = "SELECT u.first_name, u.last_name, u.username,
                          COUNT(lr.id) as total_requests,
                          SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                          SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                          SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                          SUM(lr.requested_days) as total_days,
                          AVG(lr.requested_days) as avg_days
                   FROM users u
                   LEFT JOIN leave_requests lr ON u.id = lr.user_id
                   WHERE u.is_active = 1
                   GROUP BY u.id, u.first_name, u.last_name, u.username
                   ORDER BY total_requests DESC
                   LIMIT 20";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur statistiques utilisateurs : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les statistiques mensuelles
     * @return array Statistiques par mois
     */
    private function getMonthlyStats() {
        try {
            $sql = "SELECT
                        YEAR(lr.created_at) as year,
                        MONTH(lr.created_at) as month,
                        COUNT(*) as total_requests,
                        SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                        SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                        SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                        SUM(lr.requested_days) as total_days
                    FROM leave_requests lr
                    WHERE lr.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    GROUP BY YEAR(lr.created_at), MONTH(lr.created_at)
                    ORDER BY year DESC, month DESC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur statistiques mensuelles : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les statistiques filtrées par période
     * @param string $startDate Date de début
     * @param string $endDate Date de fin
     * @return array Statistiques filtrées
     */
    private function getFilteredStats($startDate, $endDate) {
        try {
            $sql = "SELECT
                        COUNT(*) as total_requests,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                        SUM(requested_days) as total_days
                    FROM leave_requests
                    WHERE created_at BETWEEN :start_date AND :end_date";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();

            return $stmt->fetch();

        } catch(PDOException $e) {
            error_log("Erreur statistiques filtrées : " . $e->getMessage());
            return [
                'total_requests' => 0,
                'approved_requests' => 0,
                'pending_requests' => 0,
                'rejected_requests' => 0,
                'total_days' => 0
            ];
        }
    }

    /**
     * Récupérer toutes les demandes pour l'export
     * @return array Données des demandes
     */
    private function getAllRequestsForExport() {
        try {
            $sql = "SELECT
                        lr.id,
                        u.first_name,
                        u.last_name,
                        u.username,
                        lt.name as leave_type,
                        lr.start_date,
                        lr.end_date,
                        lr.requested_days,
                        lr.reason,
                        lr.status,
                        lr.created_at,
                        approver.first_name as approved_by_first_name,
                        approver.last_name as approved_by_last_name,
                        lr.approved_at,
                        lr.rejection_reason
                    FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                    LEFT JOIN users approver ON lr.approved_by = approver.id
                    ORDER BY lr.created_at DESC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur export demandes : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer tous les utilisateurs pour l'export
     * @return array Données des utilisateurs
     */
    private function getAllUsersForExport() {
        try {
            $sql = "SELECT
                        id,
                        username,
                        email,
                        first_name,
                        last_name,
                        role,
                        is_active,
                        created_at,
                        updated_at
                    FROM users
                    ORDER BY created_at DESC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur export utilisateurs : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer tous les types de congés pour l'export
     * @return array Données des types de congés
     */
    private function getAllLeaveTypesForExport() {
        try {
            $sql = "SELECT
                        id,
                        name,
                        description,
                        max_days_per_year,
                        requires_approval,
                        is_active,
                        created_at,
                        updated_at
                    FROM leave_types
                    ORDER BY name";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur export types de congés : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer toutes les données pour l'export complet
     * @return array Toutes les données
     */
    private function getAllDataForExport() {
        return [
            'requests' => $this->getAllRequestsForExport(),
            'users' => $this->getAllUsersForExport(),
            'leave_types' => $this->getAllLeaveTypesForExport()
        ];
    }

    /**
     * Exporter en CSV
     * @param array $data Données à exporter
     * @param string $filename Nom du fichier
     */
    private function exportToCSV($data, $filename) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // Écrire le BOM UTF-8 pour Excel
        fwrite($output, "\xEF\xBB\xBF");

        if (!empty($data)) {
            // Écrire les en-têtes
            $headers = array_keys($data[0]);
            fputcsv($output, $headers, ';');

            // Écrire les données
            foreach ($data as $row) {
                fputcsv($output, $row, ';');
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Exporter en PDF (simplifié - génération HTML pour impression)
     * @param array $data Données à exporter
     * @param string $filename Nom du fichier
     * @param string $type Type de données
     */
    private function exportToPDF($data, $filename, $type) {
        // Pour l'instant, générer un HTML imprimable
        // Dans un environnement réel, utiliser une librairie comme TCPDF ou DomPDF

        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d') . '.html"');

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Export <?php echo $filename; ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { border-collapse: collapse; width: 100%; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                h1 { color: #333; }
                .header { text-align: center; margin-bottom: 30px; }
                .stats { margin: 20px 0; }
                .page-break { page-break-before: always; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Système de congésGAB</h1>
                <h2>Export <?php echo ucfirst($filename); ?></h2>
                <p>Généré le <?php echo date('d/m/Y à H:i'); ?></p>
            </div>

            <?php if (!empty($data)): ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach (array_keys($data[0]) as $header): ?>
                                <th><?php echo htmlspecialchars($header); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($row as $cell): ?>
                                    <td><?php echo htmlspecialchars($cell); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune donnée à exporter.</p>
            <?php endif; ?>
        </body>
        </html>
        <?php
        exit;
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