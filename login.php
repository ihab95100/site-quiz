<?php
session_start();

// --- Lignes de débogage : À ACTIVER TEMPORAIREMENT si vous ne voyez aucune erreur ---
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// ---------------------------------------------------------------------------------

// Assurez-vous que le chemin vers db_connect.php est correct.
// Si votre fichier db_connect.php est dans le dossier 'includes' situé un niveau au-dessus de 'public',
// le chemin est '../includes/db_connect.php'.
// Si db_connect.php est directement à la racine de votre projet (un niveau au-dessus de 'public'),
// le chemin est '../db_connect.php'.
// Votre chemin actuel 'db_connect.php' implique qu'il est dans le même dossier que login.php, ce qui n'est pas idéal pour la sécurité.
// Pour suivre la structure que je vous ai donnée, il devrait être:
require_once 'db_connect.php'; // Chemin recommandé pour la sécurité et la cohérence.
// Si vous avez mis db_connect.php directement dans le dossier 'public', utilisez:
// require_once 'db_connect.php'; // Non recommandé pour la sécurité.


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    if (empty($username_or_email) || empty($password)) {
        $message = 'Tous les champs sont requis.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username_or_email, $username_or_email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                // --- C'EST ICI LA CORRECTION MAJEURE ---
                // Il faut spécifier 'Location:' pour une redirection HTTP.
                header('Location: index.php'); 
                exit(); // Toujours appeler exit() après une redirection pour s'assurer que le script s'arrête.
            } else {
                $message = 'Nom d\'utilisateur/Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            // Affichage détaillé de l'erreur de base de données (utile en débogage)
            $message = 'Erreur de base de données : ' . $e->getMessage();
            // Vous pouvez aussi loguer $e->getMessage() dans un fichier pour le débogage en production
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Quiz Dev Web</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h2>Connexion</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['registration']) && $_GET['registration'] == 'success'): ?>
            <p class="success-message">Inscription réussie ! Veuillez vous connecter.</p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username_or_email">Nom d'utilisateur ou Email:</label>
                <input type="text" id="username_or_email" name="username_or_email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Se connecter</button>
        </form>
        <p>Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous ici</a>.</p>
    </div>
</body>
</html>