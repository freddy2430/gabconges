<?php
/**
 * Script de test de connexion à la base de données
 * Utilisé pour diagnostiquer les problèmes de connexion
 */

// Inclure la configuration de la base de données
require_once __DIR__ . '/config/database.php';

echo "=== Test de connexion à la base de données ===\n\n";

// Test 1: Vérifier les constantes de configuration
echo "1. Vérification des constantes de configuration:\n";
echo "   DB_HOST: " . DB_HOST . "\n";
echo "   DB_NAME: " . DB_NAME . "\n";
echo "   DB_USER: " . DB_USER . "\n";
echo "   DB_PASS: " . (empty(DB_PASS) ? '(vide)' : '****') . "\n";
echo "   DB_CHARSET: " . DB_CHARSET . "\n\n";

// Test 2: Tester la connexion PDO
echo "2. Test de connexion PDO:\n";
try {
    $pdo = connectDB();
    echo "   ✓ Connexion réussie!\n";

    // Test 3: Vérifier si les tables existent
    echo "\n3. Vérification des tables:\n";

    $tables = ['users', 'leave_types', 'leave_requests', 'holidays'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "   ✓ Table '$table' existe\n";

                // Vérifier le nombre d'enregistrements pour certaines tables
                if ($table === 'users') {
                    $countStmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                    $count = $countStmt->fetch()['count'];
                    echo "     - Nombre d'utilisateurs: $count\n";
                }
            } else {
                echo "   ✗ Table '$table' n'existe pas\n";
            }
        } catch (Exception $e) {
            echo "   ✗ Erreur lors de la vérification de la table '$table': " . $e->getMessage() . "\n";
        }
    }

    // Test 4: Tester une requête simple
    echo "\n4. Test de requête simple:\n";
    try {
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "   ✓ Requête simple réussie: " . $result['test'] . "\n";
    } catch (Exception $e) {
        echo "   ✗ Erreur de requête simple: " . $e->getMessage() . "\n";
    }

} catch (PDOException $e) {
    echo "   ✗ Erreur de connexion PDO: " . $e->getMessage() . "\n";
    echo "   Code d'erreur: " . $e->getCode() . "\n";

    // Conseils de dépannage
    echo "\nConseils de dépannage:\n";
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "   - La base de données '" . DB_NAME . "' n'existe pas\n";
        echo "   - Exécutez le script database.sql pour créer la base de données\n";
    } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "   - Vérifiez les identifiants de connexion (DB_USER, DB_PASS)\n";
        echo "   - Vérifiez les permissions de l'utilisateur MySQL\n";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "   - Vérifiez que MySQL/MariaDB est démarré\n";
        echo "   - Vérifiez le nom d'hôte (DB_HOST)\n";
    }
}

echo "\n=== Fin du test ===\n";
?>