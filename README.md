# Projet Events & Inscriptions

## 📋 Contexte et Objectifs

### Contexte
Système d'organisation d'événements à capacité limitée avec inscriptions uniques et envoi de tickets par email.

### Objectifs pédagogiques
- ✅ Gestion de capacité et unicité des inscriptions
- ✅ Envoi d'emails avec MailHog
- ✅ Sécurité et contrôle d'accès
- ✅ PHP natif avec MySQL

## 🛠️ Prérequis Techniques

- **PHP 8.2+** avec extensions mysqli
- **MySQL 5.7+** (via XAMPP/MAMP)
- **MailHog** pour tests emails locaux
- **Navigateur moderne** (Chrome/Firefox)

## 📁 Structure du Projet

```
/
├── index.php                 # Liste événements
├── login.php                 # Connexion
├── register.php              # Inscription
├── event_detail.php          # Détail événement
├── event_register.php        # Traitement inscription
├── my_registrations.php      # Mes inscriptions
├── 403.php                   # Accès refusé
├── admin/                    # Section admin
│   ├── index.php            # Dashboard admin
│   ├── events_list.php      # CRUD events - liste
│   ├── event_add.php        # CRUD events - ajouter
│   ├── event_edit.php       # CRUD events - modifier
│   └── event_registrations.php # Liste inscrits par event
├── config/
│   └── database.php         # Connexion BDD
├── includes/
│   ├── header.php           # En-tête
│   ├── footer.php           # Pied de page
│   ├── auth.php             # Vérification auth
│   └── functions.php        # Fonctions utilitaires
└── public/
    └── uploads/             # Images événements
```

## 🗄️ Base de Données

### Script SQL d'installation

```sql
-- Base de données
CREATE DATABASE events_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE events_db;

-- Table users (obligatoire pour rôles)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('ROLE_USER', 'ROLE_ADMIN') DEFAULT 'ROLE_USER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table events (table métier 1)
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    event_date DATETIME NOT NULL,
    location VARCHAR(200) NOT NULL,
    capacity INT NOT NULL DEFAULT 50,
    image_url VARCHAR(255),
    status ENUM('draft', 'published', 'cancelled') DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Table registrations (table métier 2 avec FK)
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    UNIQUE KEY unique_registration (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Données de démonstration
INSERT INTO users (email, password_hash, first_name, last_name, role) VALUES
('admin@events.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'ROLE_ADMIN'),
('user1@events.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jean', 'Dupont', 'ROLE_USER'),
('user2@events.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marie', 'Martin', 'ROLE_USER');

INSERT INTO events (title, description, event_date, location, capacity, created_by, status) VALUES
('Conférence PHP 2024', 'Une conférence sur les dernières tendances PHP et les meilleures pratiques de développement.', '2024-03-15 14:00:00', 'Salle de conférence A', 30, 1, 'published'),
('Workshop MySQL', 'Atelier pratique sur l\'optimisation des requêtes MySQL et la conception de bases de données.', '2024-03-20 09:00:00', 'Labo informatique', 15, 1, 'published'),
('Meetup Développeurs', 'Rencontre informelle entre développeurs pour échanger sur les nouvelles technologies.', '2024-03-25 18:00:00', 'Café du Coin', 20, 1, 'published');
```

### Comptes de test
- **Admin**: admin@events.com / password
- **User1**: user1@events.com / password  
- **User2**: user2@events.com / password

## 🔐 Sécurité

### Mesures obligatoires
- ✅ Validation serveur (champs, formats, longueurs)
- ✅ Protection SQL injection (Mysqli préparé)
- ✅ Upload sécurisé (extensions, tailles, renommage)
- ✅ Gestion rôles (ROLE_USER, ROLE_ADMIN)
- ✅ Owner-check (403 si accès non autorisé)

### Exemples d'implémentation

#### Validation serveur
```php
// Email valide
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide";
}

// Capacité numérique
if (!is_numeric($capacity) || $capacity <= 0) {
    $errors[] = "La capacité doit être un nombre positif";
}
```

#### Protection SQL injection
```php
$sql = "SELECT * FROM events WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
```

#### Owner-check
```php
if ($_SESSION['user_id'] != $registration['user_id'] && $_SESSION['role'] != 'ROLE_ADMIN') {
    header('HTTP/1.0 403 Forbidden');
    include '403.php';
    exit;
}
```

## 📱 Fonctionnalités

### ROLE_USER
- 📋 Consulter la liste des événements
- 🎟 S'inscrire aux événements (unicité, capacité)
- 📧 Recevoir ticket par email (MailHog)
- 👤 Voir ses inscriptions

### ROLE_ADMIN
- ➕ CRUD événements (créer, lire, modifier, supprimer)
- 👥 Voir liste des inscrits par événement
- 📊 Gestion complète des données

## 🧪 Tests d'Acceptation

### Scénarios obligatoires

#### 1. Inscription double refusée
1. User se connecte
2. User s'inscrit à un événement
3. User tente de s'inscrire au même événement
4. **Résultat attendu**: Message d'erreur "Vous êtes déjà inscrit à cet événement"

#### 2. Capacité atteinte refus
1. Créer événement avec capacité = 2
2. User1 s'inscrit, User2 s'inscrit
3. User3 tente de s'inscrire
4. **Résultat attendu**: Message d'erreur "Cet événement est complet"

#### 3. Email MailHog
1. User s'inscrit à un événement
2. **Résultat attendu**: Email de confirmation reçu dans MailHog (localhost:8025)

#### 4. Owner-check 403
1. User1 consulte ses inscriptions
2. User1 tente d'accéder aux inscriptions de User2 (modification URL)
3. **Résultat attendu**: Page 403 "Accès refusé"

## 📦 Installation

### Étapes d'installation

1. **Installer XAMPP/MAMP**
   - Télécharger depuis le site officiel
   - Installer Apache et MySQL

2. **Configurer la base de données**
   - Démarrer MySQL via XAMPP
   - Exécuter le script SQL ci-dessus
   - Vérifier la création des tables

3. **Installer MailHog**
   - Télécharger MailHog pour Windows
   - Démarrer MailHog (SMTP: localhost:1025, Web: localhost:8025)
   - Configurer PHP pour utiliser MailHog

4. **Déployer les fichiers**
   - Copier les fichiers PHP dans `htdocs/events/`
   - Configurer les permissions pour `public/uploads/`

5. **Configuration PHP**
   ```ini
   ; php.ini
   SMTP = localhost
   smtp_port = 1025
   sendmail_path = "C:/path/to/MailHog/sendmail.exe -t"
   ```

6. **Accéder à l'application**
   - URL: `http://localhost/events/`
   - Admin: `http://localhost/events/admin/`

## 📸 Captures d'Écran Obligatoires

### Checklist des captures à fournir
- [ ] Page liste des événements (index.php)
- [ ] Page détail événement avec formulaire (event_detail.php)
- [ ] Page "Mes inscriptions" (my_registrations.php)
- [ ] Interface admin CRUD événements (admin/events_list.php)
- [ ] **MailHog** - Email ticket reçu (localhost:8025)
- [ ] **Page 403** - Owner-check démonstration

## 📋 Livrables

### Checklist finale
- [ ] Code source complet (dépôt GitHub ou ZIP)
- [ ] Script SQL avec données de test
- [ ] Captures d'écran obligatoires
- [ ] README.md complet (ce fichier)
- [ ] Mini rapport (2-3 pages) :
  - Architecture du projet
  - Mesures de sécurité implémentées
  - Difficultés rencontrées
  - Améliorations possibles

## 🚀 Déploiement

### Configuration de production

#### Base de données (`config/database.php`)
```php
<?php
$mysqli = new mysqli(
    'localhost',
    'root',
    '',
    'events_db'
);

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}
?>
```

#### Configuration MailHog
```php
<?php
// Envoi email
$to = $user['email'];
$subject = "Confirmation d'inscription - " . $event['title'];
$message = "Bonjour " . $user['first_name'] . ",\n\n";
$message .= "Votre inscription à l'événement \"" . $event['title'] . "\" est confirmée.\n";
$message .= "Date: " . date('d/m/Y H:i', strtotime($event['event_date'])) . "\n";
$message .= "Lieu: " . $event['location'] . "\n\n";
$message .= "Merci de votre participation !";

$headers = "From: noreply@events.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

mail($to, $subject, $message, $headers);
?>
```

## 📖 Documentation

### Guide d'utilisation

#### Pour les utilisateurs (ROLE_USER)
1. **Connexion**: Utiliser user1@events.com / password
2. **Navigation**: Consulter la liste des événements
3. **Inscription**: Cliquer sur "S'inscrire" sur un événement
4. **Vérification**: Consulter ses inscriptions et emails MailHog

#### Pour les administrateurs (ROLE_ADMIN)
1. **Connexion**: Utiliser admin@events.com / password
2. **CRUD**: Créer, modifier, supprimer des événements
3. **Supervision**: Voir les inscrits par événement
4. **Gestion**: Gérer les utilisateurs et les données

### Dépannage

#### Problèmes courants
- **MailHog ne fonctionne pas**: Vérifier que le service est démarré
- **Erreur 403**: Vérifier les permissions de fichiers et la configuration Apache
- **Base de données inaccessible**: Démarrer MySQL via XAMPP

---

*Projet académique - Développement Web PHP*  
*Formation: Développement Web et Applications*  
*Date: Janvier 2024*  
*Auteur: [Votre Nom]*