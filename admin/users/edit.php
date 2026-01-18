<?php
require_once '../../src/Database.php';
require_once '../../src/models/User.php';
require_once '../../src/services/AuthService.php';

session_start();
AuthService::requireAdmin();

$userId = $_GET['id'] ?? 0;
$user = User::findById($userId);

if (!$user) {
    $_SESSION['error'] = 'Utilisateur non trouvé.';
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';

    if (empty($email) || empty($firstName) || empty($lastName)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        $errors = $user->validate();
        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } else {
            if ($user->save()) {
                $_SESSION['success'] = 'Utilisateur modifié avec succès !';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Une erreur est survenue lors de la modification.';
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
    <title>Modifier un utilisateur</title>
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
            max-width: 600px;
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
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
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
        <h1>Administration - Utilisateurs</h1>
        <nav class="admin-nav">
            <a href="index.php" class="back">← Retour</a>
            <a href="../logout.php">Déconnexion</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h2>Modifier un utilisateur</h2>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
                </div>

                <div class="form-group">
                    <label for="first_name">Prénom *</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user->getFirstName()); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Nom *</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user->getLastName()); ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
