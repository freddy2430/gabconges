<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - congésGAB</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .register-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .invalid-feedback {
            display: block;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .register-footer {
            background: #f8f9fa;
            border-radius: 0 0 15px 15px;
            padding: 1.5rem;
            text-align: center;
        }
        .password-strength {
            font-size: 0.875rem;
        }
        .password-strength.weak { color: #dc3545; }
        .password-strength.medium { color: #fd7e14; }
        .password-strength.strong { color: #198754; }
        .alert {
            border-radius: 8px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card register-card">
                    <div class="register-header">
                        <h2><i class="bi bi-person-plus me-2"></i>Créer un compte</h2>
                        <p class="mb-0">Rejoignez notre système de congésGAB</p>
                    </div>

                    <div class="card-body p-4">
                        <?php if (isset($_SESSION['register_error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($_SESSION['register_error']); ?>
                            </div>
                            <?php unset($_SESSION['register_error']); ?>
                        <?php endif; ?>

                        <form method="POST" action="index.php?action=register" novalidate id="registerForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">
                                        <i class="bi bi-person me-2"></i>Prénom *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="first_name"
                                           name="first_name"
                                           value="<?php echo htmlspecialchars($_SESSION['register_data']['first_name'] ?? ''); ?>"
                                           required>
                                    <div class="invalid-feedback">Le prénom est requis.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">
                                        <i class="bi bi-person me-2"></i>Nom de famille *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="last_name"
                                           name="last_name"
                                           value="<?php echo htmlspecialchars($_SESSION['register_data']['last_name'] ?? ''); ?>"
                                           required>
                                    <div class="invalid-feedback">Le nom de famille est requis.</div>
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
                                       value="<?php echo htmlspecialchars($_SESSION['register_data']['username'] ?? ''); ?>"
                                       required>
                                <div class="form-text">Lettres, chiffres et underscores uniquement</div>
                                <div class="invalid-feedback">Le nom d'utilisateur est requis.</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-2"></i>Email *
                                </label>
                                <input type="email"
                                       class="form-control"
                                       id="email"
                                       name="email"
                                       value="<?php echo htmlspecialchars($_SESSION['register_data']['email'] ?? ''); ?>"
                                       required>
                                <div class="invalid-feedback">Une adresse email valide est requise.</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock me-2"></i>Mot de passe *
                                    </label>
                                    <input type="password"
                                           class="form-control"
                                           id="password"
                                           name="password"
                                           required>
                                    <div class="invalid-feedback">Le mot de passe est requis.</div>
                                    <div class="password-strength" id="passwordStrength"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">
                                        <i class="bi bi-lock me-2"></i>Confirmer le mot de passe *
                                    </label>
                                    <input type="password"
                                           class="form-control"
                                           id="confirm_password"
                                           name="confirm_password"
                                           required>
                                    <div class="invalid-feedback">La confirmation du mot de passe est requise.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        J'accepte les <a href="#" class="text-decoration-none">conditions d'utilisation</a> *
                                    </label>
                                    <div class="invalid-feedback">Vous devez accepter les conditions d'utilisation.</div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-register text-white">
                                    <i class="bi bi-person-plus me-2"></i>Créer mon compte
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="register-footer">
                        <p class="mb-0">
                            Déjà un compte ?
                            <a href="index.php?action=login" class="text-decoration-none fw-bold">Se connecter</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation côté client et vérification de la force du mot de passe
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const passwordStrength = document.getElementById('passwordStrength');

            // Vérification de la force du mot de passe
            password.addEventListener('input', function() {
                const value = this.value;
                let strength = '';

                if (value.length >= 8) {
                    if (/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(value)) {
                        strength = '<i class="bi bi-check-circle me-1"></i> Mot de passe fort';
                        passwordStrength.className = 'password-strength strong';
                    } else if (/^(?=.*[a-z])(?=.*[A-Z])|^(?=.*[a-z])(?=.*\d)|^(?=.*[A-Z])(?=.*\d)/.test(value)) {
                        strength = '<i class="bi bi-exclamation-triangle me-1"></i> Mot de passe moyen';
                        passwordStrength.className = 'password-strength medium';
                    } else {
                        strength = '<i class="bi bi-dash-circle me-1"></i> Mot de passe faible';
                        passwordStrength.className = 'password-strength weak';
                    }
                } else {
                    strength = '<i class="bi bi-x-circle me-1"></i> Au moins 8 caractères';
                    passwordStrength.className = 'password-strength weak';
                }

                passwordStrength.innerHTML = strength;
            });

            // Vérification de la correspondance des mots de passe
            confirmPassword.addEventListener('input', function() {
                if (this.value !== password.value) {
                    this.setCustomValidity('Les mots de passe ne correspondent pas');
                } else {
                    this.setCustomValidity('');
                }
            });

            // Validation du formulaire
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                const form = this;
                let isValid = true;

                // Validation du nom d'utilisateur
                const username = document.getElementById('username');
                if (username.value.length < 3) {
                    username.classList.add('is-invalid');
                    isValid = false;
                } else {
                    username.classList.remove('is-invalid');
                }

                // Validation de l'email
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value)) {
                    email.classList.add('is-invalid');
                    isValid = false;
                } else {
                    email.classList.remove('is-invalid');
                }

                // Validation du mot de passe
                if (password.value.length < 8) {
                    password.classList.add('is-invalid');
                    isValid = false;
                } else {
                    password.classList.remove('is-invalid');
                }

                // Validation des conditions
                const terms = document.getElementById('terms');
                if (!terms.checked) {
                    terms.classList.add('is-invalid');
                    isValid = false;
                } else {
                    terms.classList.remove('is-invalid');
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            // Effacer les erreurs après 5 secondes
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