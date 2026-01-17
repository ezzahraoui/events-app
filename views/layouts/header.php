<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Application Événements</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <header class="main-header">
        <nav class="navbar">
            <div class="nav-container">
                <a href="index.php" class="nav-brand">
                    <h1>Application Événements</h1>
                </a>
                
                <ul class="nav-menu">
                    <?php if (AuthService::isLoggedIn()): ?>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="my_registrations.php">Mes inscriptions</a></li>
                        <?php if (AuthService::isAdmin()): ?>
                            <li><a href="admin/" class="admin-link">Admin</a></li>
                        <?php endif; ?>
                        <li>
                            <span class="user-welcome">
                                Bonjour, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </span>
                        </li>
                        <li><a href="logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="login.php">Connexion</a></li>
                        <li><a href="register.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>