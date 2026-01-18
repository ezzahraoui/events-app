<?php
require_once '../../src/Database.php';
require_once '../../src/models/Event.php';
require_once '../../src/services/AuthService.php';

session_start();
AuthService::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Requête invalide.';
    header('Location: index.php');
    exit;
}

$eventId = $_POST['event_id'] ?? 0;
$event = Event::findById($eventId);

if (!$event) {
    $_SESSION['error'] = 'Événement non trouvé.';
    header('Location: index.php');
    exit;
}

if ($event->delete()) {
    $_SESSION['success'] = 'Événement supprimé avec succès !';
} else {
    $_SESSION['error'] = 'Une erreur est survenue lors de la suppression.';
}

header('Location: index.php');
exit;
