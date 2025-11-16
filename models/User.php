<?php
/**
 * Modèle User - Gestion des utilisateurs
 * Application de congésGAB
 */

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouvel utilisateur
     * @param array $data Données de l'utilisateur
     * @return bool Succès de la création
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO users (username, email, password, first_name, last_name, role, is_active)
                    VALUES (:username, :email, :password, :first_name, :last_name, :role, :is_active)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $data['password']);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':is_active', $data['is_active']);

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("Erreur création utilisateur : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Authentifier un utilisateur
     * @param string $username Nom d'utilisateur ou email
     * @param string $password Mot de passe
     * @return array|false Informations utilisateur ou false si échec
     */
    public function authenticate($username, $password) {
        try {
            $sql = "SELECT id, username, email, password, first_name, last_name, role, is_active
                    FROM users
                    WHERE (username = :username OR email = :email)
                    AND is_active = 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $username);
            $stmt->execute();

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Mettre à jour la dernière connexion
                $this->updateLastLogin($user['id']);
                return $user;
            }

            return false;

        } catch(PDOException $e) {
            error_log("Erreur authentification : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un utilisateur par ID
     * @param int $id ID de l'utilisateur
     * @return array|false Informations utilisateur ou false
     */
    public function getById($id) {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, role, is_active, created_at, updated_at
                    FROM users WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch();

        } catch(PDOException $e) {
            error_log("Erreur récupération utilisateur : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un utilisateur par nom d'utilisateur
     * @param string $username Nom d'utilisateur
     * @return array|false Informations utilisateur ou false
     */
    public function getByUsername($username) {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, role, is_active, created_at, updated_at
                    FROM users WHERE username = :username";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            return $stmt->fetch();

        } catch(PDOException $e) {
            error_log("Erreur récupération utilisateur : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les utilisateurs (avec pagination)
     * @param int $limit Nombre d'utilisateurs par page
     * @param int $offset Décalage
     * @return array Liste des utilisateurs
     */
    public function getAll($limit = 50, $offset = 0) {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, role, is_active, created_at, updated_at
                    FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            error_log("Erreur récupération utilisateurs : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mettre à jour un utilisateur
     * @param int $id ID de l'utilisateur
     * @param array $data Nouvelles données
     * @return bool Succès de la mise à jour
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE users SET
                    username = :username,
                    email = :email,
                    first_name = :first_name,
                    last_name = :last_name,
                    role = :role,
                    is_active = :is_active,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':is_active', $data['is_active']);

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("Erreur mise à jour utilisateur : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un utilisateur
     * @param int $id ID de l'utilisateur
     * @return bool Succès de la suppression
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("Erreur suppression utilisateur : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compter le nombre total d'utilisateurs
     * @return int Nombre d'utilisateurs
     */
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as count FROM users";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch();

            return (int) $result['count'];

        } catch(PDOException $e) {
            error_log("Erreur comptage utilisateurs : " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Vérifier si un nom d'utilisateur existe déjà
     * @param string $username Nom d'utilisateur
     * @param int $excludeId ID à exclure (pour les mises à jour)
     * @return bool True si existe
     */
    public function usernameExists($username, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);

            if ($excludeId) {
                $stmt->bindParam(':exclude_id', $excludeId);
            }

            $stmt->execute();
            $result = $stmt->fetch();

            return $result['count'] > 0;

        } catch(PDOException $e) {
            error_log("Erreur vérification username : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si un email existe déjà
     * @param string $email Email
     * @param int $excludeId ID à exclure (pour les mises à jour)
     * @return bool True si existe
     */
    public function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':email', $email);

            if ($excludeId) {
                $stmt->bindParam(':exclude_id', $excludeId);
            }

            $stmt->execute();
            $result = $stmt->fetch();

            return $result['count'] > 0;

        } catch(PDOException $e) {
            error_log("Erreur vérification email : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour la dernière connexion
     * @param int $id ID de l'utilisateur
     */
    private function updateLastLogin($id) {
        try {
            $sql = "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

        } catch(PDOException $e) {
            // Ne pas bloquer l'authentification en cas d'erreur
            error_log("Erreur mise à jour dernière connexion : " . $e->getMessage());
        }
    }
}