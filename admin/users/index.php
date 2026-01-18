<?php
require_once '../../src/Database.php';
require_once '../../src/models/User.php';
require_once '../../src/services/AuthService.php';

session_start();
AuthService::requireAdmin();

$database = Database::getInstance();
$stmt = $database->prepare("SELECT * FROM users WHERE role = 'ROLE_USER' ORDER BY id");
$stmt->execute();
$result = $stmt->get_result();
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs</title>
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
            max-width: 1000px;
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
        .users-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .users-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .users-table th,
        .users-table td {
            padding: 15px 20px;
            text-align: left;
        }
        .users-table th {
            background: #34495e;
            color: white;
            font-weight: 500;
        }
        .users-table tbody tr {
            border-bottom: 1px solid #ecf0f1;
        }
        .users-table tbody tr:hover {
            background: #f8f9fa;
        }
        .users-table td {
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
        <h1>Administration - Utilisateurs</h1>
        <nav class="admin-nav">
            <a href="../index.php" class="back">← Retour</a>
            <a href="../logout.php">Déconnexion</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h2>Utilisateurs</h2>
            <a href="create.php" class="btn btn-primary">+ Ajouter un utilisateur</a>
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

        <?php if (empty($users)): ?>
            <div class="users-table">
                <div class="empty-state">
                    <h3>Aucun utilisateur</h3>
                    <p>Aucun utilisateur n'a été créé pour le moment.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary">Modifier</a>
                                        <a href="delete.php?id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
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
