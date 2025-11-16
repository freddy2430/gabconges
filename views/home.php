<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Système de congésGAB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .hero {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            padding: 6rem 0;
            text-align: center;
        }
        .feature-icon {
            font-size: 3rem;
            color: #0d6efd;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

    <header class="hero">
        <div class="container">
            <h1 class="display-4">Système de congésGAB</h1>
            <p class="lead">Une solution complète et intuitive pour gérer les congés de vos employés.</p>
            <div class="mt-4">
                <a href="index.php?action=login" class="btn btn-light btn-lg me-2">Connexion</a>
                <a href="index.php?action=register" class="btn btn-outline-light btn-lg">Inscription</a>
            </div>
        </div>
    </header>

    <main class="container my-5">
        <h2 class="text-center mb-5">Fonctionnalités Clés</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 text-center p-3">
                    <div class="card-body">
                        <div class="feature-icon mb-3">👤</div>
                        <h5 class="card-title">Gestion des Utilisateurs</h5>
                        <p class="card-text">Authentification sécurisée, gestion des rôles (admin/employé) et des comptes.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-3">
                    <div class="card-body">
                        <div class="feature-icon mb-3">🌴</div>
                        <h5 class="card-title">Types de Congés</h5>
                        <p class="card-text">Types personnalisables (Annuel, Maladie, etc.) avec configuration flexible des jours.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-3">
                    <div class="card-body">
                        <div class="feature-icon mb-3">✉️</div>
                        <h5 class="card-title">Demandes de Congés</h5>
                        <p class="card-text">Soumission facile, calcul automatique des jours et validation intuitive.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-3">
                    <div class="card-body">
                        <div class="feature-icon mb-3">📊</div>
                        <h5 class="card-title">Tableaux de Bord</h5>
                        <p class="card-text">Vues dédiées pour les employés et les administrateurs avec des statistiques pertinentes.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-3">
                    <div class="card-body">
                        <div class="feature-icon mb-3">✅</div>
                        <h5 class="card-title">Validation et Approbation</h5>
                        <p class="card-text">Processus d'approbation simple pour les managers avec historique des décisions.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-3">
                    <div class="card-body">
                        <div class="feature-icon mb-3">📈</div>
                        <h5 class="card-title">Rapports et Exports</h5>
                        <p class="card-text">Statistiques détaillées et possibilité d'exporter les données au format CSV.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-4 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-0">&copy; 2025 Système de congésGAB. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
