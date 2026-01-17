# AGENTS.md - Guide pour Développement Events App

## 🐳 Commandes Docker

### Démarrage environnement
```bash
# Démarrer tous les services (web, db, mailhog)
docker-compose up -d

# Arrêter tous les services
docker-compose down

# Redémarrer un service spécifique
docker-compose restart web

# Voir les logs PHP en temps réel
docker-compose logs -f web
```

### Base de données
```bash
# Se connecter à MySQL
docker-compose exec db mysql -u root -proot events_db

# Importer le script SQL
docker-compose exec db mysql -u root -proot events_db < database/script.sql

# Exporter la base de données
docker-compose exec db mysqldump -u root -proot events_db > backup.sql
```

### Services
```bash
# Application web
http://localhost:8080

# MailHog (emails de développement)
http://localhost:8025

# PHPMyAdmin (optionnel)
http://localhost:8081
```

## 📝 Style de Code POO Étudiant

### Structure des classes
- **Pas de namespaces** : Classes simples avec require_once
- **Une classe par fichier** : Organisation claire
- **Properties privées** : Avec getters/setters simples
- **Methods camelCase** : Verbes simples (save, find, validate)

### Exemple de classe Model
```php
<?php
// src/models/User.php
class User {
    private $id;
    private $email;
    private $firstName;
    private $lastName;
    private $role = 'ROLE_USER';
    
    public function save() {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO users (email, first_name, last_name, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$this->email, $this->firstName, $this->lastName, $this->role]);
    }
    
    public function validate() {
        $errors = [];
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        }
        if (empty($this->firstName)) {
            $errors[] = "Prénom requis";
        }
        return $errors;
    }
    
    // Getters simples
    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function getFirstName() { return $this->firstName; }
    public function getLastName() { return $this->lastName; }
    public function getRole() { return $this->role; }
    
    // Setters simples
    public function setEmail($email) { $this->email = $email; }
    public function setFirstName($firstName) { $this->firstName = $firstName; }
    public function setLastName($lastName) { $this->lastName = $lastName; }
}
```

### Includes manuels (explicites)
```php
<?php
// Dans chaque fichier qui utilise des classes
require_once 'src/models/User.php';
require_once 'src/models/Event.php';
require_once 'src/services/AuthService.php';

$user = new User();
$event = new Event();
```

## 🏗️ Architecture MVC Simple

### Models (src/models/)
- **User.php** : Gestion utilisateurs
- **Event.php** : Gestion événements  
- **Registration.php** : Gestion inscriptions

### Controllers (src/controllers/)
- **EventController.php** : Logique événements
- **UserController.php** : Logique utilisateurs

### Services (src/services/)
- **AuthService.php** : Gestion authentification
- **EmailService.php** : Envoi emails (MailHog)

### Views (views/)
- **layouts/** : header.php, footer.php
- **events/** : list.php, detail.php
- **auth/** : login.php, register.php

## 🎨 CSS Simple avec Cartes

### Design des cartes événements
```css
/* Grille responsive pour les événements */
.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
}

/* Carte événement moderne */
.event-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s ease;
}

.event-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

/* Couleurs neutres */
.event-title {
    color: #1f2937;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.event-description {
    color: #6b7280;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 16px;
}

.event-date {
    color: #059669;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Boutons simples */
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 500;
    transition: background-color 0.2s ease;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}
```

## 🔐 Sécurité POO Simple

### Database Singleton
```php
<?php
// src/Database.php
class Database {
    private static $instance = null;
    private $mysqli;
    
    private function __construct() {
        $this->mysqli = new mysqli(
            'db',  // Nom du service Docker
            'root',
            'root',
            'events_db'
        );
        
        if ($this->mysqli->connect_error) {
            die('Connection failed: ' . $this->mysqli->connect_error);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function prepare($sql) {
        return $this->mysqli->prepare($sql);
    }
}
```

### Validation dans les Models
```php
<?php
// src/models/Event.php
class Event {
    private $id;
    private $title;
    private $description;
    private $eventDate;
    private $location;
    private $capacity;
    
    public function validate() {
        $errors = [];
        
        // Titre obligatoire
        if (empty($this->title) || strlen($this->title) < 3) {
            $errors[] = "Le titre doit contenir au moins 3 caractères";
        }
        
        // Capacité numérique positive
        if (!is_numeric($this->capacity) || $this->capacity <= 0) {
            $errors[] = "La capacité doit être un nombre positif";
        }
        
        // Date valide
        if (!DateTime::createFromFormat('Y-m-d H:i', $this->eventDate)) {
            $errors[] = "La date n'est pas valide";
        }
        
        return $errors;
    }
}
```

### AuthService
```php
<?php
// src/services/AuthService.php
class AuthService {
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'ROLE_ADMIN';
    }
    
    public static function canAccess($resourceUserId) {
        if (self::isAdmin()) {
            return true;
        }
        return self::isLoggedIn() && $_SESSION['user_id'] === $resourceUserId;
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('HTTP/1.0 403 Forbidden');
            include '403.php';
            exit;
        }
    }
}
```

## 🧪 Tests et Développement

### Tests manuels (obligatoires)
1. **Inscription double refusée**
   - User s'inscrit à un événement
   - Tente de s'inscrire à nouveau
   - Résultat : Message "Déjà inscrit"

2. **Capacité atteinte refus**
   - Créer événement avec capacité = 2
   - User1 et User2 s'inscrivent
   - User3 tente de s'inscrire
   - Résultat : Message "Événement complet"

3. **Email MailHog**
   - User s'inscrit à un événement
   - Vérifier email reçu dans MailHog
   - URL : http://localhost:8025

4. **Owner-check 403**
   - User1 consulte ses inscriptions
   - Tente d'accéder aux inscriptions de User2
   - Résultat : Page 403

### Débogage
```php
// Pour déboguer (à supprimer en production)
var_dump($variable);
error_log("Message de debug: " . print_r($data, true));

// Dans les services
public function debug($data) {
    error_log("DEBUG: " . print_r($data, true));
}
```

## 📁 Structure des Fichiers

```
/
├── docker-compose.yml          # Configuration Docker
├── Dockerfile                  # Image PHP personnalisée
├── index.php                   # Page d'accueil
├── login.php                   # Connexion
├── register.php                # Inscription
├── 403.php                     # Accès refusé
├── src/
│   ├── Database.php            # Singleton BDD
│   ├── models/
│   │   ├── User.php            # Class User
│   │   ├── Event.php           # Class Event
│   │   └── Registration.php    # Class Registration
│   ├── services/
│   │   ├── AuthService.php     # Gestion auth
│   │   └── EmailService.php    # Envoi emails
│   └── controllers/
│       ├── EventController.php # Logique événements
│       └── UserController.php  # Logique utilisateurs
├── public/
│   ├── css/
│   │   └── style.css           # CSS avec cartes
│   └── uploads/                # Images uploadées
├── views/
│   ├── layouts/
│   │   ├── header.php          # En-tête HTML
│   │   └── footer.php          # Pied de page HTML
│   ├── events/
│   │   ├── list.php            # Grille de cartes
│   │   └── detail.php          # Détail événement
│   └── auth/
│       ├── login.php           # Formulaire connexion
│       └── register.php        # Formulaire inscription
└── database/
    └── script.sql              # Script SQL initial
```

## 🚀 Règles de Développement

### Code POO
- **Un fichier = une classe** : Organisation claire
- **Includes explicites** : require_once en haut de chaque fichier
- **Pas de magic** : Code lisible et compréhensible
- **Comments français** : Pour le contexte académique
- **Error handling** : try/catch simples avec messages clairs

### Sécurité
- **SQL** : Mysqli prepared statements uniquement
- **Validation** : Toujours côté serveur
- **Sessions** : Démarrer avec session_start()
- **Upload** : Extensions jpg/png/pdf, taille max 2MB
- **Owner-check** : Vérification systématique des permissions

### Interface
- **HTML** : Sémantique simple, pas de div excessives
- **CSS** : Classes simples, design moderne avec cartes
- **Responsive** : Grid CSS auto-fill
- **Pas d'animations** : Transitions simples au hover uniquement

---

*Guide pour agents IA - Projet Events App Étudiant*  
*PHP 8.2 + MySQL + Docker + POO Simple*