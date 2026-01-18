<?php
require_once '../../src/Database.php';
require_once '../../src/models/Event.php';
require_once '../../src/services/AuthService.php';

session_start();
AuthService::requireAdmin();

$events = Event::findAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Administration</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .page-header h2 {
            color: #2c3e50;
            font-size: 1.5rem;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
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
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .events-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .events-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .events-table th,
        .events-table td {
            padding: 15px 20px;
            text-align: left;
        }
        .events-table th {
            background: #34495e;
            color: white;
            font-weight: 500;
        }
        .events-table tbody tr {
            border-bottom: 1px solid #ecf0f1;
        }
        .events-table tbody tr:hover {
            background: #f8f9fa;
        }
        .events-table td {
            color: #2c3e50;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        .empty-state h3 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1>Administration - Événements</h1>
        <nav class="admin-nav">
            <a href="../../admin/index.php" class="back">← Retour</a>
            <a href="../../logout.php">Déconnexion</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h2>Événements</h2>
            <a href="create.php" class="btn btn-primary">+ Créer un événement</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($events)): ?>
            <div class="events-table">
                <div class="empty-state">
                    <h3>Aucun événement</h3>
                    <p>Aucun événement n'a été créé pour le moment.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="events-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Places</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo $event->getId(); ?></td>
                                <td><?php echo htmlspecialchars($event->getTitle()); ?></td>
                                <td><?php echo $event->getEventDate()->format('d/m/Y H:i'); ?></td>
                                <td><?php echo htmlspecialchars($event->getLocation()); ?></td>
                                <td><?php echo $event->getCapacity(); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit.php?id=<?php echo $event->getId(); ?>" class="btn btn-secondary btn-small">Modifier</a>
                                        <a href="delete.php?id=<?php echo $event->getId(); ?>" class="btn btn-danger btn-small" onclick="return confirm('Supprimer cet événement ?');">Supprimer</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
