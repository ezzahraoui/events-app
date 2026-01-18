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

### Pages Racine (communes)
- **index.php** : Page d'accueil avec tous les événements (tous)
- **login.php** : Connexion (tous)
- **register.php** : Inscription (tous)
- **logout.php** : Déconnexion (tous)
- **403.php** : Erreur accès refusé (tous)
- **event_detail.php** : Détails événement + inscription (users connectés)
- **my_registrations.php** : Inscriptions personnelles (users uniquement)
- **cancel_registration.php** : POST handler pour annuler (users)

### Pages Admin (admin/)
- **index.php** : Dashboard admin avec tous les événements
- **create_event.php** : Formulaire création événement
- **edit_event.php** : Formulaire modification événement
- **delete_event.php** : POST handler suppression (hard-delete)
- **registrations.php** : Vue globale des inscriptions

### Services (src/services/)
- **AuthService.php** : Gestion authentification + rôles
- **EmailService.php** : Envoi emails (MailHog)

### Header/Footer (style étudiant)
- **Pages racine** : Header/footer dupliqués dans chaque fichier
- **Pages admin** : Header/footer dupliqués dans chaque fichier
- **Aucun système de templates** : Code PHP simple et direct

## 🎭 Rôles et Permissions

### Rôle Admin (ROLE_ADMIN)
- ✅ **PEUT** : Créer, modifier, supprimer les événements
- ✅ **PEUT** : Voir toutes les inscriptions (vue globale)
- ✅ **PEUT** : Accéder à `/admin/*`
- ❌ **NE PEUT PAS** : S'inscrire aux événements
- ❌ **NE PEUT PAS** : Voir "Mes inscriptions"
- ❌ **NE PEUT PAS** : Voir page `/event_detail.php` (redirigé à `/admin/index.php`)

### Rôle User (ROLE_USER)
- ✅ **PEUT** : Voir TOUS les événements (published + draft + cancelled)
- ✅ **PEUT** : S'inscrire aux événements disponibles
- ✅ **PEUT** : Consulter ses inscriptions personnelles
- ✅ **PEUT** : Annuler ses inscriptions
- ❌ **NE PEUT PAS** : Accéder aux pages `/admin/*`
- ❌ **NE PEUT PAS** : Créer/modifier/supprimer des événements

### Contrôle d'Accès & Redirections
```php
// Pages PUBLIQUES (tous)
index.php, login.php, register.php, logout.php, 403.php
↓
// Pages UTILISATEURS (require login + not admin)
event_detail.php, my_registrations.php, cancel_registration.php
↓
// Pages ADMIN (require admin)
admin/index.php, admin/create_event.php, admin/edit_event.php, 
admin/delete_event.php, admin/registrations.php
```

### Comportements Spéciaux
1. **Admin accède à `index.php`** → Redirection automatique vers `/admin/index.php`
2. **User accède à `/admin/*`** → Erreur 403
3. **Toute page `/admin/*` doit vérifier `AuthService::requireAdmin()`**
4. **Hard-delete** : Suppression définitive sans confirmation

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

## 🧪 Tests Manuels Obligatoires

### 1. Inscription Unique
- User s'inscrit à un événement
- Tente de s'inscrire à nouveau
- ✅ Résultat attendu: Message "Vous êtes déjà inscrit"

### 2. Capacité Atteinte
- Admin crée événement avec capacité = 2
- User1 et User2 s'inscrivent
- User3 tente de s'inscrire
- ✅ Résultat attendu: Message "Événement complet"

### 3. Emails MailHog
- User s'inscrit à un événement
- Accéder à http://localhost:8025
- ✅ Résultat attendu: Email de confirmation reçu

### 4. Accès Admin Protégé
- User normal accède à `/admin/index.php`
- ✅ Résultat attendu: Page 403 (Accès refusé)

### 5. Redirection Admin
- Admin connecté accède à `index.php`
- ✅ Résultat attendu: Redirection vers `/admin/index.php`

### 6. Hard-Delete Événement
- Admin crée événement + User inscrit
- Admin supprime l'événement
- ✅ Résultat attendu: Événement supprimé + inscription supprimée aussi (CASCADE)

## 📁 Structure des Fichiers par Rôles

```
/
├── docker-compose.yml          # Configuration Docker
├── Dockerfile                  # Image PHP personnalisée
├── index.php                   # Page d'accueil (tous)
├── login.php                   # Connexion (tous)
├── register.php                # Inscription (tous)
├── logout.php                  # Déconnexion (tous)
├── 403.php                     # Erreur accès refusé (tous)
├── event_detail.php            # Détails événement (users connectés)
├── my_registrations.php        # Inscriptions personnelles (users uniquement)
├── admin/                      # Pages admin-only
│   ├── index.php               # Dashboard admin
│   ├── create_event.php        # Créer événement
│   ├── edit_event.php          # Modifier événement
│   ├── delete_event.php        # Supprimer événement
│   └── registrations.php       # Vue globale inscriptions
├── src/
│   ├── Database.php            # Singleton BDD
│   ├── models/
│   │   ├── User.php            # Class User
│   │   ├── Event.php           # Class Event
│   │   └── Registration.php    # Class Registration
│   └── services/
│       ├── AuthService.php     # Gestion auth + rôles
│       └── EmailService.php    # Envoi emails
├── (aucun views/ - header/footer dupliqués dans chaque page, style étudiant)
├── public/
│   └── css/
│       └── style.css           # CSS avec cartes
└── database/
    └── script.sql              # Script SQL initial
```

## 🚀 Principes de Développement

### Philosophie: SIMPLE et MINIMAL

**L'application suit ces principes pour rester maintenable par des débutants:**

1. **Zéro feature bonus** - Seulement ce qui est nécessaire
2. **Hard-delete** - Pas de soft-delete, juste supprimer
3. **Pas d'annulation** - Les users gardent leurs inscriptions
4. **Tous les événements** - Pas de tri/recherche, afficher tous
5. **Pas de status** - Les événements existent simplement
6. **Code débutant** - Simple, lisible, pas de patterns complexes
7. **Duplication acceptée** - Style étudiant: header/footer copiés

### Code POO
- **Un fichier = une classe** : Organisation claire
- **Includes explicites** : `require_once` en haut de chaque fichier
- **Pas de magic** : Code lisible et compréhensible
- **Pas de namespaces** : Classes simples, accès direct
- **Getters/Setters simples** : Pas de logique complexe
- **Comments français** : Pour le contexte académique

### Sécurité
- **Prepared Statements** : MySQLi uniquement, jamais de string interpolation
- **Password Hashing** : PASSWORD_DEFAULT pour tous les mots de passe
- **Validation Serveur** : Toujours côté serveur, jamais client-side uniquement
- **Session Regeneration** : Appelé à chaque login pour sécurité
- **Owner-check** : Vérification de qui fait quoi sur les ressources
- **Admin-check** : `AuthService::requireAdmin()` dans chaque `/admin/*`

### Interface & CSS
- **HTML Sémantique** : Pas de divs inutiles
- **CSS Simple** : Pas de préprocesseur (SASS/LESS)
- **Design Moderne** : Cartes avec ombres et transitions simples
- **Responsive** : Grid CSS auto-fill, pas de mobile-first complexe
- **Animations Légères** : Juste `transition: all 0.3s ease` au hover
- **Pas de Framework** : Pas de Bootstrap, Tailwind, etc.

### Emails
- **MailHog uniquement** : Pour développement seulement
- **Pas d'HTML** : Emails en texte brut
- **Templates simples** : Juste du texte avec variables
- **Pas de staging** : Pas d'environnement staging, test local uniquement

### Base de Données
- **UTF8MB4** : Support complet des caractères spéciaux
- **Contraintes FK** : Cascade delete pour maintenir l'intégrité
- **Pas de migrations** : Un seul script SQL initial
- **Pas d'ORM** : MySQLi natif uniquement

## 📝 Exemple de Code Attendu

**✅ CODE BON (Style attendu):**
```php
<?php
require_once 'src/Database.php';
require_once 'src/models/Event.php';
require_once 'src/services/AuthService.php';

session_start();
AuthService::requireAdmin();

$events = Event::findAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = new Event();
    $event->setTitle($_POST['title'] ?? '');
    $event->setDescription($_POST['description'] ?? '');
    
    $errors = $event->validate();
    if (empty($errors)) {
        $event->save();
        $_SESSION['success'] = 'Événement créé !';
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Header simple dupliqué -->
</head>
<body>
    <!-- Contenu -->
</body>
</html>
```

**❌ CODE MAUVAIS (À éviter):**
```php
<?php
// ❌ Namespaces complexes
namespace App\Controllers\Admin;

// ❌ Dependency Injection Container
use Container;

// ❌ Builder Pattern
$event = EventBuilder::new()
    ->withTitle($data['title'])
    ->withDescription($data['description'])
    ->build();

// ❌ Traits et interfaces
class EventController implements EventControllerInterface { ... }

// ❌ Validation complexe
$validator->validate($event, EventValidationRules::class);
```

## 🎯 Règles de Développement

### Code POO
- **Un fichier = une classe** : Organisation claire
- **Includes explicites** : `require_once` en haut de chaque fichier
- **Pas de magic** : Code lisible et compréhensible
- **Comments français** : Pour le contexte académique
- **Error handling** : try/catch simples avec messages clairs

### Sécurité
- **SQL** : MySQLi prepared statements uniquement
- **Validation** : Toujours côté serveur
- **Sessions** : Démarrer avec `session_start()`
- **Owner-check** : Vérification systématique des permissions

### Interface
- **HTML** : Sémantique simple, pas de div excessives
- **CSS** : Classes simples, design moderne avec cartes
- **Responsive** : Grid CSS auto-fill
- **Pas d'animations** : Transitions simples au hover uniquement

---

*Guide pour agents IA - Projet Events App Étudiant*  
*PHP 8.2 + MySQL + Docker + POO Simple*