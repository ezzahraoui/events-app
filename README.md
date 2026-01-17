# Événements

## Contexte
Application de gestion d'événements avec inscriptions uniques et emails de confirmation, organisée par rôles pour une meilleure séparation des responsabilités.

## Technologies
- PHP 8.2+ avec MySQL
- Docker pour le développement
- MailHog pour les emails

## Démarrage Rapide
1. Clonez le dépôt
2. Lancez `docker-compose up -d`
3. Accédez à `http://localhost:8080`

## Comptes de Test
- Admin: admin@events.com / password
- User: user1@events.com / password

## Fonctionnalités
### Utilisateurs (ROLE_USER)
- Consulter les événements publiés
- S'inscrire aux événements
- Gérer ses inscriptions personnelles
- Recevoir des emails de confirmation

### Administrateurs (ROLE_ADMIN)
- Créer, modifier, supprimer des événements
- Voir toutes les inscriptions (vue globale)
- Accès au dashboard administratif
- **Ne peuvent pas s'inscrire aux événements**

## Tests Clés
- **Inscription unique** : Un utilisateur ne peut s'inscrire qu'une fois par événement
- **Limite de capacité** : Les inscriptions sont refusées quand l'événement est complet
- **Emails de confirmation** : Chaque inscription envoie un email (MailHog)
- **Accès sécurisé par rôle** : Admin ne peut pas s'inscrire, users ne peuvent pas accéder à l'admin
- **Permissions correctes** : Tentative d'accès non autorisé → page 403

### MailHog
- Vérifier les emails dans `http://localhost:8025`

## Structure du Projet
- **Racine** : Pages publiques et communes (header/footer dupliqués)
- **`/admin/`** : Pages administration (header/footer dupliqués)
- `/src/` : Classes PHP (Models, Services)
- **Aucun views/** : Header/footer dupliqués dans chaque page (style étudiant)
- `/public/` : CSS et assets
- `/database/` : Scripts SQL

### Organisation par Rôles
- **Pages publiques** : `index.php`, `login.php`, `register.php`, `logout.php`, `403.php` (header/footer dupliqués)
- **Pages utilisateurs** : `event_detail.php`, `my_registrations.php` (header/footer dupliqués)
- **Pages admin** : `/admin/index.php`, `/admin/create_event.php`, etc. (header/footer dupliqués)
- **Style étudiant** : Aucun système de templates, header/footer copiés dans chaque page