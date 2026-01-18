<?php
require_once '../src/Database.php';
require_once '../src/services/AuthService.php';

session_start();
AuthService::requireAdmin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .admin-header {
            background: #2c3e50;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            font-size: 1.5rem;
        }
        .admin-nav {
            display: flex;
            gap: 20px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: #34495e;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .admin-nav a:hover {
            background: #3498db;
        }
        .admin-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        .admin-menu {
            display: flex;
            gap: 30px;
        }
        .menu-card {
            background: white;
            padding: 40px 60px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }
        .menu-card h2 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.3rem;
        }
        .menu-card p {
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        .menu-card a {
            display: inline-block;
            padding: 12px 30px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .menu-card a:hover {
            background: #2980b9;
        }
        .menu-card.users a {
            background: #27ae60;
        }
        .menu-card.users a:hover {
            background: #219a52;
        }
        .menu-card.events a {
            background: #e74c3c;
        }
        .menu-card.events a:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1>Administration</h1>
        <nav class="admin-nav">
            <a href="index.php">Accueil</a>
            <a href="../logout.php">Déconnexion</a>
        </nav>
    </header>

    <main class="admin-content">
        <div class="admin-menu">
            <div class="menu-card users">
                <h2>Utilisateurs</h2>
                <p>Gérer les utilisateurs</p>
                <a href="users/index.php">Gérer</a>
            </div>
            <div class="menu-card events">
                <h2>Événements</h2>
                <p>Gérer les événements</p>
                <a href="events/index.php">Gérer</a>
            </div>
            <div class="menu-card events">
                <h2>Inscriptions</h2>
                <p>Voir les inscriptions</p>
                <a href="events/registrations.php">Voir</a>
            </div>
        </div>
    </main>
</body>
</html>
