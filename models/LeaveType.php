<?php
/**
 * Modèle LeaveType - Gestion des types de congés
 * Application de congésGAB
 */

class LeaveType {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouveau type de congé
     * @param array $data Données du type de congé
     * @return bool Succès de la création
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO leave_types (name, description, max_days_per_year, requires_approval, is_active)
                    VALUES (:name, :description, :max_days_per_year, :requires_approval, :is_active)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':max_days_per_year', $data['max_days_per_year'], PDO::PARAM_INT);
            $stmt->bindParam(':requires_approval', $data['requires_approval'], PDO::PARAM_BOOL);
            $stmt->bindParam(':is_active', $data['is_active'], PDO::PARAM_BOOL);

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("Erreur création type de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un type de congé par ID
     * @param int $id ID du type de congé
     * @return array|false Informations du type de congé ou false
     */
    public function getById($id) {
        try {
            $sql = "SELECT id, name, description, max_days_per_year, requires_approval, is_active, created_at, updated_at
                    FROM leave_types WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch();

        } catch(PDOException $e) {
            error_log("Erreur récupération type de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un type de congé par nom
     * @param string $name Nom du type de congé
     * @return array|false Informations du type de congé ou false
     */
    public function getByName($name) {
        try {
            $sql = "SELECT id, name, description, max_days_per_year, requires_approval, is_active, created_at, updated_at
                    FROM leave_types WHERE name = :name";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->execute();

            return $stmt->fetch();

        } catch(PDOException $e) {
            error_log("Erreur récupération type de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les types de congés actifs
     * @return array Liste des types de congés actifs
     */
    public function getAllActive() {
        try {
            $sql = "SELECT id, name, description, max_days_per_year, requires_approval, is_active, created_at, updated_at
                    FROM leave_types WHERE is_active = 1 ORDER BY name";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur récupération types de congés actifs : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer tous les types de congés (avec pagination)
     * @param int $limit Nombre de types par page
     * @param int $offset Décalage
     * @return array Liste des types de congés
     */
    public function getAll($limit = 50, $offset = 0) {
        try {
            $sql = "SELECT id, name, description, max_days_per_year, requires_approval, is_active, created_at, updated_at
                    FROM leave_types ORDER BY name LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur récupération types de congés : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mettre à jour un type de congé
     * @param int $id ID du type de congé
     * @param array $data Nouvelles données
     * @return bool Succès de la mise à jour
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE leave_types SET
                    name = :name,
                    description = :description,
                    max_days_per_year = :max_days_per_year,
                    requires_approval = :requires_approval,
                    is_active = :is_active,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':max_days_per_year', $data['max_days_per_year'], PDO::PARAM_INT);
            $stmt->bindParam(':requires_approval', $data['requires_approval'], PDO::PARAM_BOOL);
            $stmt->bindParam(':is_active', $data['is_active'], PDO::PARAM_BOOL);

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("Erreur mise à jour type de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un type de congé
     * @param int $id ID du type de congé
     * @return bool Succès de la suppression
     */
    public function delete($id) {
        try {
            // Vérifier si le type est utilisé dans des demandes de congés
            $sql = "SELECT COUNT(*) as count FROM leave_requests WHERE leave_type_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                throw new Exception("Impossible de supprimer ce type de congé car il est utilisé dans des demandes existantes.");
            }

            $sql = "DELETE FROM leave_types WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("Erreur suppression type de congé : " . $e->getMessage());
            return false;
        } catch(Exception $e) {
            error_log("Erreur métier suppression type de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compter le nombre total de types de congés
     * @return int Nombre de types de congés
     */
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as count FROM leave_types";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch();

            return (int) $result['count'];

        } catch(PDOException $e) {
            error_log("Erreur comptage types de congés : " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Vérifier si un nom de type de congé existe déjà
     * @param string $name Nom du type de congé
     * @param int $excludeId ID à exclure (pour les mises à jour)
     * @return bool True si existe
     */
    public function nameExists($name, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM leave_types WHERE name = :name";
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name);

            if ($excludeId) {
                $stmt->bindParam(':exclude_id', $excludeId);
            }

            $stmt->execute();
            $result = $stmt->fetch();

            return $result['count'] > 0;

        } catch(PDOException $e) {
            error_log("Erreur vérification nom type de congé : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les statistiques d'utilisation d'un type de congé
     * @param int $id ID du type de congé
     * @return array Statistiques d'utilisation
     */
    public function getUsageStats($id) {
        try {
            $sql = "SELECT
                        COUNT(*) as total_requests,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                        AVG(requested_days) as avg_days
                    FROM leave_requests
                    WHERE leave_type_id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch();

        } catch(PDOException $e) {
            error_log("Erreur statistiques type de congé : " . $e->getMessage());
            return [
                'total_requests' => 0,
                'approved_requests' => 0,
                'pending_requests' => 0,
                'rejected_requests' => 0,
                'avg_days' => 0
            ];
        }
    }

    /**
     * Basculer le statut actif/inactif d'un type de congé
     * @param int $id ID du type de congé
     * @return bool Succès de l'opération
     */
    public function toggleStatus($id) {
        try {
            $type = $this->getById($id);

            if (!$type) {
                return false;
            }

            $newStatus = $type['is_active'] ? 0 : 1;
            $data = [
                'name' => $type['name'],
                'description' => $type['description'],
                'max_days_per_year' => $type['max_days_per_year'],
                'requires_approval' => $type['requires_approval'],
                'is_active' => $newStatus
            ];

            return $this->update($id, $data);

        } catch(Exception $e) {
            error_log("Erreur basculement statut type de congé : " . $e->getMessage());
            return false;
        }
    }
}