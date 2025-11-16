<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Administration</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            background-color: #f8f9fa;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        .chart-container {
            position: relative;
            height: 300px;
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .table-responsive {
            background: white;
            border-radius: 10px;
            padding: 20px;
        }
        .export-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 10px;
        }
        .form-icon {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column p-3">
                    <h5 class="text-white mb-4">
                        <i class="bi bi-speedometer2 me-2"></i>Administration
                    </h5>
                    <nav class="nav nav-pills flex-column">
                        <a href="index.php?action=admin_dashboard" class="nav-link">
                            <i class="bi bi-house me-2"></i>Tableau de bord
                        </a>
                        <a href="index.php?action=users" class="nav-link">
                            <i class="bi bi-people me-2"></i>Utilisateurs
                        </a>
                        <a href="index.php?action=leave_types" class="nav-link">
                            <i class="bi bi-tags me-2"></i>Types de congés
                        </a>
                        <a href="index.php?action=leave_requests" class="nav-link">
                            <i class="bi bi-calendar-check me-2"></i>Demandes
                        </a>
                        <a href="index.php?action=statistics" class="nav-link active">
                            <i class="bi bi-bar-chart me-2"></i>Statistiques
                        </a>
                        <hr class="text-white-50">
                        <a href="index.php?action=dashboard" class="nav-link">
                            <i class="bi bi-person me-2"></i>Mon compte
                        </a>
                        <a href="index.php?action=logout" class="nav-link text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="container-fluid p-4">
                    <!-- Header -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1">Statistiques et Rapports</h2>
                                    <p class="text-muted mb-0">Analyse des données du système de congésGAB</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="dropdown">
                                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-download me-2"></i>Exporter
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><h6 class="dropdown-header">Format d'export</h6></li>
                                            <li><a class="dropdown-item" href="index.php?action=export&format=csv&type=all">CSV - Toutes les données</a></li>
                                            <li><a class="dropdown-item" href="index.php?action=export&format=pdf&type=all">PDF - Toutes les données</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><h6 class="dropdown-header">Données spécifiques</h6></li>
                                            <li><a class="dropdown-item" href="index.php?action=export&format=csv&type=requests">CSV - Demandes de congés</a></li>
                                            <li><a class="dropdown-item" href="index.php?action=export&format=csv&type=users">CSV - Utilisateurs</a></li>
                                            <li><a class="dropdown-item" href="index.php?action=export&format=csv&type=types">CSV - Types de congés</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="stats-card card">
                                <div class="card-body">
                                    <form method="GET" action="index.php" class="row g-3">
                                        <input type="hidden" name="action" value="statistics">

                                        <div class="col-md-4">
                                            <label for="start_date" class="form-label">
                                                <i class="bi bi-calendar-plus form-icon me-2"></i>Date de début
                                            </label>
                                            <input type="date"
                                                   class="form-control"
                                                   id="start_date"
                                                   name="start_date"
                                                   value="<?php echo htmlspecialchars($_GET['start_date'] ?? date('Y-m-d', strtotime('-6 months'))); ?>">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="end_date" class="form-label">
                                                <i class="bi bi-calendar-minus form-icon me-2"></i>Date de fin
                                            </label>
                                            <input type="date"
                                                   class="form-control"
                                                   id="end_date"
                                                   name="end_date"
                                                   value="<?php echo htmlspecialchars($_GET['end_date'] ?? date('Y-m-d')); ?>">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-funnel me-2"></i>Filtrer
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stats-card card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-check text-primary" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title text-primary mt-3 mb-2"><?php echo $generalStats['total_requests']; ?></h3>
                                    <p class="card-text text-muted mb-0">Total demandes</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="stats-card card">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title text-success mt-3 mb-2"><?php echo $generalStats['approved_requests']; ?></h3>
                                    <p class="card-text text-muted mb-0">Approuvées</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="stats-card card">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock text-warning" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title text-warning mt-3 mb-2"><?php echo $generalStats['pending_requests']; ?></h3>
                                    <p class="card-text text-muted mb-0">En attente</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="stats-card card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar text-info" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title text-info mt-3 mb-2"><?php echo $generalStats['total_days_approved']; ?></h3>
                                    <p class="card-text text-muted mb-0">Jours approuvés</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Leave Type Statistics -->
                        <div class="col-lg-6 mb-4">
                            <div class="stats-card card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="bi bi-tags me-2"></i>Statistiques par type de congé
                                    </h5>

                                    <?php if (empty($leaveTypeStats)): ?>
                                        <div class="text-center py-4">
                                            <i class="bi bi-bar-chart text-muted mb-2" style="font-size: 2rem;"></i>
                                            <p class="text-muted mb-0">Aucune donnée disponible</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Type de congé</th>
                                                        <th class="text-center">Demandes</th>
                                                        <th class="text-center">Approuvées</th>
                                                        <th class="text-center">Jours</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($leaveTypeStats as $stat): ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($stat['name']); ?></strong>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-secondary"><?php echo $stat['total_requests']; ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-success"><?php echo $stat['approved_requests']; ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-info"><?php echo $stat['total_days']; ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- User Statistics -->
                        <div class="col-lg-6 mb-4">
                            <div class="stats-card card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="bi bi-people me-2"></i>Top utilisateurs actifs
                                    </h5>

                                    <?php if (empty($userStats)): ?>
                                        <div class="text-center py-4">
                                            <i class="bi bi-people text-muted mb-2" style="font-size: 2rem;"></i>
                                            <p class="text-muted mb-0">Aucun utilisateur actif</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Utilisateur</th>
                                                        <th class="text-center">Demandes</th>
                                                        <th class="text-center">Jours</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($userStats as $stat): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                         style="width: 35px; height: 35px;">
                                                                        <?php echo strtoupper(substr($stat['first_name'], 0, 1) . substr($stat['last_name'], 0, 1)); ?>
                                                                    </div>
                                                                    <div>
                                                                        <strong><?php echo htmlspecialchars($stat['first_name'] . ' ' . $stat['last_name']); ?></strong>
                                                                        <br>
                                                                        <small class="text-muted">@<?php echo htmlspecialchars($stat['username']); ?></small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-secondary"><?php echo $stat['total_requests']; ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-info"><?php echo $stat['total_days']; ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtered Statistics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="export-card card">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="card-title mb-1">Statistiques de la période sélectionnée</h5>
                                            <p class="card-text mb-0 opacity-75">
                                                Du <?php echo date('d/m/Y', strtotime($startDate)); ?> au <?php echo date('d/m/Y', strtotime($endDate)); ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="h5 mb-0 text-white"><?php echo $filteredStats['total_requests']; ?></div>
                                                    <small class="opacity-75">Demandes</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h5 mb-0 text-white"><?php echo $filteredStats['approved_requests']; ?></div>
                                                    <small class="opacity-75">Approuvées</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h5 mb-0 text-white"><?php echo $filteredStats['total_days']; ?></div>
                                                    <small class="opacity-75">Jours</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Options -->
                    <div class="row">
                        <div class="col-12">
                            <div class="stats-card card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="bi bi-download me-2"></i>Options d'export
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="border rounded p-3 text-center">
                                                <i class="bi bi-file-earmark-spreadsheet text-success mb-2" style="font-size: 2rem;"></i>
                                                <h6>Export CSV</h6>
                                                <p class="text-muted small mb-3">Format tableur (Excel, LibreOffice)</p>
                                                <div class="d-grid gap-2">
                                                    <a href="index.php?action=export&format=csv&type=requests" class="btn btn-sm btn-outline-success">
                                                        Demandes
                                                    </a>
                                                    <a href="index.php?action=export&format=csv&type=users" class="btn btn-sm btn-outline-success">
                                                        Utilisateurs
                                                    </a>
                                                    <a href="index.php?action=export&format=csv&type=types" class="btn btn-sm btn-outline-success">
                                                        Types
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="border rounded p-3 text-center">
                                                <i class="bi bi-file-earmark-pdf text-danger mb-2" style="font-size: 2rem;"></i>
                                                <h6>Export PDF</h6>
                                                <p class="text-muted small mb-3">Format document imprimable</p>
                                                <div class="d-grid">
                                                    <a href="index.php?action=export&format=pdf&type=all" class="btn btn-sm btn-outline-danger">
                                                        Rapport complet
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="border rounded p-3 text-center">
                                                <i class="bi bi-graph-up text-primary mb-2" style="font-size: 2rem;"></i>
                                                <h6>Rapports avancés</h6>
                                                <p class="text-muted small mb-3">Analyses détaillées</p>
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="generateMonthlyReport()">
                                                        Rapport mensuel
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="generateUserReport()">
                                                        Par utilisateur
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function generateMonthlyReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            window.open(`index.php?action=export&format=pdf&type=monthly&start_date=${startDate}&end_date=${endDate}`, '_blank');
        }

        function generateUserReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            window.open(`index.php?action=export&format=pdf&type=user_stats&start_date=${startDate}&end_date=${endDate}`, '_blank');
        }
    </script>
</body>
</html>