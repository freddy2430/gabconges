<?php
$pageTitle = 'Tableau de Bord Administrateur';
$activePage = 'admin_dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Welcome Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-card card">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="card-title mb-2">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Tableau de bord administrateur
                        </h2>
                        <p class="card-text mb-0 opacity-75">
                            Vue d'ensemble du système de congésGAB.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="h4 mb-0">
                            <?php echo date('d F Y'); ?>
                        </div>
                        <small class="opacity-75"><?php echo date('H:i'); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Urgent Actions -->
<?php if ($generalStats['pending_requests'] > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="urgent-card card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Actions urgentes requises
                        </h5>
                        <p class="card-text mb-0">
                            <?php echo $generalStats['pending_requests']; ?> demande<?php echo $generalStats['pending_requests'] > 1 ? 's' : ''; ?> de congé en attente d'approbation.
                        </p>
                    </div>
                    <a href="index.php?action=leave_requests&status=pending" class="btn btn-light">
                        <i class="bi bi-arrow-right me-2"></i>Voir les demandes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card card">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check text-primary icon-large mb-3"></i>
                <h3 class="card-title text-primary mb-2"><?php echo $generalStats['total_requests']; ?></h3>
                <p class="card-text text-muted mb-0">Total demandes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card card">
            <div class="card-body text-center">
                <i class="bi bi-check-circle text-success icon-large mb-3"></i>
                <h3 class="card-title text-success mb-2"><?php echo $generalStats['approved_requests']; ?></h3>
                <p class="card-text text-muted mb-0">Approuvées</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card card">
            <div class="card-body text-center">
                <i class="bi bi-clock text-warning icon-large mb-3"></i>
                <h3 class="card-title text-warning mb-2"><?php echo $generalStats['pending_requests']; ?></h3>
                <p class="card-text text-muted mb-0">En attente</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card card">
            <div class="card-body text-center">
                <i class="bi bi-calendar text-info icon-large mb-3"></i>
                <h3 class="card-title text-info mb-2"><?php echo $generalStats['total_days_approved']; ?></h3>
                <p class="card-text text-muted mb-0">Jours approuvés</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pending Requests -->
    <div class="col-lg-6 mb-4">
        <div class="stats-card card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2 text-gradient"></i>
                        Demandes en attente
                    </h5>
                    <a href="index.php?action=leave_requests&status=pending" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <?php if (empty($pendingRequests)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0">Aucune demande en attente</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($pendingRequests as $request): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($request['first_name'], 0, 1) . substr($request['last_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></strong>
                                        <br>
                                        <small class="text-muted">@<?php echo htmlspecialchars($request['username']); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-info me-2">
                                    <?php echo htmlspecialchars($request['leave_type_name']); ?>
                                </span>
                                <small class="text-muted">
                                    <?php echo $request['requested_days']; ?> jours
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="col-lg-6 mb-4">
        <div class="stats-card card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity me-2 text-gradient"></i>
                        Activité récente
                    </h5>
                    <a href="index.php?action=leave_requests" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <?php if (empty($recentRequests)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0">Aucune activité récente</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentRequests as $request): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($request['first_name'], 0, 1) . substr($request['last_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></strong>
                                        <br>
                                        <small class="text-muted">@<?php echo htmlspecialchars($request['username']); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-info me-2">
                                    <?php echo htmlspecialchars($request['leave_type_name']); ?>
                                </span>
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
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="stats-card card">
            <div class="card-body p-4">
                <h5 class="card-title mb-3">
                    <i class="bi bi-lightning-charge me-2 text-gradient"></i>
                    Actions rapides
                </h5>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="index.php?action=user_create" class="btn btn-primary w-100 p-3">
                            <i class="bi bi-person-plus me-2" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong>Nouvel utilisateur</strong>
                                <br>
                                <small>Créer un compte</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?action=leave_type_create" class="btn btn-outline-primary w-100 p-3">
                            <i class="bi bi-tag me-2" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong>Nouveau type</strong>
                                <br>
                                <small>Ajouter un type de congé</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?action=leave_requests&status=pending" class="btn btn-outline-warning w-100 p-3">
                            <i class="bi bi-clock me-2" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong>Demandes en attente</strong>
                                <br>
                                <small><?php echo $generalStats['pending_requests']; ?> à traiter</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?action=statistics" class="btn btn-outline-info w-100 p-3">
                            <i class="bi bi-bar-chart me-2" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong>Statistiques</strong>
                                <br>
                                <small>Voir les rapports</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
