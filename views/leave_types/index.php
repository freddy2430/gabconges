<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Types de Congés - Administration</title>
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
        .leave-type-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            background: white;
        }
        .leave-type-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
            font-size: 0.75rem;
        }
        .approval-badge {
            font-size: 0.75rem;
        }
        .days-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                                    <h2 class="mb-1">Types de Congés</h2>
                                    <p class="text-muted mb-0">Gérez les différents types de congés disponibles</p>
                                </div>
                                <a href="index.php?action=leave_type_create" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Nouveau type
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Leave Types Grid -->
                    <div class="row">
                        <?php if (empty($leaveTypes)): ?>
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="bi bi-tags display-1 text-muted mb-3"></i>
                                    <h4 class="text-muted">Aucun type de congé</h4>
                                    <p class="text-muted mb-4">Créez votre premier type de congé pour commencer.</p>
                                    <a href="index.php?action=leave_type_create" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Créer le premier type
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($leaveTypes as $type): ?>
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="card leave-type-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h5 class="card-title mb-1">
                                                        <i class="bi bi-tag me-2"></i>
                                                        <?php echo htmlspecialchars($type['name']); ?>
                                                    </h5>
                                                    <?php if (!empty($type['description'])): ?>
                                                        <p class="card-text text-muted small mb-2">
                                                            <?php echo htmlspecialchars($type['description']); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="index.php?action=leave_type_edit&id=<?php echo $type['id']; ?>">
                                                                <i class="bi bi-pencil me-2"></i>Modifier
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="index.php?action=leave_type_toggle_status&id=<?php echo $type['id']; ?>">
                                                                <i class="bi <?php echo $type['is_active'] ? 'bi-pause' : 'bi-play'; ?> me-2"></i>
                                                                <?php echo $type['is_active'] ? 'Désactiver' : 'Activer'; ?>
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button class="dropdown-item text-danger" onclick="confirmDelete(<?php echo $type['id']; ?>, '<?php echo htmlspecialchars($type['name']); ?>')">
                                                                <i class="bi bi-trash me-2"></i>Supprimer
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex gap-2">
                                                    <?php if ($type['is_active']): ?>
                                                        <span class="badge bg-success status-badge">
                                                            <i class="bi bi-check-circle me-1"></i>Actif
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary status-badge">
                                                            <i class="bi bi-pause-circle me-1"></i>Inactif
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if ($type['requires_approval']): ?>
                                                        <span class="badge bg-warning approval-badge">
                                                            <i class="bi bi-shield-check me-1"></i>Approbation requise
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info approval-badge">
                                                            <i class="bi bi-shield-x me-1"></i>Auto-approbation
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Max par an:</small>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($type['max_days_per_year'] > 0): ?>
                                                            <span class="badge days-badge text-white me-2">
                                                                <?php echo $type['max_days_per_year']; ?> jours
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary me-2">Illimité</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="text-end">
                                                    <small class="text-muted">Créé le</small>
                                                    <br>
                                                    <small class="fw-bold">
                                                        <?php echo date('d/m/Y', strtotime($type['created_at'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Pagination types de congés" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?action=leave_types&page=<?php echo $page - 1; ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="index.php?action=leave_types&page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?action=leave_types&page=<?php echo $page + 1; ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer le type de congé <strong id="deleteLeaveTypeName"></strong> ?</p>
                    <p class="text-danger"><small>Cette action est irréversible et supprimera toutes les données associées.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(leaveTypeId, leaveTypeName) {
            document.getElementById('deleteLeaveTypeName').textContent = leaveTypeName;
            document.getElementById('confirmDeleteBtn').href = 'index.php?action=leave_type_delete&id=' + leaveTypeId + '&confirm=yes';
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

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