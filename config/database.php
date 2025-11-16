<?php
/**
 * Configuration de la base de données
 * Application de congésGAB
 */

// Configuration de la base de données - PRODUCTION (à remplir)
define('DB_HOST', 'VOTRE_HOTE_ICI');      // ex: 'localhost' ou l'adresse du serveur de DB
define('DB_NAME', 'VOTRE_NOM_DB_ICI'); // Le nom de la base de données
define('DB_USER', 'VOTRE_USER_ICI');   // L'utilisateur de la base de données
define('DB_PASS', 'VOTRE_MDP_ICI');     // Le mot de passe
define('DB_CHARSET', 'utf8mb4');

/*
// Configuration de la base de données - LOCAL
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_conges');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
*/

// Options PDO pour une meilleure sécurité et gestion d'erreurs
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => true,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

/**
 * Fonction de connexion à la base de données
 * @return PDO|null Instance PDO ou null en cas d'erreur
 */
function connectDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    } catch(PDOException $e) {
        // En développement, afficher l'erreur
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        } else {
            // En production, loguer l'erreur et afficher un message générique
            error_log("Erreur DB : " . $e->getMessage());
            die("Erreur de connexion à la base de données");
        }
    }
}

/**
 * Fonction d'initialisation de la base de données
 * Crée les tables si elles n'existent pas
 * @return bool Succès de l'initialisation
 */
function initDatabase() {
    try {
        $pdo = connectDB();

        // Lecture du fichier SQL
        $sql = file_get_contents(__DIR__ . '/../database.sql');

        if ($sql === false) {
            throw new Exception("Impossible de lire le fichier database.sql");
        }

        // Séparation des requêtes SQL
        $queries = [];
        $lines = explode("\n", $sql);
        $currentQuery = '';
        $inComment = false;

        foreach ($lines as $line) {
            $line = trim($line);

            // Gestion des commentaires
            if (strpos($line, '--') === 0) {
                continue;
            }

            if (strpos($line, '/*') === 0) {
                $inComment = true;
                continue;
            }

            if ($inComment) {
                if (strpos($line, '*/') !== false) {
                    $inComment = false;
                }
                continue;
            }

            // Ajout de la ligne à la requête courante
            if (!empty($line)) {
                $currentQuery .= $line . "\n";
            }

            // Si la ligne se termine par un point-virgule, c'est la fin d'une requête
            if (!empty($line) && substr($line, -1) === ';') {
                $queries[] = $currentQuery;
                $currentQuery = '';
            }
        }

        // Exécution des requêtes
        foreach ($queries as $query) {
            if (!empty(trim($query))) {
                $pdo->exec($query);
            }
        }

        return true;

    } catch(Exception $e) {
        error_log("Erreur d'initialisation DB : " . $e->getMessage());
        return false;
    }
}

/**
 * Fonction utilitaire pour calculer les jours ouvrés entre deux dates
 * @param string $startDate Date de début (YYYY-MM-DD)
 * @param string $endDate Date de fin (YYYY-MM-DD)
 * @return int Nombre de jours ouvrés
 */
function calculateWorkingDays($startDate, $endDate) {
    try {
        $pdo = connectDB();

        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day'); // Inclure la date de fin

        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);

        $workingDays = 0;
        $holidays = [];

        // Récupérer les jours fériés
        $stmt = $pdo->query("SELECT holiday_date FROM holidays WHERE holiday_date BETWEEN '{$startDate}' AND '{$endDate}'");
        while ($holiday = $stmt->fetch()) {
            $holidays[] = $holiday['holiday_date'];
        }

        foreach ($period as $date) {
            // Exclure les weekends (samedi et dimanche)
            if ($date->format('N') >= 6) {
                continue;
            }

            // Exclure les jours fériés
            if (in_array($date->format('Y-m-d'), $holidays)) {
                continue;
            }

            $workingDays++;
        }

        return $workingDays;

    } catch(Exception $e) {
        error_log("Erreur calcul jours ouvrés : " . $e->getMessage());
        return 0;
    }
}

/**
 * Fonction pour vérifier les chevauchements de congés
 * @param int $userId ID de l'utilisateur
 * @param string $startDate Date de début
 * @param string $endDate Date de fin
 * @param int $excludeId ID de la demande à exclure (pour les modifications)
 * @return bool True si chevauchement détecté
 */
function checkLeaveOverlap($userId, $startDate, $endDate, $excludeId = null) {
    try {
        $pdo = connectDB();

        $sql = "SELECT COUNT(*) as count FROM leave_requests
                WHERE user_id = :user_id
                AND status = 'approved'
                AND ((start_date BETWEEN :start_date AND :end_date)
                     OR (end_date BETWEEN :start_date AND :end_date)
                     OR (start_date <= :start_date AND end_date >= :end_date))";

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);

        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }

        $stmt->execute();
        $result = $stmt->fetch();

        return $result['count'] > 0;

    } catch(Exception $e) {
        error_log("Erreur vérification chevauchement : " . $e->getMessage());
        return false;
    }
}