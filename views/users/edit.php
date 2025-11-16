<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur - Administration</title>
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
        .user-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
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
                                    <h2 class="mb-1">Modifier Utilisateur</h2>
                                    <p class="text-muted mb-0">Modifier les informations de <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                </div>
                                <a href="index.php?action=users" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Confirmation Alert -->
                    <?php if (isset($_GET['delete']) && $_GET['delete'] === 'confirm'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> Vous êtes sur le point de supprimer l'utilisateur
                            <strong><?php echo htmlspecialchars($_SESSION['delete_user_name'] ?? ''); ?></strong>.
                            <br>Cette action est irréversible.
                            <div class="mt-2">
                                <a href="index.php?action=user_delete&id=<?php echo $user['id']; ?>&confirm=yes"
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
                    <?php if (isset($_SESSION['edit_user_errors'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Erreurs de validation :</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($_SESSION['edit_user_errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['edit_user_errors']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['edit_user_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['edit_user_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['edit_user_error']); ?>
                    <?php endif; ?>

                    <div class="row">
                        <!-- User Info Card -->
                        <div class="col-lg-4 mb-4">
                            <div class="user-info-card card">
                                <div class="card-body text-center p-4">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 80px; height: 80px;">
                                        <span class="fs-1 text-white">
                                            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                                        </span>
                                    </div>
                                    <h5 class="card-title text-white mb-1">
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                    </h5>
                                    <p class="text-white-50 mb-2">@<?php echo htmlspecialchars($user['username']); ?></p>
                                    <span class="badge bg-white bg-opacity-25">
                                        <?php echo $user['role'] === 'admin' ? 'Administrateur' : 'Employé'; ?>
                                    </span>
                                    <div class="mt-3">
                                        <small class="text-white-50">
                                            Créé le <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <div class="col-lg-8">
                            <div class="form-card card">
                                <div class="card-body p-4">
                                    <form method="POST" action="index.php?action=user_update" novalidate>
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="first_name" class="form-label">
                                                    <i class="bi bi-person me-2"></i>Prénom *
                                                </label>
                                                <input type="text"
                                                       class="form-control"
                                                       id="first_name"
                                                       name="first_name"
                                                       value="<?php echo htmlspecialchars($_SESSION['edit_user_data']['first_name'] ?? $user['first_name']); ?>"
                                                       required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="last_name" class="form-label">
                                                    <i class="bi bi-person me-2"></i>Nom de famille *
                                                </label>
                                                <input type="text"
                                                       class="form-control"
                                                       id="last_name"
                                                       name="last_name"
                                                       value="<?php echo htmlspecialchars($_SESSION['edit_user_data']['last_name'] ?? $user['last_name']); ?>"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="username" class="form-label">
                                                <i class="bi bi-at me-2"></i>Nom d'utilisateur *
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="username"
                                                   name="username"
                                                   value="<?php echo htmlspecialchars($_SESSION['edit_user_data']['username'] ?? $user['username']); ?>"
                                                   required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">
                                                <i class="bi bi-envelope me-2"></i>Email *
                                            </label>
                                            <input type="email"
                                                   class="form-control"
                                                   id="email"
                                                   name="email"
                                                   value="<?php echo htmlspecialchars($_SESSION['edit_user_data']['email'] ?? $user['email']); ?>"
                                                   required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="role" class="form-label">
                                                    <i class="bi bi-shield me-2"></i>Rôle
                                                </label>
                                                <select class="form-control" id="role" name="role">
                                                    <option value="employee" <?php echo (($user['role'] === 'employee') ? 'selected' : ''); ?>>
                                                        Employé
                                                    </option>
                                                    <option value="admin" <?php echo (($user['role'] === 'admin') ? 'selected' : ''); ?>>
                                                        Administrateur
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           id="is_active"
                                                           name="is_active"
                                                           <?php echo (($user['is_active']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="is_active">
                                                        Compte actif
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="index.php?action=users" class="btn btn-outline-secondary me-md-2">
                                                <i class="bi bi-x-circle me-2"></i>Annuler
                                            </a>
                                            <button type="submit" class="btn btn-save text-white">
                                                <i class="bi bi-check-circle me-2"></i>Enregistrer les modifications
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Danger Zone -->
                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                        <hr class="my-4">
                                        <div class="alert alert-danger">
                                            <h6 class="alert-heading">
                                                <i class="bi bi-exclamation-triangle me-2"></i>Zone de danger
                                            </h6>
                                            <p class="mb-2">La suppression d'un utilisateur est irréversible.</p>
                                            <a href="index.php?action=user_edit&id=<?php echo $user['id']; ?>&delete=confirm"
                                               class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash me-1"></i>Supprimer cet utilisateur
                                            </a>
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