<?php
/**
 * Modèle LeaveRequest - Gestion des demandes de congés
 * Application de congésGAB
 */

class LeaveRequest {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer une nouvelle demande de congé
     * @param array $data Données de la demande
     * @return bool Succès de la création
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO leave_requests (user_id, leave_type_id, start_date, end_date, reason, status, requested_days, created_at)
                    VALUES (:user_id, :leave_type_id, :start_date, :end_date, :reason, :status, :requested_days, NOW())";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':leave_type_id', $data['leave_type_id']);
            $stmt->bindParam(':start_date', $data['start_date']);
            $stmt->bindParam(':end_date', $data['end_date']);
            $stmt->bindParam(':reason', $data['reason']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':requested_days', $data['requested_days']);

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("Erreur création demande de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer une demande de congé par ID
     * @param int $id ID de la demande
     * @return array|false Informations de la demande ou false
     */
    public function getById($id) {
        try {
            $sql = "SELECT lr.*, u.first_name, u.last_name, u.username, lt.name as leave_type_name
                    FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                    WHERE lr.id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch();

        } catch(PDOException $e) {
            error_log("Erreur récupération demande de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les demandes d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre de demandes par page
     * @param int $offset Décalage
     * @return array Liste des demandes
     */
    public function getByUserId($userId, $limit = 50, $offset = 0) {
        try {
            $sql = "SELECT lr.*, lt.name as leave_type_name, lt.max_days_per_year
                    FROM leave_requests lr
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                    WHERE lr.user_id = :user_id
                    ORDER BY lr.created_at DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur récupération demandes utilisateur : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer toutes les demandes (avec pagination)
     * @param int $limit Nombre de demandes par page
     * @param int $offset Décalage
     * @param string $status Filtrer par statut (optionnel)
     * @return array Liste des demandes
     */
    public function getAll($limit = 50, $offset = 0, $status = null) {
        try {
            $sql = "SELECT lr.*, u.first_name, u.last_name, u.username, lt.name as leave_type_name
                    FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    JOIN leave_types lt ON lr.leave_type_id = lt.id";

            if ($status) {
                $sql .= " WHERE lr.status = :status";
            }

            $sql .= " ORDER BY lr.created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);

            if ($status) {
                $stmt->bindParam(':status', $status);
            }

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur récupération demandes : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mettre à jour une demande de congé
     * @param int $id ID de la demande
     * @param array $data Nouvelles données
     * @return bool Succès de la mise à jour
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE leave_requests SET
                    leave_type_id = :leave_type_id,
                    start_date = :start_date,
                    end_date = :end_date,
                    reason = :reason,
                    requested_days = :requested_days,
                    updated_at = NOW()";

            // Ajouter les champs d'approbation si présents
            if (isset($data['status'])) {
                $sql .= ", status = :status";
            }
            if (isset($data['approved_by'])) {
                $sql .= ", approved_by = :approved_by, approved_at = NOW()";
            }
            if (isset($data['rejection_reason'])) {
                $sql .= ", rejection_reason = :rejection_reason";
            }

            $sql .= " WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':leave_type_id', $data['leave_type_id']);
            $stmt->bindParam(':start_date', $data['start_date']);
            $stmt->bindParam(':end_date', $data['end_date']);
            $stmt->bindParam(':reason', $data['reason']);
            $stmt->bindParam(':requested_days', $data['requested_days']);

            if (isset($data['status'])) {
                $stmt->bindParam(':status', $data['status']);
            }
            if (isset($data['approved_by'])) {
                $stmt->bindParam(':approved_by', $data['approved_by']);
            }
            if (isset($data['rejection_reason'])) {
                $stmt->bindParam(':rejection_reason', $data['rejection_reason']);
            }

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("Erreur mise à jour demande de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approuver une demande de congé
     * @param int $id ID de la demande
     * @param int $approvedBy ID de l'administrateur qui approuve
     * @return bool Succès de l'approbation
     */
    public function approve($id, $approvedBy) {
        try {
            $data = [
                'status' => 'approved',
                'approved_by' => $approvedBy
            ];

            return $this->update($id, $data);

        } catch(Exception $e) {
            error_log("Erreur approbation demande : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Rejeter une demande de congé
     * @param int $id ID de la demande
     * @param int $rejectedBy ID de l'administrateur qui rejette
     * @param string $reason Raison du rejet
     * @return bool Succès du rejet
     */
    public function reject($id, $rejectedBy, $reason = null) {
        try {
            $data = [
                'status' => 'rejected',
                'approved_by' => $rejectedBy,
                'rejection_reason' => $reason
            ];

            return $this->update($id, $data);

        } catch(Exception $e) {
            error_log("Erreur rejet demande : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer une demande de congé
     * @param int $id ID de la demande
     * @return bool Succès de la suppression
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM leave_requests WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("Erreur suppression demande de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compter le nombre total de demandes
     * @param string $status Filtrer par statut (optionnel)
     * @return int Nombre de demandes
     */
    public function count($status = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM leave_requests";

            if ($status) {
                $sql .= " WHERE status = :status";
            }

            $stmt = $this->pdo->prepare($sql);

            if ($status) {
                $stmt->bindParam(':status', $status);
            }

            $stmt->execute();
            $result = $stmt->fetch();

            return (int) $result['count'];

        } catch(PDOException $e) {
            error_log("Erreur comptage demandes : " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer les statistiques des demandes
     * @param int $userId ID de l'utilisateur (optionnel, pour les stats d'un utilisateur)
     * @return array Statistiques des demandes
     */
    public function getStats($userId = null) {
        try {
            $whereClause = $userId ? "WHERE lr.user_id = :user_id" : "";

            $sql = "SELECT
                        COUNT(*) as total_requests,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                        SUM(requested_days) as total_days_requested,
                        SUM(CASE WHEN status = 'approved' THEN requested_days ELSE 0 END) as total_days_approved
                    FROM leave_requests lr $whereClause";

            $stmt = $this->pdo->prepare($sql);

            if ($userId) {
                $stmt->bindParam(':user_id', $userId);
            }

            $stmt->execute();
            return $stmt->fetch();

        } catch(PDOException $e) {
            error_log("Erreur statistiques demandes : " . $e->getMessage());
            return [
                'total_requests' => 0,
                'pending_requests' => 0,
                'approved_requests' => 0,
                'rejected_requests' => 0,
                'total_days_requested' => 0,
                'total_days_approved' => 0
            ];
        }
    }

    /**
     * Récupérer les demandes en attente d'approbation
     * @param int $limit Nombre de demandes par page
     * @param int $offset Décalage
     * @return array Liste des demandes en attente
     */
    public function getPendingRequests($limit = 50, $offset = 0) {
        return $this->getAll($limit, $offset, 'pending');
    }

    /**
     * Récupérer les demandes récentes (pour le tableau de bord)
     * @param int $limit Nombre de demandes
     * @return array Liste des demandes récentes
     */
    public function getRecentRequests($limit = 10) {
        try {
            $sql = "SELECT lr.*, u.first_name, u.last_name, lt.name as leave_type_name
                    FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                    ORDER BY lr.created_at DESC
                    LIMIT :limit";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur demandes récentes : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifier les chevauchements de congés pour un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param string $startDate Date de début
     * @param string $endDate Date de fin
     * @param int $excludeId ID de la demande à exclure (pour les modifications)
     * @return bool True si chevauchement détecté
     */
    public function checkOverlap($userId, $startDate, $endDate, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM leave_requests
                    WHERE user_id = :user_id
                    AND status = 'approved'
                    AND ((start_date BETWEEN :start_date AND :end_date)
                         OR (end_date BETWEEN :start_date AND :end_date)
                         OR (start_date <= :start_date AND end_date >= :end_date))";

            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);

            if ($excludeId) {
                $stmt->bindParam(':exclude_id', $excludeId);
            }

            $stmt->execute();
            $result = $stmt->fetch();

            return $result['count'] > 0;

        } catch(PDOException $e) {
            error_log("Erreur vérification chevauchement : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les demandes par période
     * @param string $startDate Date de début
     * @param string $endDate Date de fin
     * @return array Liste des demandes dans la période
     */
    public function getByDateRange($startDate, $endDate) {
        try {
            $sql = "SELECT lr.*, u.first_name, u.last_name, u.username, lt.name as leave_type_name
                    FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                    WHERE lr.status = 'approved'
                    AND ((lr.start_date BETWEEN :start_date AND :end_date)
                         OR (lr.end_date BETWEEN :start_date AND :end_date)
                         OR (lr.start_date <= :start_date AND lr.end_date >= :end_date))
                    ORDER BY lr.start_date";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur demandes par période : " . $e->getMessage());
            return [];
        }
    }
}