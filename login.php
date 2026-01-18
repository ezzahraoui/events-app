<?php
// Set UTF-8 header for proper character encoding
if (headers_sent() === false) {
    header('Content-Type: text/html; charset=utf-8');
}

require_once 'src/Database.php';
require_once 'src/models/User.php';
require_once 'src/services/AuthService.php';
require_once 'src/services/EmailService.php';

session_start();

// If user is already logged in, redirect to home
if (AuthService::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        if (AuthService::login($email, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Événements</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <header class="main-header">
        <nav class="navbar">
            <div class="nav-container">
                <a href="index.php" class="nav-brand">
                    <h1>Événements</h1>
                </a>

                <ul class="nav-menu">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="register.php">Inscription</a></li>
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
            <div class="auth-container">
                <div class="auth-card">
                    <h2>Connexion</h2>

                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="auth-form">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">
                            Se connecter
                        </button>
                    </form>

                    <div class="auth-links">
                        <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .auth-container {
                max-width: 400px;
                margin: 50px auto;
            }

            .auth-card {
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                border: 1px solid #e1e8ed;
            }

            .auth-card h2 {
                text-align: center;
                margin-bottom: 30px;
                color: #2c3e50;
            }

            .auth-form {
                margin-bottom: 20px;
            }

            .btn-full {
                width: 100%;
            }

            .auth-links {
                text-align: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #f1f3f4;
            }

            .auth-links a {
                color: #3498db;
                text-decoration: none;
            }

            .auth-links a:hover {
                text-decoration: underline;
            }
        </style>

    </main>

    <footer class="main-footer">
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> Événements. Tous droits réservés.</p>
        </div>
    </footer>
</body>

</html>