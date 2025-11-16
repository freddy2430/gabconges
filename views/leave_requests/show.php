<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Demande - Administration</title>
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
        .request-details-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .user-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
        .status-badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
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
                        <a href="index.php?action=leave_requests" class="nav-link active">
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
                                    <h2 class="mb-1">Détails de la Demande</h2>
                                    <p class="text-muted mb-0">Informations détaillées de la demande de congé</p>
                                </div>
                                <a href="index.php?action=leave_requests" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Request Details -->
                    <div class="row">
                        <!-- User Info -->
                        <div class="col-lg-4 mb-4">
                            <div class="user-info-card card">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 80px; height: 80px;">
                                        <span class="fs-1 text-white">
                                            <?php echo strtoupper(substr($request['first_name'], 0, 1) . substr($request['last_name'], 0, 1)); ?>
                                        </span>
                                    </div>
                                    <h5 class="card-title text-white mb-1">
                                        <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>
                                    </h5>
                                    <p class="text-white-50 mb-2">@<?php echo htmlspecialchars($request['username']); ?></p>
                                    <span class="badge bg-white bg-opacity-25">
                                        Demande #<?php echo $request['id']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Request Details -->
                        <div class="col-lg-8">
                            <div class="request-details-card card">
                                <div class="card-body p-4">
                                    <div class="row mb-4">
                                        <div class="col-sm-6">
                                            <h6 class="text-muted mb-2">Type de congé</h6>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-tag form-icon me-2"></i>
                                                <span class="h5 mb-0"><?php echo htmlspecialchars($request['leave_type_name']); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <h6 class="text-muted mb-2">Statut</h6>
                                            <?php if ($request['status'] === 'pending'): ?>
                                                <span class="badge status-badge bg-warning">
                                                    <i class="bi bi-clock me-1"></i>En attente
                                                </span>
                                            <?php elseif ($request['status'] === 'approved'): ?>
                                                <span class="badge status-badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Approuvée
                                                </span>
                                            <?php else: ?>
                                                <span class="badge status-badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Rejetée
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-sm-6">
                                            <h6 class="text-muted mb-2">Date de début</h6>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-plus form-icon me-2"></i>
                                                <span class="h5 mb-0"><?php echo date('d/m/Y', strtotime($request['start_date'])); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <h6 class="text-muted mb-2">Date de fin</h6>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-minus form-icon me-2"></i>
                                                <span class="h5 mb-0"><?php echo date('d/m/Y', strtotime($request['end_date'])); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-sm-6">
                                            <h6 class="text-muted mb-2">Durée demandée</h6>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-hourglass form-icon me-2"></i>
                                                <span class="h5 mb-0 text-primary"><?php echo $request['requested_days']; ?> jours ouvrés</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <h6 class="text-muted mb-2">Date de création</h6>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-event form-icon me-2"></i>
                                                <span class="h5 mb-0"><?php echo date('d/m/Y à H:i', strtotime($request['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($request['status'] === 'approved' && $request['approved_at']): ?>
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="text-muted mb-2">Informations d'approbation</h6>
                                                <div class="alert alert-success">
                                                    <i class="bi bi-check-circle me-2"></i>
                                                    Approuvée le <?php echo date('d/m/Y à H:i', strtotime($request['approved_at'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($request['status'] === 'rejected' && $request['rejection_reason']): ?>
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="text-muted mb-2">Raison du rejet</h6>
                                                <div class="alert alert-danger">
                                                    <i class="bi bi-x-circle me-2"></i>
                                                    <?php echo htmlspecialchars($request['rejection_reason']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mb-4">
                                        <h6 class="text-muted mb-2">Motif de la demande</h6>
                                        <div class="border rounded p-3 bg-light">
                                            <i class="bi bi-chat-quote form-icon me-2"></i>
                                            <?php echo htmlspecialchars($request['reason']); ?>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <?php if ($request['status'] === 'pending'): ?>
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button"
                                                    class="btn btn-success"
                                                    onclick="approveRequest(<?php echo $request['id']; ?>)">
                                                <i class="bi bi-check-circle me-2"></i>Approuver la demande
                                            </button>
                                            <button type="button"
                                                    class="btn btn-danger"
                                                    onclick="rejectRequest(<?php echo $request['id']; ?>)">
                                                <i class="bi bi-x-circle me-2"></i>Rejeter la demande
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approuver la demande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir approuver cette demande de congé ?</p>
                    <div class="alert alert-info">
                        <strong>Demande :</strong> <?php echo htmlspecialchars($request['leave_type_name']); ?><br>
                        <strong>Période :</strong> du <?php echo date('d/m/Y', strtotime($request['start_date'])); ?> au <?php echo date('d/m/Y', strtotime($request['end_date'])); ?><br>
                        <strong>Durée :</strong> <?php echo $request['requested_days']; ?> jours ouvrés
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a href="#" id="confirmApproveBtn" class="btn btn-success">Approuver</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rejeter la demande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Raison du rejet (optionnel)</label>
                        <textarea class="form-control" id="rejection_reason" rows="3" placeholder="Expliquez pourquoi cette demande est rejetée..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" onclick="confirmReject()">Rejeter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentRequestId = <?php echo $request['id']; ?>;

        function approveRequest(requestId) {
            document.getElementById('confirmApproveBtn').href = 'index.php?action=leave_request_approve&id=' + requestId;
            new bootstrap.Modal(document.getElementById('approveModal')).show();
        }

        function rejectRequest(requestId) {
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }

        function confirmReject() {
            const reason = document.getElementById('rejection_reason').value;
            const url = 'index.php?action=leave_request_reject&id=' + currentRequestId;

            if (reason.trim()) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'rejection_reason';
                input.value = reason;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            } else {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>