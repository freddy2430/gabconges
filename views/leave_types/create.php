<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Type de Congé - Administration</title>
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
                                    <h2 class="mb-1">Nouveau Type de Congé</h2>
                                    <p class="text-muted mb-0">Créer un nouveau type de congé pour les employés</p>
                                </div>
                                <a href="index.php?action=leave_types" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    <?php if (isset($_SESSION['create_leave_type_errors'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Erreurs de validation :</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($_SESSION['create_leave_type_errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['create_leave_type_errors']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['create_leave_type_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['create_leave_type_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['create_leave_type_error']); ?>
                    <?php endif; ?>

                    <!-- Create Form -->
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="form-card card">
                                <div class="card-body p-4">
                                    <form method="POST" action="index.php?action=leave_type_create" novalidate>
                                        <div class="mb-4">
                                            <label for="name" class="form-label">
                                                <i class="bi bi-tag form-icon me-2"></i>Nom du type de congé *
                                            </label>
                                            <input type="text"
                                                   class="form-control form-control-lg"
                                                   id="name"
                                                   name="name"
                                                   value="<?php echo htmlspecialchars($_SESSION['create_leave_type_data']['name'] ?? ''); ?>"
                                                   required
                                                   placeholder="Ex: Congé annuel, Maladie, Maternité...">
                                            <div class="form-text">Nom unique pour identifier ce type de congé</div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="description" class="form-label">
                                                <i class="bi bi-card-text form-icon me-2"></i>Description
                                            </label>
                                            <textarea class="form-control"
                                                      id="description"
                                                      name="description"
                                                      rows="3"
                                                      placeholder="Description optionnelle du type de congé..."><?php echo htmlspecialchars($_SESSION['create_leave_type_data']['description'] ?? ''); ?></textarea>
                                            <div class="form-text">Décrivez les caractéristiques de ce type de congé</div>
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
                                                       value="<?php echo htmlspecialchars($_SESSION['create_leave_type_data']['max_days_per_year'] ?? '0'); ?>"
                                                       min="0"
                                                       max="365">
                                                <div class="form-text">0 = illimité</div>
                                            </div>

                                            <div class="col-md-6 mb-4">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           id="requires_approval"
                                                           name="requires_approval"
                                                           <?php echo (($_SESSION['create_leave_type_data']['requires_approval'] ?? 1) == 1) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="requires_approval">
                                                        <i class="bi bi-shield-check form-icon me-2"></i>Approbation requise
                                                    </label>
                                                </div>
                                                <div class="form-text">Les demandes nécessitent l'approbation d'un administrateur</div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="is_active"
                                                       name="is_active"
                                                       <?php echo (($_SESSION['create_leave_type_data']['is_active'] ?? 1) == 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">
                                                    <i class="bi bi-check-circle form-icon me-2"></i>Type de congé actif
                                                </label>
                                            </div>
                                            <div class="form-text">Les employés peuvent demander ce type de congé</div>
                                        </div>

                                        <!-- Preview Card -->
                                        <div class="mb-4">
                                            <h6 class="mb-3">
                                                <i class="bi bi-eye form-icon me-2"></i>Aperçu
                                            </h6>
                                            <div class="border rounded p-3 bg-light">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <i class="bi bi-tag me-1"></i>
                                                            <span id="preview-name">Nouveau type</span>
                                                        </h6>
                                                        <p class="mb-2 text-muted" id="preview-description">Description...</p>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-success status-badge mb-2">
                                                            <i class="bi bi-check-circle me-1"></i>Actif
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">Max: <span id="preview-days">0 jours</span></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="index.php?action=leave_types" class="btn btn-outline-secondary me-md-2">
                                                <i class="bi bi-x-circle me-2"></i>Annuler
                                            </a>
                                            <button type="submit" class="btn btn-save text-white">
                                                <i class="bi bi-plus-circle me-2"></i>Créer le type de congé
                                            </button>
                                        </div>
                                    </form>
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
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const descriptionInput = document.getElementById('description');
            const maxDaysInput = document.getElementById('max_days_per_year');
            const requiresApprovalInput = document.getElementById('requires_approval');
            const isActiveInput = document.getElementById('is_active');

            const previewName = document.getElementById('preview-name');
            const previewDescription = document.getElementById('preview-description');
            const previewDays = document.getElementById('preview-days');

            function updatePreview() {
                previewName.textContent = nameInput.value || 'Nouveau type';
                previewDescription.textContent = descriptionInput.value || 'Description...';

                const maxDays = parseInt(maxDaysInput.value) || 0;
                previewDays.textContent = maxDays > 0 ? maxDays + ' jours' : 'Illimité';
            }

            // Mettre à jour l'aperçu en temps réel
            nameInput.addEventListener('input', updatePreview);
            descriptionInput.addEventListener('input', updatePreview);
            maxDaysInput.addEventListener('input', updatePreview);

            // Mettre à jour l'aperçu initial
            updatePreview();

            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>