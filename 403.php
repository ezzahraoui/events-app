<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé - Événements</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <div class="container">
        <div class="error-page">
            <div class="error-code">403</div>
            <h1>Accès refusé</h1>
            <p>Vous n'avez pas la permission d'accéder à cette ressource.</p>

            <div class="error-actions">
                <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                <?php
                session_start();
                if (isset($_SESSION['user_id'])):
                    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ROLE_ADMIN'):
                ?>
                        <a href="admin/index.php" class="btn btn-secondary">Admin</a>
                    <?php else: ?>
                        <a href="my_registrations.php" class="btn btn-secondary">Mes inscriptions</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">Se connecter</a>
                <?php endif; ?>
            </div>

            <div class="error-info">
                <p><strong>Pourquoi cette erreur ?</strong></p>
                <ul>
                    <li>Vous essayez d'accéder à des ressources qui ne vous appartiennent pas</li>
                    <li>Cette page est réservée aux administrateurs</li>
                    <li>Vous n'êtes pas connecté</li>
                </ul>
            </div>
        </div>
    </div>

    <style>
        .error-page {
            text-align: center;
            max-width: 600px;
            margin: 80px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e1e8ed;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #e74c3c;
            margin-bottom: 20px;
            line-height: 1;
        }

        .error-page h1 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .error-page p {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .error-actions {
            margin-bottom: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .error-info {
            text-align: left;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .error-info p {
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }

        .error-info ul {
            margin: 0;
            padding-left: 20px;
        }

        .error-info li {
            margin-bottom: 5px;
            color: #666;
        }
    </style>
</body>

</html>