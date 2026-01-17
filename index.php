<?php
require_once 'src/Database.php';
require_once 'src/models/Event.php';
require_once 'src/services/AuthService.php';

session_start();

// Get database connection
$database = Database::getInstance();
$mysqli = $database->getConnection();

// Get published events
$events = [];
$sql = "SELECT * FROM events WHERE status = 'published' ORDER BY event_date ASC LIMIT 6";
$result = $mysqli->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

require_once 'views/layouts/header.php';
?>

<div class="container">
    <header class="hero">
        <h1>Application Événements</h1>
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

<?php require_once 'views/layouts/footer.php'; ?>