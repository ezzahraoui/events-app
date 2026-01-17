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

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    
    // Validation
    if (empty($email)) {
        $errors[] = 'L\'email est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email n\'est pas valide.';
    }
    
    if (empty($firstName)) {
        $errors[] = 'Le prénom est obligatoire.';
    }
    
    if (empty($lastName)) {
        $errors[] = 'Le nom est obligatoire.';
    }
    
    if (empty($password)) {
        $errors[] = 'Le mot de passe est obligatoire.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
    
    if (empty($errors)) {
        if (AuthService::registerUser($email, $password, $firstName, $lastName)) {
            header('Location: login.php');
            exit;
        } else {
            $errors[] = $_SESSION['error'] ?? 'Une erreur est survenue lors de l\'inscription.';
        }
    }
}

$pageTitle = 'Inscription';
require_once 'views/layouts/header.php';
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h2>Inscription</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" class="auth-form">
                <div class="form-group">
                    <label for="first_name">Prénom</label>
                    <input 
                        type="text" 
                        id="first_name" 
                        name="first_name" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="last_name">Nom</label>
                    <input 
                        type="text" 
                        id="last_name" 
                        name="last_name" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                        required
                    >
                </div>
                
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
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-control" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-success btn-full">
                    S'inscrire
                </button>
            </form>
            
            <div class="auth-links">
                <p>Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
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