<?php
session_start();
// Assurez-vous que le chemin vers db_connect.php est correct.
require_once 'db_connect.php'; 

// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$categories = [];
$user_scores = []; // Tableau pour stocker les scores de l'utilisateur par catégorie

try {
    // Récupérer toutes les catégories disponibles de la base de données.
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- NOUVEAU : Récupérer le meilleur score de l'utilisateur pour chaque catégorie ---
    // Nous allons chercher le score le plus élevé pour chaque catégorie pour l'utilisateur connecté.
    $stmt_scores = $pdo->prepare("
        SELECT category_id, MAX(score) as best_score
        FROM quiz_results
        WHERE user_id = :user_id
        GROUP BY category_id
    ");
    $stmt_scores->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_scores->execute();
    
    // Organiser les scores dans un tableau associatif pour un accès facile (category_id => best_score)
    while ($row = $stmt_scores->fetch(PDO::FETCH_ASSOC)) {
        $user_scores[$row['category_id']] = $row['best_score'];
    }

} catch (PDOException $e) {
    echo "Erreur lors du chargement des catégories ou des scores : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélectionner un Quiz - Quiz Dev Web</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* Quelques styles pour l'affichage du score */
        .category-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: space-between; /* Aligne le titre/score et le bouton */
            align-items: center; /* Centre verticalement */
        }
        .category-info {
            flex-grow: 1; /* Prend l'espace disponible */
        }
        .category-score {
            font-weight: bold;
            color: #007bff; /* Couleur du score */
            margin-left: 20px;
            white-space: nowrap; /* Empêche le score de passer à la ligne */
        }
        .category-item h3 {
            margin-top: 0;
            margin-bottom: 5px;
        }
        .category-item .btn {
            margin-left: 20px; /* Espace entre le score et le bouton */
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Quiz Dev Web</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="select_quiz.php">Démarrer un Quiz</a></li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="admin.php">Administration</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="select-quiz-page">
        <div class="container">
            <h2>Choisissez un Quiz par Domaine</h2>
            <?php if (!empty($categories)): ?>
                <div class="category-list">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-item">
                            <div class="category-info">
                                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                                <?php 
                                // Vérifier si un score existe pour cette catégorie
                                if (isset($user_scores[$category['id']])) {
                                    echo '<p class="category-score">Votre meilleur score : ' . htmlspecialchars($user_scores[$category['id']]) . '</p>';
                                } else {
                                    echo '<p class="category-score">Pas encore de score</p>';
                                }
                                ?>
                            </div>
                            <a href="quiz.php?category_id=<?php echo $category['id']; ?>" class="btn btn-primary">Démarrer le Quiz</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Aucune catégorie de quiz disponible pour le moment. Veuillez contacter l'administrateur.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Quiz Dev Web. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>