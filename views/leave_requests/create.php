<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Demande de Congé</title>
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
        .date-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 1rem;
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
                        <a href="index.php?action=dashboard" class="nav-link">
                            <i class="bi bi-house me-2"></i>Tableau de bord
                        </a>
                        <a href="index.php?action=my_requests" class="nav-link">
                            <i class="bi bi-calendar-check me-2"></i>Mes demandes
                        </a>
                        <a href="index.php?action=leave_request_create" class="nav-link active">
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
                                    <h2 class="mb-1">Nouvelle Demande de Congé</h2>
                                    <p class="text-muted mb-0">Créez une nouvelle demande de congé</p>
                                </div>
                                <a href="index.php?action=my_requests" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Retour à mes demandes
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    <?php if (isset($_SESSION['create_request_errors'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Erreurs de validation :</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($_SESSION['create_request_errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['create_request_errors']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['create_request_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['create_request_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['create_request_error']); ?>
                    <?php endif; ?>

                    <!-- Create Form -->
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="form-card card">
                                <div class="card-body p-4">
                                    <form method="POST" action="index.php?action=leave_request_create" novalidate>
                                        <div class="mb-4">
                                            <label for="leave_type_id" class="form-label">
                                                <i class="bi bi-tag form-icon me-2"></i>Type de congé *
                                            </label>
                                            <select class="form-control" id="leave_type_id" name="leave_type_id" required>
                                                <option value="">Choisissez un type de congé</option>
                                                <?php foreach ($leaveTypes as $type): ?>
                                                    <option value="<?php echo $type['id']; ?>"
                                                            <?php echo (($_SESSION['create_request_data']['leave_type_id'] ?? '') == $type['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($type['name']); ?>
                                                        <?php if ($type['max_days_per_year'] > 0): ?>
                                                            (max <?php echo $type['max_days_per_year']; ?> jours/an)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text">Sélectionnez le type de congé souhaité</div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label for="start_date" class="form-label">
                                                    <i class="bi bi-calendar-plus form-icon me-2"></i>Date de début *
                                                </label>
                                                <input type="date"
                                                       class="form-control"
                                                       id="start_date"
                                                       name="start_date"
                                                       value="<?php echo htmlspecialchars($_SESSION['create_request_data']['start_date'] ?? ''); ?>"
                                                       min="<?php echo date('Y-m-d'); ?>"
                                                       required>
                                                <div class="form-text">Premier jour du congé</div>
                                            </div>

                                            <div class="col-md-6 mb-4">
                                                <label for="end_date" class="form-label">
                                                    <i class="bi bi-calendar-minus form-icon me-2"></i>Date de fin *
                                                </label>
                                                <input type="date"
                                                       class="form-control"
                                                       id="end_date"
                                                       name="end_date"
                                                       value="<?php echo htmlspecialchars($_SESSION['create_request_data']['end_date'] ?? ''); ?>"
                                                       min="<?php echo date('Y-m-d'); ?>"
                                                       required>
                                                <div class="form-text">Dernier jour du congé</div>
                                            </div>
                                        </div>

                                        <!-- Date Info -->
                                        <div class="date-info mb-4" id="dateInfo" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>Durée totale :</strong>
                                                    <div id="totalDays" class="h5 text-primary mb-0">0 jours</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Jours ouvrés :</strong>
                                                    <div id="workingDays" class="h5 text-success mb-0">0 jours</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Weekends :</strong>
                                                    <div id="weekendDays" class="h5 text-muted mb-0">0 jours</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="reason" class="form-label">
                                                <i class="bi bi-chat-quote form-icon me-2"></i>Motif de la demande *
                                            </label>
                                            <textarea class="form-control"
                                                      id="reason"
                                                      name="reason"
                                                      rows="4"
                                                      required
                                                      placeholder="Expliquez le motif de votre demande de congé..."><?php echo htmlspecialchars($_SESSION['create_request_data']['reason'] ?? ''); ?></textarea>
                                            <div class="form-text">Décrivez les raisons de votre demande (minimum 10 caractères)</div>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    Caractères restants : <span id="charCount">500</span>
                                                </small>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="index.php?action=my_requests" class="btn btn-outline-secondary me-md-2">
                                                <i class="bi bi-x-circle me-2"></i>Annuler
                                            </a>
                                            <button type="submit" class="btn btn-save text-white">
                                                <i class="bi bi-send me-2"></i>Soumettre la demande
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
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const reasonInput = document.getElementById('reason');
            const charCount = document.getElementById('charCount');
            const dateInfo = document.getElementById('dateInfo');
            const totalDays = document.getElementById('totalDays');
            const workingDays = document.getElementById('workingDays');
            const weekendDays = document.getElementById('weekendDays');

            // Mettre à jour le compteur de caractères
            reasonInput.addEventListener('input', function() {
                const remaining = 500 - this.value.length;
                charCount.textContent = remaining;

                if (remaining < 50) {
                    charCount.className = 'text-danger';
                } else if (remaining < 100) {
                    charCount.className = 'text-warning';
                } else {
                    charCount.className = 'text-muted';
                }
            });

            // Calcul des jours
            function calculateDays() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (startDateInput.value && endDateInput.value && startDate <= endDate) {
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                    let workingDaysCount = 0;
                    let weekendDaysCount = 0;

                    for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                        if (d.getDay() === 0 || d.getDay() === 6) {
                            weekendDaysCount++;
                        } else {
                            workingDaysCount++;
                        }
                    }

                    totalDays.textContent = diffDays + ' jours';
                    workingDays.textContent = workingDaysCount + ' jours';
                    weekendDays.textContent = weekendDaysCount + ' jours';

                    dateInfo.style.display = 'block';
                } else {
                    dateInfo.style.display = 'none';
                }
            }

            startDateInput.addEventListener('change', calculateDays);
            endDateInput.addEventListener('change', calculateDays);

            // Validation côté client
            document.querySelector('form').addEventListener('submit', function(e) {
                const leaveType = document.getElementById('leave_type_id').value;
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;
                const reason = reasonInput.value;

                if (!leaveType) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un type de congé.');
                    return false;
                }

                if (!startDate) {
                    e.preventDefault();
                    alert('Veuillez saisir une date de début.');
                    startDateInput.focus();
                    return false;
                }

                if (!endDate) {
                    e.preventDefault();
                    alert('Veuillez saisir une date de fin.');
                    endDateInput.focus();
                    return false;
                }

                if (new Date(startDate) > new Date(endDate)) {
                    e.preventDefault();
                    alert('La date de fin doit être après la date de début.');
                    endDateInput.focus();
                    return false;
                }

                if (reason.length < 10) {
                    e.preventDefault();
                    alert('Le motif doit contenir au moins 10 caractères.');
                    reasonInput.focus();
                    return false;
                }

                return true;
            });

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