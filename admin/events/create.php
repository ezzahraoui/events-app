<?php
require_once '../../src/Database.php';
require_once '../../src/models/Event.php';
require_once '../../src/services/AuthService.php';

session_start();
AuthService::requireAdmin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = new Event();
    $event->setTitle($_POST['title'] ?? '');
    $event->setDescription($_POST['description'] ?? '');
    $event->setLocation($_POST['location'] ?? '');
    $event->setCapacity($_POST['capacity'] ?? 50);
    $event->setImageUrl($_POST['image_url'] ?? '');
    $event->setCreatedBy($_SESSION['user_id']);

    try {
        $eventDate = DateTime::createFromFormat('Y-m-d H:i', $_POST['event_date'] ?? '');
        if ($eventDate) {
            $event->setEventDate($eventDate);
        } else {
            $error = 'La date n\'est pas valide.';
        }
    } catch (Exception $e) {
        $error = 'La date n\'est pas valide.';
    }

    if (empty($error)) {
        $errors = $event->validate();
        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } else {
            if ($event->save()) {
                $_SESSION['success'] = 'Événement créé avec succès !';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Une erreur est survenue lors de la création.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un événement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }
        .admin-header {
            background: #2c3e50;
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            font-size: 1.3rem;
        }
        .admin-nav {
            display: flex;
            gap: 15px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: #34495e;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .admin-nav a:hover {
            background: #3498db;
        }
        .admin-nav a.back {
            background: #7f8c8d;
        }
        .main-content {
            padding: 30px 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .page-header {
            margin-bottom: 30px;
        }
        .page-header h2 {
            color: #2c3e50;
            font-size: 1.5rem;
        }
        .form-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1>Administration - Événements</h1>
        <nav class="admin-nav">
            <a href="index.php" class="back">← Retour</a>
            <a href="../../logout.php">Déconnexion</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h2>Créer un événement</h2>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label for="title">Titre *</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="event_date">Date et heure *</label>
                        <input type="datetime-local" id="event_date" name="event_date" value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="capacity">Capacité *</label>
                        <input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($_POST['capacity'] ?? 50); ?>" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Lieu *</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="image_url">URL de l'image (optionnel)</label>
                    <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>" placeholder="https://...">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Créer</button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
