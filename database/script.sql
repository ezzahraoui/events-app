CREATE DATABASE IF NOT EXISTS events_db;
USE events_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('ROLE_USER', 'ROLE_ADMIN') DEFAULT 'ROLE_USER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    event_date DATETIME NOT NULL,
    location VARCHAR(200) NOT NULL,
    capacity INT NOT NULL DEFAULT 50,
    image_url VARCHAR(255),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_registration (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (email, password_hash, first_name, last_name, role) VALUES
('admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'ROLE_ADMIN'),
('user1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jawad', 'User', 'ROLE_USER'),
('user2@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ayman', 'User', 'ROLE_USER');

INSERT INTO events (title, description, event_date, location, capacity, created_by) VALUES
('Conférence PHP 2024', 'Une conférence sur les dernières tendances PHP et les meilleures pratiques de développement.', '2026-12-20 14:00:00', 'Salle de conférence A', 30, 1),
('Workshop MySQL', 'Atelier pratique sur l\'optimisation des requêtes MySQL et la conception de bases de données.', '2026-12-25 09:00:00', 'Labo informatique', 15, 1),
('Meetup Développeurs', 'Rencontre informelle entre développeurs pour échanger sur les nouvelles technologies.', '2026-12-30 18:00:00', 'Café du Coin', 20, 1),
('Hackathon Week-end', '48h de coding intensif pour créer des projets innovants.', '2027-01-15 09:00:00', 'Tech Hub', 50, 1);