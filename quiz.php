<?php
session_start();

// --- LIGNES DE DÉBOGAGE : Laissez-les activées si vous rencontrez des problèmes ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ---------------------------------------------------------------------------------

// Assurez-vous que le chemin vers db_connect.php est correct.
// Si votre structure est quiz_project/public/quiz.php et quiz_project/includes/db_connect.php
require_once 'db_connect.php'; 

// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// --- NOUVEAU : Récupérer l'ID de la catégorie sélectionnée ---
$selected_category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Si aucune catégorie n'est sélectionnée ou invalide, rediriger vers la page de sélection de quiz
if ($selected_category_id === 0) {
    header('Location: select_quiz.php');
    exit();
}

$questions = [];
$current_question_index = isset($_GET['q']) ? (int)$_GET['q'] : 0;
// Récupérer le score actuel de la session
$user_score = isset($_SESSION['score']) ? $_SESSION['score'] : 0;
$total_questions = 0;

try {
    // --- MODIFICATION MAJEURE : Récupérer les questions FILTRÉES par catégorie ---
    $sql_questions = "SELECT q.id AS question_id, q.question_text, c.name AS category_name
                      FROM questions q
                      JOIN categories c ON q.category_id = c.id
                      WHERE q.category_id = ?
                      ORDER BY RAND()"; // Mélanger les questions pour cette catégorie
    
    $stmt = $pdo->prepare($sql_questions);
    $stmt->execute([$selected_category_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Charger les options pour chaque question récupérée
    foreach ($questions as $key => $question) {
        $stmt_options = $pdo->prepare("SELECT id AS option_id, option_text, is_correct FROM options WHERE question_id = ? ORDER BY RAND()");
        $stmt_options->execute([$question['question_id']]);
        $questions[$key]['options'] = $stmt_options->fetchAll(PDO::FETCH_ASSOC);
    }
    $total_questions = count($questions);

    // Gérer la soumission du formulaire de quiz
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_answer'])) {
        $selected_option_id = isset($_POST['option']) ? (int)$_POST['option'] : 0;
        $current_question_id = isset($_POST['question_id']) ? (int)$_POST['question_id'] : 0;

        // Récupérer l'option correcte pour la question actuelle
        $stmt_correct = $pdo->prepare("SELECT is_correct FROM options WHERE id = ? AND question_id = ?");
        $stmt_correct->execute([$selected_option_id, $current_question_id]);
        $is_correct = $stmt_correct->fetchColumn();

        if ($is_correct) {
            // Incrémenter le score
            $_SESSION['score'] = (isset($_SESSION['score']) ? $_SESSION['score'] : 0) + 1;
            $user_score = $_SESSION['score']; // Mettre à jour la variable locale
        }

        // Passer à la question suivante ou terminer le quiz
        $next_question_index = $current_question_index + 1;
        if ($next_question_index < $total_questions) {
            // --- MODIFICATION : Conserver le category_id dans la redirection ---
            header('Location: quiz.php?category_id=' . $selected_category_id . '&q=' . $next_question_index);
            exit();
        } else {
            // Quiz terminé
            // Enregistrer le résultat dans la base de données
            $user_id = $_SESSION['user_id'];
            $stmt_insert_result = $pdo->prepare("INSERT INTO quiz_results (user_id, score, total_questions, category_id) VALUES (?, ?, ?, ?)");
            $stmt_insert_result->execute([$user_id, $user_score, $total_questions, $selected_category_id]);

            // --- MODIFICATION : Conserver le category_id dans la redirection de fin ---
            header('Location: quiz.php?category_id=' . $selected_category_id . '&finished=true');
            exit();
        }
    }

} catch (PDOException $e) {
    // Afficher l'erreur de base de données en cas de problème de requête
    echo "Erreur de base de données : " . $e->getMessage();
    exit();
}

// --- NOUVEAU : Réinitialiser le score si c'est le début d'un nouveau quiz pour cette catégorie ---
// ou si l'utilisateur arrive sur la page sans un index de question spécifié (nouvelle session de quiz)
if ($current_question_index == 0 && !isset($_POST['submit_answer'])) { // Seulement si ce n'est pas une soumission de réponse
    // Si la catégorie en session change ou si on démarre un nouveau quiz (q=0)
    if (!isset($_SESSION['current_quiz_category_id']) || $_SESSION['current_quiz_category_id'] !== $selected_category_id) {
        $_SESSION['score'] = 0;
        $_SESSION['current_quiz_category_id'] = $selected_category_id; // Stocke la catégorie actuelle
    }
    $user_score = $_SESSION['score']; // Mettre à jour la variable locale avec le score potentiellement réinitialisé
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Dev Web</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <header>
        <div class="container">
            <h1>Quiz Dev Web</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="select_quiz.php">Démarrer un Quiz</a></li> <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="admin.php">Administration</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="quiz-page">
        <div class="container">
            <?php if (isset($_GET['finished']) && $_GET['finished'] == 'true'): ?>
                <div class="quiz-finished">
                    <h2>Quiz terminé !</h2>
                    <p>Votre score final : <span class="score"><?php echo $_SESSION['score']; ?></span> / <?php echo $total_questions; ?></p>
                    <p>Bravo pour votre participation !</p>
                    <a href="quiz.php?category_id=<?php echo $selected_category_id; ?>" class="btn btn-primary">Recommencer le Quiz</a>
                    <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
                </div>
            <?php elseif ($total_questions > 0 && isset($questions[$current_question_index])):
                $question = $questions[$current_question_index];
                ?>
                <div class="quiz-container">
                    <h2>Question <?php echo $current_question_index + 1; ?> / <?php echo $total_questions; ?></h2>
                    <p class="question-category">Catégorie: <?php echo htmlspecialchars($question['category_name']); ?></p>
                    <p class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></p>

                    <form action="quiz.php?category_id=<?php echo $selected_category_id; ?>&q=<?php echo $current_question_index; ?>" method="POST">
                        <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                        <div class="options-list">
                            <?php foreach ($question['options'] as $option): ?>
                                <label class="option-item">
                                    <input type="radio" name="option" value="<?php echo $option['option_id']; ?>" required>
                                    <span><?php echo htmlspecialchars($option['option_text']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" name="submit_answer" class="btn btn-primary">Répondre</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="no-questions">
                    <p>Aucune question disponible pour cette catégorie pour le moment. Veuillez contacter l'administrateur ou choisir une autre catégorie.</p>
                    <a href="select_quiz.php" class="btn btn-secondary">Retour à la sélection des quiz</a>
                </div>
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