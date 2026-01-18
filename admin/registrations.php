<?php
// Set UTF-8 header for proper character encoding
if (headers_sent() === false) {
    header('Content-Type: text/html; charset=utf-8');
}

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/models/Registration.php';
require_once __DIR__ . '/../src/models/Event.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/services/AuthService.php';

session_start();

// Vérification admin OBLIGATOIRE
AuthService::requireAdmin();

// Récupérer toutes les inscriptions
$database = Database::getInstance();
$mysqli = $database->getConnection();

$sql = "SELECT r.*, e.title as event_title, u.email as user_email, u.first_name, u.last_name 
        FROM registrations r 
        JOIN events e ON r.event_id = e.id 
        JOIN users u ON r.user_id = u.id 
        ORDER BY e.title, r.registration_date DESC";

$result = $mysqli->query($sql);
$registrations = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Inscriptions</title>
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
                    <h1>Toutes les Inscriptions</h1>
                    <a href="index.php" class="btn btn-secondary">
                        ← Retour au Dashboard
                    </a>
                </div>

                <?php if (empty($registrations)): ?>
                    <div class="no-events">
                        <p>Aucune inscription pour le moment.</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Événement</th>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Date d'Inscription</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reg['event_title']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($reg['user_email']); ?></td>
                                    <td>
                                        <?php
                                        try {
                                            $date = new DateTime($reg['registration_date']);
                                            echo $date->format('d/m/Y H:i');
                                        } catch (Exception $e) {
                                            echo 'Date invalide';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $reg['status']; ?>">
                                            <?php echo ucfirst($reg['status']); ?>
                                        </span>
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

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
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
        }
    </style>
</body>

</html>