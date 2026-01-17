# Events App

## Contexte
Système d'événements avec inscriptions uniques et emails de confirmation.

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
- **Users**: Consulter et s'inscrire aux événements
- **Admin**: Gérer les événements et voir les inscrits

## Tests Clés
- Inscription unique par événement
- Limite de capacité respectée
- Emails de confirmation envoyés
- Accès sécurisé par rôle

## Structure
- `/src/` : Classes PHP (Models, Services, Controllers)
- `/views/` : Templates HTML
- `/public/` : CSS et uploads
- `/database/` : Script SQL

---
*PHP + MySQL + Docker*