<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Administration</title>
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
        .user-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
            font-size: 0.75rem;
        }
        .role-badge {
            font-size: 0.75rem;
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
                        <a href="index.php?action=users" class="nav-link active">
                            <i class="bi bi-people me-2"></i>Utilisateurs
                        </a>
                        <a href="index.php?action=leave_types" class="nav-link">
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
                                    <h2 class="mb-1">Gestion des Utilisateurs</h2>
                                    <p class="text-muted mb-0">Gérez les comptes utilisateurs du système</p>
                                </div>
                                <a href="index.php?action=user_create" class="btn btn-primary">
                                    <i class="bi bi-person-plus me-2"></i>Nouvel utilisateur
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

                    <!-- Users Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Utilisateur</th>
                                                    <th>Email</th>
                                                    <th>Rôle</th>
                                                    <th>Statut</th>
                                                    <th>Dernière activité</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($users)): ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4 text-muted">
                                                            <i class="bi bi-people me-2"></i>
                                                            Aucun utilisateur trouvé
                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($users as $user): ?>
                                                        <tr class="user-card">
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                         style="width: 40px; height: 40px;">
                                                                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                                                                    </div>
                                                                    <div>
                                                                        <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                                                        <br>
                                                                        <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                            <td>
                                                                <?php if ($user['role'] === 'admin'): ?>
                                                                    <span class="badge bg-danger role-badge">
                                                                        <i class="bi bi-shield-check me-1"></i>Administrateur
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-info role-badge">
                                                                        <i class="bi bi-person me-1"></i>Employé
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($user['is_active']): ?>
                                                                    <span class="badge bg-success status-badge">
                                                                        <i class="bi bi-check-circle me-1"></i>Actif
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary status-badge">
                                                                        <i class="bi bi-pause-circle me-1"></i>Inactif
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">
                                                                    <?php
                                                                    if ($user['updated_at'] !== $user['created_at']) {
                                                                        echo 'Modifié ' . date('d/m/Y', strtotime($user['updated_at']));
                                                                    } else {
                                                                        echo 'Créé ' . date('d/m/Y', strtotime($user['created_at']));
                                                                    }
                                                                    ?>
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="index.php?action=user_edit&id=<?php echo $user['id']; ?>"
                                                                       class="btn btn-sm btn-outline-primary"
                                                                       title="Modifier">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <a href="index.php?action=user_toggle_status&id=<?php echo $user['id']; ?>"
                                                                       class="btn btn-sm <?php echo $user['is_active'] ? 'btn-outline-warning' : 'btn-outline-success'; ?>"
                                                                       title="<?php echo $user['is_active'] ? 'Désactiver' : 'Activer'; ?>">
                                                                        <i class="bi <?php echo $user['is_active'] ? 'bi-pause' : 'bi-play'; ?>"></i>
                                                                    </a>
                                                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                                        <button type="button"
                                                                                class="btn btn-sm btn-outline-danger"
                                                                                onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')"
                                                                                title="Supprimer">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <?php if ($totalPages > 1): ?>
                                        <nav aria-label="Pagination utilisateurs" class="mt-4">
                                            <ul class="pagination justify-content-center">
                                                <?php if ($page > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="index.php?action=users&page=<?php echo $page - 1; ?>">
                                                            <i class="bi bi-chevron-left"></i>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>

                                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                        <a class="page-link" href="index.php?action=users&page=<?php echo $i; ?>">
                                                            <?php echo $i; ?>
                                                        </a>
                                                    </li>
                                                <?php endfor; ?>

                                                <?php if ($page < $totalPages): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="index.php?action=users&page=<?php echo $page + 1; ?>">
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
                    <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="deleteUserName"></strong> ?</p>
                    <p class="text-danger"><small>Cette action est irréversible.</small></p>
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
        function confirmDelete(userId, userName) {
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('confirmDeleteBtn').href = 'index.php?action=user_delete&id=' + userId + '&confirm=yes';
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