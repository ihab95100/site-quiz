<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Quiz Dev Web</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Quiz Dev Web</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="quiz.php">Démarrer le Quiz</a></li>
                        <?php if ($_SESSION['is_admin']): ?>
                            <li><a href="admin.php">Administration</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Se connecter</a></li>
                        <li><a href="register.php">S'inscrire</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="hero">
        <div class="container">
            <section class="hero-content">
                <h2>Bienvenue sur Quiz Dev Web</h2>
                <p>Testez vos connaissances en HTML, CSS et PHP avec nos quizzes interactifs !</p>
                <p>Que vous soyez débutant ou expert, nos questions vous aideront à maîtriser le développement web.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="quiz.php" class="btn btn-primary">Je suis prêt !</a>
                <?php else: ?>
                    <p>Pour commencer, veuillez vous connecter ou vous inscrire.</p>
                    <a href="login.php" class="btn btn-primary">Se connecter</a>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Quiz Dev Web. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>