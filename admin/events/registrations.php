<?php
require_once '../../src/Database.php';
require_once '../../src/models/Registration.php';
require_once '../../src/models/Event.php';
require_once '../../src/models/User.php';
require_once '../../src/services/AuthService.php';

session_start();
AuthService::requireAdmin();

$database = Database::getInstance();
$mysqli = $database->getConnection();

$sql = "
    SELECT r.*, e.title as event_title, u.email as user_email, u.first_name, u.last_name
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    JOIN users u ON r.user_id = u.id
    ORDER BY e.title, r.registration_date DESC
";

$result = $mysqli->query($sql);
$registrations = [];
while ($row = $result->fetch_assoc()) {
    $registrations[] = $row;
}
$result->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscriptions - Administration</title>
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
        .registrations-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .registrations-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .registrations-table th,
        .registrations-table td {
            padding: 15px 20px;
            text-align: left;
        }
        .registrations-table th {
            background: #34495e;
            color: white;
            font-weight: 500;
        }
        .registrations-table tbody tr {
            border-bottom: 1px solid #ecf0f1;
        }
        .registrations-table tbody tr:hover {
            background: #f8f9fa;
        }
        .registrations-table td {
            color: #2c3e50;
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
        <h1>Administration - Inscriptions</h1>
        <nav class="admin-nav">
            <a href="../index.php" class="back">← Retour</a>
            <a href="../logout.php">Déconnexion</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h2>Inscriptions</h2>
        </div>

        <?php if (empty($registrations)): ?>
            <div class="registrations-table">
                <div class="empty-state">
                    <h3>Aucune inscription</h3>
                    <p>Aucune inscription n'a été effectuée pour le moment.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="registrations-table">
                <table>
                    <thead>
                        <tr>
                            <th>Événement</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Date d'inscription</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reg['event_title']); ?></td>
                                <td><?php echo htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($reg['user_email']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($reg['registration_date'])); ?></td>
                                <td><?php echo $reg['status']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
