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

if (!$event || $event->getStatus() !== 'published') {
    $_SESSION['error'] = 'Événement non trouvé.';
    header('Location: index.php');
    exit;
}

$userId = AuthService::getCurrentUserId();
$isRegistered = Registration::isUserRegistered($eventId, $userId);
$hasCapacity = Registration::hasAvailableCapacity($eventId);

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isRegistered && $hasCapacity) {
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

$pageTitle = $event->getTitle();
require_once 'views/layouts/header.php';
?>

<div class="container">
    <div class="event-detail">
        <div class="event-header-section">
            <h1><?php echo htmlspecialchars($event->getTitle()); ?></h1>
            <div class="event-meta">
                <span class="event-date-large">
                    📅 <?php echo $event->getEventDate()->format('d/m/Y à H:i'); ?>
                </span>
                <span class="event-location-large">
                    📍 <?php echo htmlspecialchars($event->getLocation()); ?>
                </span>
                <span class="event-capacity-large">
                    👥 <?php echo $event->getCapacity(); ?> places
                </span>
            </div>
        </div>
        
        <div class="event-content">
            <div class="event-description-full">
                <h2>Description</h2>
                <p><?php echo nl2br(htmlspecialchars($event->getDescription())); ?></p>
            </div>
            
            <div class="event-registration-section">
                <?php if ($isRegistered): ?>
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
                            <p>En vous inscrivant, vous recevrez un email de confirmation avec tous les détails de l'événement.</p>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-large">
                            S'inscrire à cet événement
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.event-detail {
    max-width: 800px;
    margin: 0 auto;
}

.event-header-section {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.event-header-section h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: #2c3e50;
}

.event-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-top: 20px;
}

.event-date-large,
.event-location-large,
.event-capacity-large {
    font-size: 1.1rem;
    color: #666;
}

.event-content {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.event-description-full h2 {
    margin-bottom: 20px;
    color: #2c3e50;
}

.event-description-full p {
    line-height: 1.8;
    color: #555;
    margin-bottom: 30px;
}

.event-registration-section {
    border-top: 2px solid #f1f3f4;
    padding-top: 30px;
    margin-top: 30px;
}

.registration-form {
    text-align: center;
    max-width: 400px;
    margin: 0 auto;
}

.registration-form h3 {
    margin-bottom: 15px;
    color: #2c3e50;
}

.btn-large {
    padding: 15px 30px;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .event-header-section h1 {
        font-size: 2rem;
    }
    
    .event-meta {
        flex-direction: column;
        gap: 15px;
    }
    
    .event-header-section,
    .event-content {
        padding: 20px;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>