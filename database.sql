-- Script de création de la base de données pour l'application de congésGAB
-- Compatible MySQL 8.x

-- Table des utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'employee') DEFAULT 'employee',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des types de congés
CREATE TABLE leave_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    max_days_per_year INT DEFAULT 0,
    requires_approval BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des demandes de congés
CREATE TABLE leave_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    requested_days INT NOT NULL,
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des jours fériés (optionnel, pour calcul des jours ouvrés)
CREATE TABLE holidays (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    holiday_date DATE UNIQUE NOT NULL,
    is_recurring BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion des données de base

-- Utilisateur administrateur par défaut
-- Mot de passe : Admin123! (haché)
INSERT INTO users (username, email, password, first_name, last_name, role) VALUES
('admin', 'admin@gestion-conges.local',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', -- Admin123!
 'Administrateur', 'Système', 'admin');

-- Types de congés par défaut
INSERT INTO leave_types (name, description, max_days_per_year) VALUES
('Annuel', 'Congés payés annuels', 25),
('Maladie', 'Congé maladie', 0),
('Maternité', 'Congé maternité/paternité', 0),
('Exceptionnel', 'Congé exceptionnel', 0),
('Formation', 'Congé formation professionnelle', 0);

-- Quelques jours fériés français (exemples)
INSERT INTO holidays (name, holiday_date, is_recurring) VALUES
('Jour de l\'An', '2024-01-01', TRUE),
('Fête du Travail', '2024-05-01', TRUE),
('Victoire 1945', '2024-05-08', TRUE),
('Fête Nationale', '2024-07-14', TRUE),
('Assomption', '2024-08-15', TRUE),
('Toussaint', '2024-11-01', TRUE),
('Armistice 1918', '2024-11-11', TRUE),
('Noël', '2024-12-25', TRUE);

-- Index pour optimiser les performances
CREATE INDEX idx_leave_requests_user_id ON leave_requests(user_id);
CREATE INDEX idx_leave_requests_status ON leave_requests(status);
CREATE INDEX idx_leave_requests_dates ON leave_requests(start_date, end_date);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_active ON users(is_active);