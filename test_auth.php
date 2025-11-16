<?php
/**
 * Script de test d'authentification
 */

// Inclure les fichiers nécessaires
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';

echo "=== Test d'authentification ===\n\n";

try {
    // Connexion à la base de données
    $pdo = connectDB();
    $userModel = new User($pdo);

    echo "1. Test de connexion avec l'administrateur par défaut:\n";
    echo "   Username: admin\n";
    echo "   Password: Admin123!\n";

    $user = $userModel->authenticate('admin', 'Admin123!');

    if ($user) {
        echo "   ✓ Authentification réussie!\n";
        echo "   Utilisateur: {$user['username']} ({$user['email']})\n";
        echo "   Rôle: {$user['role']}\n";
        echo "   Statut: " . ($user['is_active'] ? 'Actif' : 'Inactif') . "\n";
    } else {
        echo "   ✗ Échec de l'authentification\n";

        // Vérifier si l'utilisateur existe
        $userExists = $userModel->getByUsername('admin');
        if ($userExists) {
            echo "   - L'utilisateur admin existe mais le mot de passe est incorrect\n";

            // Vérifier le hash du mot de passe dans la base
            $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
            $stmt->execute(['admin']);
            $hash = $stmt->fetch()['password'];
            echo "   - Hash du mot de passe dans la base: " . substr($hash, 0, 20) . "...\n";
        } else {
            echo "   - L'utilisateur admin n'existe pas\n";
        }
    }

    echo "\n2. Test de création d'un nouvel utilisateur:\n";
    $testUser = [
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => password_hash('Test123!', PASSWORD_DEFAULT),
        'first_name' => 'Test',
        'last_name' => 'User',
        'role' => 'employee',
        'is_active' => 1
    ];

    // Vérifier si l'utilisateur de test existe déjà
    if ($userModel->usernameExists('testuser')) {
        echo "   - L'utilisateur de test existe déjà, suppression...\n";
        $userModel->delete($userModel->getByUsername('testuser')['id']);
    }

    if ($userModel->create($testUser)) {
        echo "   ✓ Utilisateur de test créé avec succès\n";

        // Tester l'authentification avec le nouvel utilisateur
        echo "\n3. Test d'authentification avec l'utilisateur de test:\n";
        $authTest = $userModel->authenticate('testuser', 'Test123!');

        if ($authTest) {
            echo "   ✓ Authentification avec l'utilisateur de test réussie!\n";
        } else {
            echo "   ✗ Échec de l'authentification avec l'utilisateur de test\n";
        }

        // Nettoyer
        $userModel->delete($userModel->getByUsername('testuser')['id']);
        echo "   - Utilisateur de test supprimé\n";

    } else {
        echo "   ✗ Échec de la création de l'utilisateur de test\n";
    }

    echo "\n4. Test des méthodes de vérification:\n";
    echo "   Username 'admin' existe: " . ($userModel->usernameExists('admin') ? 'Oui' : 'Non') . "\n";
    echo "   Email 'admin@gestion-conges.local' existe: " . ($userModel->emailExists('admin@gestion-conges.local') ? 'Oui' : 'Non') . "\n";
    echo "   Username 'nonexistent' existe: " . ($userModel->usernameExists('nonexistent') ? 'Oui' : 'Non') . "\n";

} catch (Exception $e) {
    echo "   ✗ Erreur lors du test: " . $e->getMessage() . "\n";
}

echo "\n=== Fin du test d'authentification ===\n";
?>