<?php
require_once '../../src/Database.php';
require_once '../../src/models/User.php';
require_once '../../src/services/AuthService.php';

session_start();
AuthService::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Requête invalide.';
    header('Location: index.php');
    exit;
}

$userId = $_POST['id'] ?? 0;
$user = User::findById($userId);

if (!$user) {
    $_SESSION['error'] = 'Utilisateur non trouvé.';
    header('Location: index.php');
    exit;
}

$currentUserId = AuthService::getCurrentUserId();

if ($userId == $currentUserId) {
    $_SESSION['error'] = 'Vous ne pouvez pas vous supprimer vous-même.';
    header('Location: index.php');
    exit;
}

if ($user->delete()) {
    $_SESSION['success'] = 'Utilisateur supprimé avec succès !';
} else {
    $_SESSION['error'] = 'Une erreur est survenue lors de la suppression.';
}

header('Location: index.php');
exit;
