<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Demandes de Congés</title>
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
        .request-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
        }
        .request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
            font-size: 0.75rem;
        }
        .status-pending {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }
        .status-approved {
            background: linear-gradient(135deg, #d1edff 0%, #bee5eb 100%);
            color: #0c5460;
        }
        .status-rejected {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
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
                        <i class="bi bi-person me-2"></i>Mes congés
                    </h5>
                    <nav class="nav nav-pills flex-column">
                        <a href="index.php?action=dashboard" class="nav-link active">
                            <i class="bi bi-house me-2"></i>Tableau de bord
                        </a>
                        <a href="index.php?action=my_requests" class="nav-link">
                            <i class="bi bi-calendar-check me-2"></i>Mes demandes
                        </a>
                        <a href="index.php?action=leave_request_create" class="nav-link">
                            <i class="bi bi-plus-circle me-2"></i>Nouvelle demande
                        </a>
                        <hr class="text-white-50">
                        <a href="index.php?action=admin_dashboard" class="nav-link">
                            <i class="bi bi-speedometer2 me-2"></i>Administration
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
                                    <h2 class="mb-1">Mes Demandes de Congés</h2>
                                    <p class="text-muted mb-0">Gérez vos demandes de congés</p>
                                </div>
                                <a href="index.php?action=leave_request_create" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Nouvelle demande
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

                    <!-- Requests List -->
                    <div class="row">
                        <?php if (empty($leaveRequests)): ?>
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-plus display-1 text-muted mb-3"></i>
                                    <h4 class="text-muted">Aucune demande de congé</h4>
                                    <p class="text-muted mb-4">Vous n'avez pas encore créé de demande de congé.</p>
                                    <a href="index.php?action=leave_request_create" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Créer ma première demande
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($leaveRequests as $request): ?>
                                <div class="col-12 mb-3">
                                    <div class="card request-card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                             style="width: 40px; height: 40px;">
                                                            <i class="bi bi-calendar-event"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0"><?php echo htmlspecialchars($request['leave_type_name']); ?></h6>
                                                            <small class="text-muted">
                                                                Créée le <?php echo date('d/m/Y', strtotime($request['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mb-1">
                                                        <i class="bi bi-calendar-range me-1"></i>
                                                        <strong><?php echo date('d/m/Y', strtotime($request['start_date'])); ?></strong>
                                                        au <strong><?php echo date('d/m/Y', strtotime($request['end_date'])); ?></strong>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo $request['requested_days']; ?> jour<?php echo $request['requested_days'] > 1 ? 's' : ''; ?> ouvré<?php echo $request['requested_days'] > 1 ? 's' : ''; ?>
                                                    </small>
                                                </div>

                                                <div class="col-md-2">
                                                    <?php if ($request['status'] === 'pending'): ?>
                                                        <span class="badge status-badge status-pending">
                                                            <i class="bi bi-clock me-1"></i>En attente
                                                        </span>
                                                    <?php elseif ($request['status'] === 'approved'): ?>
                                                        <span class="badge status-badge status-approved">
                                                            <i class="bi bi-check-circle me-1"></i>Approuvée
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge status-badge status-rejected">
                                                            <i class="bi bi-x-circle me-1"></i>Rejetée
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="btn-group" role="group">
                                                        <a href="index.php?action=leave_request_show&id=<?php echo $request['id']; ?>"
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Voir les détails">
                                                            <i class="bi bi-eye"></i>
                                                        </a>

                                                        <?php if ($request['status'] === 'pending'): ?>
                                                            <a href="index.php?action=leave_request_edit&id=<?php echo $request['id']; ?>"
                                                               class="btn btn-sm btn-outline-secondary"
                                                               title="Modifier">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    onclick="confirmDelete(<?php echo $request['id']; ?>)"
                                                                    title="Supprimer">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <?php if ($request['status'] === 'rejected' && !empty($request['rejection_reason'])): ?>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-info"
                                                                    onclick="showRejectionReason('<?php echo htmlspecialchars($request['rejection_reason']); ?>')"
                                                                    title="Raison du rejet">
                                                                <i class="bi bi-question-circle"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (!empty($request['reason'])): ?>
                                                <div class="row mt-3">
                                                    <div class="col-12">
                                                        <hr class="my-2">
                                                        <small class="text-muted">
                                                            <i class="bi bi-chat-quote me-1"></i>
                                                            <strong>Motif :</strong> <?php echo htmlspecialchars($request['reason']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($request['status'] === 'approved' && isset($request['approved_by'])): ?>
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <small class="text-success">
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            Approuvée le <?php echo date('d/m/Y à H:i', strtotime($request['approved_at'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Pagination mes demandes" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?action=my_requests&page=<?php echo $page - 1; ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="index.php?action=my_requests&page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?action=my_requests&page=<?php echo $page + 1; ?>">
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
                    <p>Êtes-vous sûr de vouloir supprimer cette demande de congé ?</p>
                    <p class="text-danger"><small>Cette action est irréversible.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Raison du rejet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="rejectionReasonText"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(requestId) {
            document.getElementById('confirmDeleteBtn').href = 'index.php?action=leave_request_delete&id=' + requestId;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function showRejectionReason(reason) {
            document.getElementById('rejectionReasonText').textContent = reason;
            new bootstrap.Modal(document.getElementById('rejectionModal')).show();
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