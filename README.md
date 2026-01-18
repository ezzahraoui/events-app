# Événements - Application Simple de Gestion d'Événements

## 📋 Contexte
Application minimale de gestion d'événements avec inscriptions simples et emails de confirmation. Architecture POO simple dédiée aux développeurs débutants.

## 🛠️ Technologies
- **Backend**: PHP 8.2 avec MySQLi
- **Base de données**: MySQL
- **Conteneurisation**: Docker & Docker Compose
- **Emails**: MailHog (développement)
- **Frontend**: HTML/CSS vanilla (pas de framework)

## 🚀 Démarrage Rapide
```bash
# 1. Démarrer les services Docker
docker-compose up -d

# 2. Accéder à l'application
http://localhost:8080

# 3. Consulter les emails (MailHog)
http://localhost:8025
```

## 👥 Comptes de Test
```
Admin: admin@gmail.com / password
User:  user1@gmail.com / password
User:  user2@gmail.com / password
```

## ✨ Fonctionnalités Principales

### 👤 Utilisateurs (ROLE_USER)
- ✅ Consulter **TOUS** les événements disponibles
- ✅ S'inscrire aux événements (une fois par événement)
- ✅ Consulter ses inscriptions personnelles
- ✅ Recevoir emails de confirmation d'inscription
- ❌ **NE PEUVENT PAS** : Accéder à l'admin, modifier les événements, annuler les inscriptions

### 🔧 Administrateurs (ROLE_ADMIN)
- ✅ Créer des événements
- ✅ Modifier les événements
- ✅ Supprimer les événements (hard-delete)
- ✅ Voir toutes les inscriptions (tableau global)
- ✅ Dashboard administratif
- ❌ **NE PEUVENT PAS** : S'inscrire aux événements, voir "Mes inscriptions"

## ✅ Tests Clés
- **Inscription unique** : Un user ne peut s'inscrire qu'une fois par événement
- **Limite de capacité** : Les inscriptions sont refusées si l'événement est complet
- **Emails MailHog** : Chaque inscription envoie un email de confirmation
- **Accès sécurisé** : Admin ne peut pas s'inscrire, users ne peuvent pas accéder `/admin/`
- **Gestion d'erreurs** : 403 si accès non autorisé

## 📁 Structure du Projet
```
events-app/
├── src/
│   ├── Database.php                 # Singleton MySQLi
│   ├── models/
│   │   ├── User.php                 # Gestion utilisateurs
│   │   ├── Event.php                # Gestion événements
│   │   └── Registration.php         # Gestion inscriptions
│   └── services/
│       ├── AuthService.php          # Auth & autorisation
│       └── EmailService.php         # Envoi emails
├── admin/                           # Pages admin (protégées)
│   ├── index.php                    # Dashboard
│   ├── create_event.php             # Créer événement
│   ├── edit_event.php               # Éditer événement
│   ├── delete_event.php             # Supprimer événement
│   └── registrations.php            # Vue inscriptions
├── public/
│   └── css/style.css                # CSS simple
├── database/
│   └── script.sql                   # Init base de données
├── index.php                        # Accueil
├── login.php                        # Connexion
├── register.php                     # Inscription
├── logout.php                       # Déconnexion
├── 403.php                          # Erreur accès refusé
├── event_detail.php                 # Détails événement
├── my_registrations.php             # Mes inscriptions
└── cancel_registration.php          # Annuler inscription
```

## 🎯 Pages et Rôles

### Pages Publiques (Tous)
- `index.php` - Accueil avec liste des événements
- `login.php` - Connexion
- `register.php` - Inscription nouvel utilisateur
- `logout.php` - Déconnexion
- `403.php` - Erreur d'accès

### Pages Utilisateurs (ROLE_USER)
- `event_detail.php` - Détails d'un événement + inscription
- `my_registrations.php` - Liste des inscriptions personnelles
- `cancel_registration.php` - POST handler pour annuler une inscription

### Pages Admin (ROLE_ADMIN)
- `admin/index.php` - Dashboard avec tous les événements
- `admin/create_event.php` - Créer un nouvel événement
- `admin/edit_event.php` - Modifier un événement existant
- `admin/delete_event.php` - POST handler pour supprimer un événement
- `admin/registrations.php` - Vue globale de toutes les inscriptions

## 🏗️ Architecture & Style
- **POO Simple** : Classes sans namespaces, une classe par fichier
- **Pas de templates** : Header/footer dupliqués dans chaque page (style étudiant)
- **Includes explicites** : `require_once` en haut de chaque fichier
- **Prepared Statements** : Sécurité MySQLi avec binding de paramètres
- **Validation serveur** : Côté serveur uniquement, pas de validation client
- **Hard-delete** : Suppression définitive des événements (pas de soft-delete)

## 🔐 Sécurité
- ✅ Prepared statements MySQLi
- ✅ Password hashing (PASSWORD_DEFAULT)
- ✅ Session regeneration à la connexion
- ✅ Vérification des rôles pour toutes les pages admin
- ✅ Validation des données côté serveur
- ✅ Owner-check pour les ressources utilisateur