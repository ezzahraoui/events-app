<?php
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

$pageTitle = 'Connexion';
require_once 'views/layouts/header.php';
?>

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
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        required
                    >
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
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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

<?php require_once 'views/layouts/footer.php'; ?>