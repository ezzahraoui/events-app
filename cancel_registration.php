<?php
require_once 'src/Database.php';
require_once 'src/models/Registration.php';
require_once 'src/services/AuthService.php';

session_start();

// Vérification utilisateur connecté
AuthService::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Requête invalide.';
    header('Location: my_registrations.php');
    exit;
}

$eventId = $_POST['event_id'] ?? 0;
$userId = AuthService::getCurrentUserId();

// Trouver l'inscription
$database = Database::getInstance();
$sql = "SELECT * FROM registrations WHERE event_id = ? AND user_id = ? AND status = 'confirmed'";
$stmt = $database->prepare($sql);
$stmt->bind_param("ii", $eventId, $userId);
$stmt->execute();

$result = $stmt->get_result();
$registrationRow = $result->fetch_assoc();
$stmt->close();

if (!$registrationRow) {
    $_SESSION['error'] = 'Inscription non trouvée.';
    header('Location: my_registrations.php');
    exit;
}

// Vérifier que c'est l'utilisateur qui annule sa propre inscription (owner-check)
if ($registrationRow['user_id'] !== $userId) {
    $_SESSION['error'] = 'Vous n\'avez pas la permission d\'annuler cette inscription.';
    header('Location: 403.php');
    exit;
}

// Annuler l'inscription (soft-update de status)
$updateSql = "UPDATE registrations SET status = 'cancelled' WHERE id = ?";
$updateStmt = $database->prepare($updateSql);
$updateStmt->bind_param("i", $registrationRow['id']);

if ($updateStmt->execute()) {
    $_SESSION['success'] = 'Inscription annulée avec succès !';
} else {
    $_SESSION['error'] = 'Une erreur est survenue lors de l\'annulation.';
}
$updateStmt->close();

header('Location: my_registrations.php');
exit;
