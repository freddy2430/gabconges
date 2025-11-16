<?php
$pageTitle = 'Tableau de Bord - Mes Congés';
$activePage = 'dashboard';
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
                            <i class="bi bi-sunrise me-2"></i>
                                                        Bonjour, <?php echo e($currentUser['first_name']); ?> !
                                                    </h2>
                                                    <p class="card-text mb-0 opacity-75">
                                                        Voici un aperçu de vos congés et demandes en cours.
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
                            
                            <!-- Statistics Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <div class="stats-card card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-calendar-check text-primary icon-large mb-3"></i>
                                            <h3 class="card-title text-primary mb-2"><?php echo e($stats['total_requests']); ?></h3>
                                            <p class="card-text text-muted mb-0">Total demandes</p>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-3 mb-3">
                                    <div class="stats-card card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-check-circle text-success icon-large mb-3"></i>
                                            <h3 class="card-title text-success mb-2"><?php echo e($stats['approved_requests']); ?></h3>
                                            <p class="card-text text-muted mb-0">Approuvées</p>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-3 mb-3">
                                    <div class="stats-card card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-clock text-warning icon-large mb-3"></i>
                                            <h3 class="card-title text-warning mb-2"><?php echo e($stats['pending_requests']); ?></h3>
                                            <p class="card-text text-muted mb-0">En attente</p>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-3 mb-3">
                                    <div class="stats-card card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-calendar text-info icon-large mb-3"></i>
                                            <h3 class="card-title text-info mb-2"><?php echo e($stats['total_days_approved']); ?></h3>
                                            <p class="card-text text-muted mb-0">Jours approuvés</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Remaining Days -->
                                <div class="col-lg-6 mb-4">
                                    <div class="remaining-days-card card">
                                        <div class="card-body p-4">
                                            <h5 class="card-title mb-3">
                                                <i class="bi bi-hourglass-split me-2"></i>
                                                Jours de congés restants (<?php echo date('Y'); ?>)
                                            </h5>
                            
                                            <?php if (empty($remainingDays)):
                                            ?>
                                                <p class="text-white-50 mb-0">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    Aucun quota de congés défini pour cette année.
                                                </p>
                                            <?php else:
                                            ?>
                                                <div class="row">
                                                    <?php foreach ($remainingDays as $days): ?>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="bg-white bg-opacity-25 rounded p-3">
                                                                <h6 class="text-white mb-1"><?php echo e($days['type_name']); ?></h6>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <span class="small">Utilisés: <?php echo e($days['used_days']); ?>/<?php echo e($days['max_days']); ?></span>
                                                                    <span class="badge bg-white bg-opacity-75 text-dark">
                                                                        <?php echo e($days['remaining_days']); ?> restants
                                                                    </span>
                                                                </div>
                                                                <div class="progress mt-2" style="height: 6px;">
                                                                    <div class="progress-bar bg-white bg-opacity-75"
                                                                         style="width: <?php echo e(($days['used_days'] / $days['max_days']) * 100); ?>%">
                                                                    </div>
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
                                                    <i class="bi bi-clock-history me-2 text-gradient"></i>
                                                    Mes dernières demandes
                                                </h5>
                                                <a href="index.php?action=my_requests" class="btn btn-sm btn-outline-primary">
                                                    Voir tout
                                                </a>
                                            </div>
                            
                                            <?php if (empty($recentRequests)):
                                            ?>
                                                <div class="text-center py-4">
                                                    <i class="bi bi-calendar-x text-muted mb-2" style="font-size: 2rem;"></i>
                                                    <p class="text-muted mb-0">Aucune demande récente</p>
                                                </div>
                                            <?php else:
                                            ?>
                                                <div class="list-group list-group-flush">
                                                    <?php foreach ($recentRequests as $request): ?>
                                                        <div class="list-group-item px-0">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="badge bg-info me-2">
                                                                            <?php echo e($request['leave_type_name']); ?>
                                                                        </span>
                                                                        <small class="text-muted">
                                                                            <?php echo date('d/m/Y', strtotime($request['start_date'])); ?> -
                                                                            <?php echo date('d/m/Y', strtotime($request['end_date'])); ?>
                                                                        </small>
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        <?php echo e($request['requested_days']); ?> jours • Créée le <?php echo date('d/m', strtotime($request['created_at'])); ?>
                                                                    </small>
                                                                </div>
                                                                <div class="text-end">
                                                                    <?php if ($request['status'] === 'pending'): ?>
                                                                        <span class="badge bg-warning status-badge">
                                                                            <i class="bi bi-clock me-1"></i>En attente
                                                                        </span>
                                                                    <?php elseif ($request['status'] === 'approved'): ?>
                                                                        <span class="badge bg-success status-badge">
                                                                            <i class="bi bi-check-circle me-1"></i>Approuvée
                                                                        </span>
                                                                    <?php else:
                                                                    ?>
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
                    <div class="col-md-4 mb-3">
                        <a href="index.php?action=leave_request_create" class="btn btn-primary w-100 p-3">
                            <i class="bi bi-plus-circle me-2" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong>Nouvelle demande</strong>
                                <br>
                                <small>Créer une demande de congé</small>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="index.php?action=my_requests" class="btn btn-outline-primary w-100 p-3">
                            <i class="bi bi-calendar-check me-2" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong>Mes demandes</strong>
                                <br>
                                <small>Voir toutes mes demandes</small>
                            </div>
                        </a>
                    </div>

                    <?php if ($this->authController->isAdmin()): ?>
                        <div class="col-md-4 mb-3">
                            <a href="index.php?action=admin_dashboard" class="btn btn-outline-secondary w-100 p-3">
                                <i class="bi bi-speedometer2 me-2" style="font-size: 1.2rem;"></i>
                                <div>
                                    <strong>Administration</strong>
                                    <br>
                                    <small>Accéder au panneau admin</small>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>