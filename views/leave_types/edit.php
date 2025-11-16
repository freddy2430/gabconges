<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Type de Congé - Administration</title>
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
        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                        <a href="index.php?action=leave_types" class="nav-link active">
                            <i class="bi bi-tags me-2"></i>Types de congés
                        </a>
                        <a href="index.php?action=leave_requests" class="nav-link">
                            <i class="bi bi-calendar-check me-2"></i>Demandes
                        </a>
                        <a href="index.php?action=statistics" class="nav-link">
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
                                    <h2 class="mb-1">Modifier Type de Congé</h2>
                                    <p class="text-muted mb-0">Modifier <?php echo htmlspecialchars($leaveType['name']); ?></p>
                                </div>
                                <a href="index.php?action=leave_types" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Confirmation Alert -->
                    <?php if (isset($_GET['delete']) && $_GET['delete'] === 'confirm'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> Vous êtes sur le point de supprimer le type de congé
                            <strong><?php echo htmlspecialchars($_SESSION['delete_leave_type_name'] ?? ''); ?></strong>.
                            <br>Cette action est irréversible.
                            <div class="mt-2">
                                <a href="index.php?action=leave_type_delete&id=<?php echo $leaveType['id']; ?>&confirm=yes"
                                   class="btn btn-sm btn-danger me-2">
                                    <i class="bi bi-trash me-1"></i>Confirmer la suppression
                                </a>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="window.history.back()">
                                    Annuler
                                </button>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Error Messages -->
                    <?php if (isset($_SESSION['edit_leave_type_errors'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Erreurs de validation :</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($_SESSION['edit_leave_type_errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['edit_leave_type_errors']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['edit_leave_type_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['edit_leave_type_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['edit_leave_type_error']); ?>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Statistics Card -->
                        <div class="col-lg-4 mb-4">
                            <div class="stats-card card">
                                <div class="card-body p-4">
                                    <h6 class="card-title text-white mb-3">
                                        <i class="bi bi-bar-chart me-2"></i>Statistiques d'utilisation
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="p-2 bg-white bg-opacity-25 rounded">
                                                <div class="h4 mb-0 text-white"><?php echo $stats['total_requests']; ?></div>
                                                <small class="text-white-50">Total demandes</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 bg-white bg-opacity-25 rounded">
                                                <div class="h4 mb-0 text-white"><?php echo $stats['approved_requests']; ?></div>
                                                <small class="text-white-50">Approuvées</small>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="text-white-50">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="h5 mb-0 text-white"><?php echo $stats['pending_requests']; ?></div>
                                            <small class="text-white-50">En attente</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="h5 mb-0 text-white"><?php echo $stats['rejected_requests']; ?></div>
                                            <small class="text-white-50">Rejetées</small>
                                        </div>
                                    </div>
                                    <?php if ($stats['avg_days'] > 0): ?>
                                        <hr class="text-white-50">
                                        <div class="text-center">
                                            <div class="h6 mb-0 text-white">
                                                <i class="bi bi-calendar me-1"></i>
                                                <?php echo round($stats['avg_days'], 1); ?> jours en moyenne
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <div class="col-lg-8">
                            <div class="form-card card">
                                <div class="card-body p-4">
                                    <form method="POST" action="index.php?action=leave_type_update" novalidate>
                                        <input type="hidden" name="leave_type_id" value="<?php echo $leaveType['id']; ?>">

                                        <div class="mb-4">
                                            <label for="name" class="form-label">
                                                <i class="bi bi-tag form-icon me-2"></i>Nom du type de congé *
                                            </label>
                                            <input type="text"
                                                   class="form-control form-control-lg"
                                                   id="name"
                                                   name="name"
                                                   value="<?php echo htmlspecialchars($_SESSION['edit_leave_type_data']['name'] ?? $leaveType['name']); ?>"
                                                   required>
                                        </div>

                                        <div class="mb-4">
                                            <label for="description" class="form-label">
                                                <i class="bi bi-card-text form-icon me-2"></i>Description
                                            </label>
                                            <textarea class="form-control"
                                                      id="description"
                                                      name="description"
                                                      rows="3"><?php echo htmlspecialchars($_SESSION['edit_leave_type_data']['description'] ?? $leaveType['description']); ?></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label for="max_days_per_year" class="form-label">
                                                    <i class="bi bi-calendar-event form-icon me-2"></i>Jours maximum par an
                                                </label>
                                                <input type="number"
                                                       class="form-control"
                                                       id="max_days_per_year"
                                                       name="max_days_per_year"
                                                       value="<?php echo htmlspecialchars($_SESSION['edit_leave_type_data']['max_days_per_year'] ?? $leaveType['max_days_per_year']); ?>"
                                                       min="0"
                                                       max="365">
                                            </div>

                                            <div class="col-md-6 mb-4">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           id="requires_approval"
                                                           name="requires_approval"
                                                           <?php echo (($leaveType['requires_approval']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="requires_approval">
                                                        <i class="bi bi-shield-check form-icon me-2"></i>Approbation requise
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="is_active"
                                                       name="is_active"
                                                       <?php echo (($leaveType['is_active']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="is_active">
                                                    <i class="bi bi-check-circle form-icon me-2"></i>Type de congé actif
                                                </label>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="index.php?action=leave_types" class="btn btn-outline-secondary me-md-2">
                                                <i class="bi bi-x-circle me-2"></i>Annuler
                                            </a>
                                            <button type="submit" class="btn btn-save text-white">
                                                <i class="bi bi-check-circle me-2"></i>Enregistrer les modifications
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Danger Zone -->
                                    <hr class="my-4">
                                    <div class="alert alert-danger">
                                        <h6 class="alert-heading">
                                            <i class="bi bi-exclamation-triangle me-2"></i>Zone de danger
                                        </h6>
                                        <p class="mb-2">La suppression d'un type de congé est irréversible.</p>
                                        <a href="index.php?action=leave_type_edit&id=<?php echo $leaveType['id']; ?>&delete=confirm"
                                           class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>Supprimer ce type de congé
                                        </a>
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
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>