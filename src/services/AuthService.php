<?php
class AuthService
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return self::isLoggedIn() &&
            isset($_SESSION['user_role']) &&
            $_SESSION['user_role'] === 'ROLE_ADMIN';
    }

    public static function getCurrentUserId(): ?int
    {
        return self::isLoggedIn() ? $_SESSION['user_id'] : null;
    }

    public static function getCurrentUser(): ?User
    {
        $userId = self::getCurrentUserId();
        return $userId ? User::findById($userId) : null;
    }

    public static function login(string $email, string $password): bool
    {
        $user = User::findByEmail($email);

        if ($user && $user->verifyPassword($password)) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_name'] = $user->getFullName();
            $_SESSION['user_role'] = $user->getRole();

            // Regenerate session ID for security
            session_regenerate_id(true);

            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        // Unset all session variables
        $_SESSION = [];

        // Destroy the session
        session_destroy();

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
            header('Location: login.php');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            $_SESSION['error'] = "Accès réservé aux administrateurs.";
            header('Location: 403.php');
            exit;
        }
    }

    public static function canAccess(int $resourceUserId): bool
    {
        return self::isAdmin() ||
            (self::isLoggedIn() && self::getCurrentUserId() === $resourceUserId);
    }

    public static function canAccessResource(int $resourceUserId): void
    {
        if (!self::canAccess($resourceUserId)) {
            $_SESSION['error'] = "Vous n'avez pas la permission d'accéder à cette ressource.";
            header('Location: 403.php');
            exit;
        }
    }

    public static function registerUser(string $email, string $password, string $firstName, string $lastName): bool
    {
        // Check if email already exists
        if (User::findByEmail($email)) {
            $_SESSION['error'] = "Cet email est déjà utilisé.";
            return false;
        }

        // Create new user
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        // Validate user data
        $errors = $user->validate();
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            return false;
        }

        // Save user
        if ($user->save()) {
            $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            return true;
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de l'inscription.";
            return false;
        }
    }

    public static function updateUser(int $userId, string $email, string $firstName, string $lastName): bool
    {
        $user = User::findById($userId);

        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            return false;
        }

        // Check if email is used by another user
        $existingUser = User::findByEmail($email);
        if ($existingUser && $existingUser->getId() !== $userId) {
            $_SESSION['error'] = "Cet email est déjà utilisé par un autre utilisateur.";
            return false;
        }

        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        // Validate user data
        $errors = $user->validate();
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            return false;
        }

        // Save user
        if ($user->save()) {
            $_SESSION['success'] = "Profil mis à jour avec succès.";

            // Update session data
            if ($userId === self::getCurrentUserId()) {
                $_SESSION['user_email'] = $user->getEmail();
                $_SESSION['user_name'] = $user->getFullName();
            }

            return true;
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour.";
            return false;
        }
    }
}
