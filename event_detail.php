<?php
require_once 'src/Database.php';
require_once 'src/models/User.php';
require_once 'src/models/Event.php';
require_once 'src/models/Registration.php';
require_once 'src/services/AuthService.php';
require_once 'src/services/EmailService.php';

session_start();

AuthService::requireLogin();

$eventId = $_GET['id'] ?? 0;
$event = Event::findById($eventId);

if (!$event) {
    $_SESSION['error'] = 'Événement non trouvé.';
    header('Location: index.php');
    exit;
}

$userId = AuthService::getCurrentUserId();
$isRegistered = Registration::isUserRegistered($eventId, $userId);
$hasCapacity = Registration::hasAvailableCapacity($eventId);

// Handle registration - BLOCK ADMIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isRegistered && $hasCapacity && !AuthService::isAdmin()) {
    $registration = new Registration($eventId, $userId);

    if ($registration->save()) {
        // Send confirmation email
        EmailService::sendRegistrationConfirmation($userId, $eventId);

        $_SESSION['success'] = 'Inscription confirmée ! Vous recevrez un email de confirmation.';
        header('Location: my_registrations.php');
        exit;
    } else {
        $_SESSION['error'] = 'Une erreur est survenue lors de l\'inscription.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event->getTitle()); ?> - Événements</title>
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
            <div class="event-detail">
                <div class="event-header-section">
                    <h1><?php echo htmlspecialchars($event->getTitle()); ?></h1>
                    <div class="event-meta">
                        <div class="event-date-large">
                            📅 <?php
                                try {
                                    $date = $event->getEventDate();
                                    echo $date->format('d/m/Y H:i');
                                } catch (Exception $e) {
                                    echo 'Date invalide';
                                }
                                ?>
                        </div>
                        <div class="event-location-large">
                            📍 <?php echo htmlspecialchars($event->getLocation()); ?>
                        </div>
                        <div class="event-capacity-large">
                            👥 <?php echo $event->getCapacity(); ?> places
                        </div>
                    </div>
                </div>

                <div class="event-description-full">
                    <h2>Description</h2>
                    <p><?php echo nl2br(htmlspecialchars($event->getDescription())); ?></p>
                </div>

                <div class="event-registration-section">
                    <?php if (AuthService::isAdmin()): ?>
                        <div class="alert alert-info">
                            👨‍💼 En tant qu'administrateur, vous ne pouvez pas vous inscrire aux événements.
                        </div>
                    <?php elseif ($isRegistered): ?>
                        <div class="alert alert-success">
                            ✅ Vous êtes déjà inscrit à cet événement.
                        </div>
                        <a href="my_registrations.php" class="btn btn-primary">
                            Voir mes inscriptions
                        </a>
                    <?php elseif (!$hasCapacity): ?>
                        <div class="alert alert-error">
                            ❌ Cet événement est complet.
                        </div>
                    <?php else: ?>
                        <form method="post" class="registration-form">
                            <div class="form-group">
                                <h3>Confirmer votre inscription</h3>
                                <p>Vous allez vous inscrire à cet événement. Un email de confirmation vous sera envoyé.</p>
                            </div>
                            <button type="submit" class="btn btn-success btn-large">
                                ✅ M'inscrire à cet événement
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

</body>

</html>