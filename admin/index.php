<?php
// Set UTF-8 header for proper character encoding
if (headers_sent() === false) {
    header('Content-Type: text/html; charset=utf-8');
}

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/models/Event.php';
require_once __DIR__ . '/../src/services/AuthService.php';

session_start();

// Vérification admin OBLIGATOIRE
AuthService::requireAdmin();

// Récupérer tous les événements
$events = Event::findAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tableau des Événements</title>
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
            <div class="admin-section">
                <div class="section-header">
                    <h1>Tous les Événements</h1>
                    <a href="create_event.php" class="btn btn-success">
                        + Créer un Événement
                    </a>
                </div>

                <?php if (empty($events)): ?>
                    <div class="no-events">
                        <p>Aucun événement pour le moment.</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Capacité</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event->getTitle()); ?></td>
                                    <td>
                                        <?php
                                        try {
                                            $date = $event->getEventDate();
                                            echo $date->format('d/m/Y H:i');
                                        } catch (Exception $e) {
                                            echo 'Date invalide';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($event->getLocation()); ?></td>
                                    <td><?php echo $event->getCapacity(); ?> places</td>
                                    <td>
                                        <span class="status-badge status-<?php echo $event->getStatus(); ?>">
                                            <?php echo ucfirst($event->getStatus()); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="edit_event.php?id=<?php echo $event->getId(); ?>" class="btn btn-primary btn-small">
                                            Éditer
                                        </a>
                                        <form method="post" action="delete_event.php" style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?php echo $event->getId(); ?>">
                                            <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Êtes-vous sûr ? Les inscriptions seront aussi supprimées.')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> Événements. Tous droits réservés.</p>
        </div>
    </footer>

    <style>
        .admin-section {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .section-header h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin: 0;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .admin-table thead {
            background: #34495e;
            color: white;
        }

        .admin-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .admin-table td {
            padding: 15px;
            border-bottom: 1px solid #f1f3f4;
        }

        .admin-table tbody tr:hover {
            background: #f8f9fa;
        }

        .admin-table .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-published {
            background: #d4edda;
            color: #155724;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .admin-table {
                font-size: 0.9rem;
            }

            .admin-table th,
            .admin-table td {
                padding: 10px;
            }

            .admin-table .actions {
                flex-direction: column;
            }
        }
    </style>
</body>

</html>