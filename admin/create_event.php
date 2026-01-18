<?php
// Set UTF-8 header for proper character encoding
if (headers_sent() === false) {
    header('Content-Type: text/html; charset=utf-8');
}

require_once '../src/Database.php';
require_once '../src/models/Event.php';
require_once '../src/services/AuthService.php';

session_start();

// Vérification admin OBLIGATOIRE
AuthService::requireAdmin();

$errors = [];
$userId = AuthService::getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = new Event();
    $event->setTitle($_POST['title'] ?? '');
    $event->setDescription($_POST['description'] ?? '');
    $event->setLocation($_POST['location'] ?? '');
    $event->setCapacity((int)($_POST['capacity'] ?? 0));
    $event->setImageUrl($_POST['image_url'] ?? null);
    $event->setStatus($_POST['status'] ?? 'draft');
    $event->setCreatedBy($userId);

    // Traiter la date
    try {
        if (!empty($_POST['event_date'])) {
            $dateTime = DateTime::createFromFormat('Y-m-d H:i', $_POST['event_date']);
            if ($dateTime) {
                $event->setEventDate($dateTime);
            }
        }
    } catch (Exception $e) {
        $errors[] = 'Format de date invalide.';
    }

    // Valider
    $validationErrors = $event->validate();
    if (!empty($validationErrors)) {
        $errors = array_merge($errors, $validationErrors);
    }

    if (empty($errors)) {
        if ($event->save()) {
            $_SESSION['success'] = 'Événement créé avec succès !';
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Une erreur est survenue lors de la création.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Créer Événement</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <header class="main-header">
        <nav class="navbar">
            <div class="nav-container">
                <a href="../index.php" class="nav-brand">
                    <h1>Événements - Admin</h1>
                </a>

                <ul class="nav-menu">
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="registrations.php">Inscriptions</a></li>
                    <li>
                        <span class="user-welcome">
                            Bonjour, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </span>
                    </li>
                    <li><a href="../logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="form-card">
                    <h1>Créer un Événement</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error">
                            <?php foreach ($errors as $error): ?>
                                <div><?php echo htmlspecialchars($error); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="event-form">
                        <div class="form-group">
                            <label for="title">Titre *</label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                class="form-control"
                                value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                rows="5"
                                required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="event_date">Date et Heure *</label>
                                <input
                                    type="datetime-local"
                                    id="event_date"
                                    name="event_date"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="location">Lieu *</label>
                                <input
                                    type="text"
                                    id="location"
                                    name="location"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                                    required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="capacity">Capacité *</label>
                                <input
                                    type="number"
                                    id="capacity"
                                    name="capacity"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['capacity'] ?? '50'); ?>"
                                    min="1"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="status">Statut *</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="draft" <?php echo (($_POST['status'] ?? '') === 'draft' ? 'selected' : ''); ?>>Brouillon</option>
                                    <option value="published" <?php echo (($_POST['status'] ?? '') === 'published' ? 'selected' : ''); ?>>Publié</option>
                                    <option value="cancelled" <?php echo (($_POST['status'] ?? '') === 'cancelled' ? 'selected' : ''); ?>>Annulé</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image_url">URL Image</label>
                            <input
                                type="url"
                                id="image_url"
                                name="image_url"
                                class="form-control"
                                value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success btn-large">
                                Créer l'Événement
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e1e8ed;
        }

        .form-card h1 {
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .event-form {
            margin-top: 30px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-large {
            padding: 14px 28px;
            font-size: 1rem;
        }

        textarea.form-control {
            resize: vertical;
            font-family: inherit;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</body>

</html>