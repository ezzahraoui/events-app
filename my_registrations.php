<?php
require_once 'src/Database.php';
require_once 'src/models/User.php';
require_once 'src/models/Event.php';
require_once 'src/models/Registration.php';
require_once 'src/services/AuthService.php';
require_once 'src/services/EmailService.php';

session_start();

AuthService::requireLogin();

// Block admin access
if (AuthService::isAdmin()) {
    header('Location: 403.php');
    exit;
}

$userId = AuthService::getCurrentUserId();
$registrations = Registration::findByUser($userId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes inscriptions - Événements</title>
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
                    <li><a href="my_registrations.php">Mes inscriptions</a></li>
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
    <div class="registrations-section">
        <header class="section-header">
            <h1>Mes inscriptions</h1>
            <p>Retrouvez tous les événements auxquels vous êtes inscrit.</p>
        </header>
        
        <?php if (empty($registrations)): ?>
            <div class="no-registrations">
                <div class="empty-state">
                    <h3>Vous n'êtes inscrit à aucun événement</h3>
                    <p>Découvrez nos événements à venir et participez à ceux qui vous intéressent.</p>
                    <a href="index.php" class="btn btn-primary">
                        Voir les événements
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="registrations-grid">
                <?php foreach ($registrations as $registration): ?>
                    <div class="registration-card">
                        <div class="registration-header">
                            <h3 class="registration-title">
                                <?php echo htmlspecialchars($registration->getEventTitle()); ?>
                            </h3>
                            <span class="registration-date">
                                <?php echo $registration->getRegistrationDate()->format('d/m/Y'); ?>
                            </span>
                        </div>
                        
                        <div class="registration-details">
                            <div class="detail-item">
                                <span class="detail-label">📅 Date:</span>
                                <span class="detail-value">
                                    <?php echo $registration->getEventDate()->format('d/m/Y à H:i'); ?>
                                </span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">📍 Lieu:</span>
                                <span class="detail-value">
                                    <?php echo htmlspecialchars($registration->getEventLocation()); ?>
                                </span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">🎟 Inscription:</span>
                                <span class="detail-value">
                                    <?php echo $registration->getRegistrationDate()->format('d/m/Y à H:i'); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="registration-status">
                            <span class="status-badge status-confirmed">
                                ✅ Confirmée
                            </span>
                        </div>
                        
                        <div class="registration-actions">
                            <a href="event_detail.php?id=<?php echo $registration->getEventId(); ?>" class="btn btn-outline" style="display:inline-flex; align-items:center; height:44px; padding: 0 24px;">
                                Voir détails
                            </a>
                            <?php 
                            // Allow cancellation only if event is in the future
                            $now = new DateTime();
                            if ($registration->getEventDate() && $registration->getEventDate() > $now): ?>
                                <form method="post" action="cancel_registration.php" style="margin:0;">
                                    <input type="hidden" name="event_id" value="<?php echo $registration->getEventId(); ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette inscription ?')">
                                        Annuler
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.registrations-section {
    max-width: 1200px;
    margin: 0 auto;
}

.section-header {
    text-align: center;
    margin-bottom: 40px;
}

.section-header h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    color: #2c3e50;
}

.section-header p {
    color: #666;
    font-size: 1.1rem;
}

.no-registrations {
    text-align: center;
    padding: 60px 20px;
}

.empty-state {
    background: white;
    padding: 60px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid #e1e8ed;
}

.empty-state h3 {
    margin-bottom: 15px;
    color: #2c3e50;
}

.empty-state p {
    color: #666;
    margin-bottom: 30px;
}

.registrations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 30px;
}

.registration-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid #e1e8ed;
    transition: all 0.3s ease;
}

.registration-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.15);
}

.registration-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.registration-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    flex: 1;
}

.registration-date {
    color: #666;
    font-size: 0.9rem;
    white-space: nowrap;
    margin-left: 15px;
}

.registration-details {
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    margin-bottom: 10px;
}

.detail-label {
    font-weight: 500;
    color: #666;
    min-width: 100px;
}

.detail-value {
    color: #333;
    flex: 1;
}

.registration-status {
    margin-bottom: 20px;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.status-confirmed {
    background: #d4edda;
    color: #155724;
}

.registration-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}

.registration-actions form {
    display: inline;
    margin: 0;
}

.registration-actions .btn {
    display: inline-flex;
    align-items: center;
    height: 44px;
    padding: 0 24px;
    line-height: 1;
}

@media (max-width: 768px) {
    .registrations-grid {
        grid-template-columns: 1fr;
    }
    
    .registration-header {
        flex-direction: column;
    }
    
    .registration-date {
        margin-left: 0;
        margin-top: 5px;
    }
    
    .detail-item {
        flex-direction: column;
    }
    
    .detail-label {
        min-width: auto;
        margin-bottom: 3px;
    }
}
</style>

    </main>

</body>
</html>