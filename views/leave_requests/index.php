<?php
$pageTitle = 'Demandes de Congés - Administration';
$activePage = 'leave_requests';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Demandes de Congés</h2>
                <p class="text-muted mb-0">Gérez les demandes de congés des employés</p>
            </div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel me-2"></i>Filtrer par statut
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item <?php echo !isset($_GET['status']) ? 'active' : ''; ?>" href="index.php?action=leave_requests">
                                Toutes les demandes
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'active' : ''; ?>" href="index.php?action=leave_requests&status=pending">
                                <i class="bi bi-clock me-2"></i>En attente
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo (isset($_GET['status']) && $_GET['status'] === 'approved') ? 'active' : ''; ?>" href="index.php?action=leave_requests&status=approved">
                                <i class="bi bi-check-circle me-2"></i>Approuvées
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo (isset($_GET['status']) && $_GET['status'] === 'rejected') ? 'active' : ''; ?>" href="index.php?action=leave_requests&status=rejected">
                                <i class="bi bi-x-circle me-2"></i>Rejetées
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
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
                <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                <h4 class="text-muted">
                    <?php if (isset($_GET['status'])): ?>
                        Aucune demande <?php echo $_GET['status'] === 'pending' ? 'en attente' : ($_GET['status'] === 'approved' ? 'approuvée' : 'rejetée'); ?>
                    <?php else: ?>
                        Aucune demande de congé
                    <?php endif; ?>
                </h4>
                <p class="text-muted">Les demandes apparaîtront ici une fois créées par les employés.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($leaveRequests as $request): ?>
            <div class="col-12 mb-3">
                <div class="card request-card <?php echo $request['status'] === 'pending' ? 'priority-high' : ($request['status'] === 'approved' ? 'priority-low' : 'priority-medium'); ?>">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 50px; height: 50px;">
                                        <?php echo strtoupper(substr($request['first_name'], 0, 1) . substr($request['last_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></h6>
                                        <small class="text-muted">@<?php echo htmlspecialchars($request['username']); ?></small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <span class="badge bg-info">
                                    <i class="bi bi-tag me-1"></i>
                                    <?php echo htmlspecialchars($request['leave_type_name']); ?>
                                </span>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-1">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <strong><?php echo date('d/m/Y', strtotime($request['start_date'])); ?></strong>
                                    au <strong><?php echo date('d/m/Y', strtotime($request['end_date'])); ?></strong>
                                </div>
                                <small class="text-muted">
                                    <?php echo $request['requested_days']; ?> jour<?php echo $request['requested_days'] > 1 ? 's' : ''; ?> ouvré<?php echo $request['requested_days'] > 1 ? 's' : ''; ?>
                                </small>
                            </div>

                            <div class="col-md-2">
                                <?php if ($request['status'] === 'pending'): ?>
                                    <span class="badge bg-warning status-badge">
                                        <i class="bi bi-clock me-1"></i>En attente
                                    </span>
                                <?php elseif ($request['status'] === 'approved'): ?>
                                    <span class="badge bg-success status-badge">
                                        <i class="bi bi-check-circle me-1"></i>Approuvée
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger status-badge">
                                        <i class="bi bi-x-circle me-1"></i>Rejetée
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-2">
                                <div class="btn-group" role="group">
                                    <a href="index.php?action=leave_request_show&id=<?php echo $request['id']; ?>"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Voir les détails">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <?php if ($request['status'] === 'pending'): ?>
                                        <button type="button"
                                                class="btn btn-sm btn-success"
                                                onclick="approveRequest(<?php echo $request['id']; ?>)"
                                                title="Approuver">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                onclick="rejectRequest(<?php echo $request['id']; ?>)"
                                                title="Rejeter">
                                            <i class="bi bi-x"></i>
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
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <nav aria-label="Pagination demandes" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?action=leave_requests&page=<?php echo $page - 1; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="index.php?action=leave_requests&page=<?php echo $i; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?action=leave_requests&page=<?php echo $page + 1; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

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

<script>
    let currentRequestId = null;

    function approveRequest(requestId) {
        currentRequestId = requestId;
        document.getElementById('confirmApproveBtn').href = 'index.php?action=leave_request_approve&id=' + requestId;
        new bootstrap.Modal(document.getElementById('approveModal')).show();
    }

    function rejectRequest(requestId) {
        currentRequestId = requestId;
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

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>