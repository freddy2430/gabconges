<?php
/**
 * Script de test d'initialisation de la base de données
 */

// Inclure la configuration de la base de données
require_once __DIR__ . '/config/database.php';

echo "=== Test d'initialisation de la base de données ===\n\n";

echo "1. Test de la fonction initDatabase():\n";
try {
    $result = initDatabase();
    if ($result) {
        echo "   ✓ Initialisation réussie!\n";
    } else {
        echo "   ✗ Échec de l'initialisation\n";
    }
} catch (Exception $e) {
    echo "   ✗ Erreur lors de l'initialisation: " . $e->getMessage() . "\n";
}

echo "\n2. Vérification du contenu de la base après initialisation:\n";

// Reconnecter à la base pour vérifier
try {
    $pdo = connectDB();

    // Vérifier les utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    echo "   Utilisateurs: $userCount\n";

    // Vérifier les types de congés
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM leave_types");
    $leaveTypeCount = $stmt->fetch()['count'];
    echo "   Types de congés: $leaveTypeCount\n";

    // Vérifier les demandes de congés
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM leave_requests");
    $requestCount = $stmt->fetch()['count'];
    echo "   Demandes de congés: $requestCount\n";

    // Vérifier les jours fériés
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM holidays");
    $holidayCount = $stmt->fetch()['count'];
    echo "   Jours fériés: $holidayCount\n";

    // Afficher les détails des utilisateurs
    if ($userCount > 0) {
        echo "\n3. Détails des utilisateurs:\n";
        $stmt = $pdo->query("SELECT id, username, email, role, is_active FROM users");
        while ($user = $stmt->fetch()) {
            echo "   ID {$user['id']}: {$user['username']} ({$user['email']}) - {$user['role']}\n";
        }
    }

    // Afficher les types de congés
    if ($leaveTypeCount > 0) {
        echo "\n4. Types de congés disponibles:\n";
        $stmt = $pdo->query("SELECT id, name, max_days_per_year FROM leave_types");
        while ($type = $stmt->fetch()) {
            echo "   {$type['name']} (max {$type['max_days_per_year']} jours/an)\n";
        }
    }

} catch (Exception $e) {
    echo "   ✗ Erreur lors de la vérification: " . $e->getMessage() . "\n";
}

echo "\n=== Fin du test d'initialisation ===\n";
?>