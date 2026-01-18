<?php
require_once 'src/Database.php';
require_once 'src/models/Event.php';
require_once 'src/services/AuthService.php';

session_start();

// Admin redirection
if (AuthService::isAdmin()) {
    header('Location: admin/index.php');
    exit;
}

// Get database connection
$database = Database::getInstance();
$mysqli = $database->getConnection();

// Get all events
$events = [];
$sql = "SELECT * FROM events ORDER BY event_date ASC LIMIT 6";
$result = $mysqli->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <header class="main-header">
        <nav class="navbar">
            <div class="nav-container">
                <a href="index.php" class="nav-brand">
                    <h1>Événements</h1>
                </a>

                <ul class="nav-menu">
                    <?php if (AuthService::isLoggedIn()): ?>
                        <li><a href="index.php">Accueil</a></li>
                        <?php if (AuthService::isLoggedIn() && !AuthService::isAdmin()): ?>
                            <li><a href="my_registrations.php">Mes inscriptions</a></li>
                        <?php endif; ?>
                        <?php if (AuthService::isAdmin()): ?>
                            <li><a href="admin/index.php" class="admin-link">Admin</a></li>
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

        <div class="container">
            <header class="hero">
                <h1>Événements</h1>
                <p>Découvrez et participez à nos événements</p>
            </header>

            <section class="events-section">
                <h2>Événements à venir</h2>

                <?php if (empty($events)): ?>
                    <div class="no-events">
                        <p>Aucun événement publié pour le moment.</p>
                    </div>
                <?php else: ?>
                    <div class="events-grid">
                        <?php foreach ($events as $event): ?>
                            <div class="event-card">
                                <div class="event-header">
                                    <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <span class="event-date">
                                        <?php
                                        $date = new DateTime($event['event_date']);
                                        echo $date->format('d/m/Y H:i');
                                        ?>
                                    </span>
                                </div>

                                <p class="event-description">
                                    <?php echo htmlspecialchars(substr($event['description'], 0, 150)) . '...'; ?>
                                </p>

                                <div class="event-footer">
                                    <span class="event-location">
                                        📍 <?php echo htmlspecialchars($event['location']); ?>
                                    </span>
                                    <span class="event-capacity">
                                        👥 <?php echo $event['capacity']; ?> places
                                    </span>
                                </div>

                                <div class="event-actions">
                                    <?php if (AuthService::isLoggedIn()): ?>
                                        <a href="event_detail.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">
                                            Voir détails
                                        </a>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-secondary">
                                            Connectez-vous pour s'inscrire
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

    </main>

</body>

</html>