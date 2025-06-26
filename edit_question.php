<?php
// Afficher toutes les erreurs PHP pour le débogage (à retirer en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_connect.php'; // Chemin vers votre fichier de connexion à la BDD

// Vérifier si l'utilisateur est admin, sinon rediriger
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}

$message = '';
$question = null;
$options = [];
$categories = [];

// Récupérer l'ID de la question depuis l'adresse (URL)
$question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Si pas d'ID, retourner à la page admin avec un message
if ($question_id === 0) {
    header('Location: admin.php?msg=' . urlencode('Aucun ID de question trouvé pour la modification.'));
    exit();
}

try {
    // Charger la question et ses options depuis la base de données
    $stmt = $pdo->prepare("SELECT id, question_text, category_id FROM questions WHERE id = ?");
    $stmt->execute([$question_id]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$question) {
        header('Location: admin.php?msg=' . urlencode('Cette question n\'existe pas.'));
        exit();
    }

    $stmt = $pdo->prepare("SELECT id, option_text, is_correct FROM options WHERE question_id = ? ORDER BY id ASC");
    $stmt->execute([$question_id]);
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Charger toutes les catégories pour la liste déroulante
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Gérer l'envoi du formulaire de modification
    if (isset($_POST['update_question'])) {
        $new_question_text = trim($_POST['question_text']);
        $new_category_id = (int)$_POST['category_id'];
        $new_options_texts = $_POST['options_text'];
        $new_correct_option_index = (int)$_POST['correct_option'];

        if (empty($new_question_text) || count($new_options_texts) < 2) {
            $message = 'Veuillez remplir la question et au moins deux options.';
        } else {
            $pdo->beginTransaction(); // Commencer une transaction (pour que tout soit fait ou rien)
            try {
                // 1. Mettre à jour le texte de la question et sa catégorie
                $stmt = $pdo->prepare("UPDATE questions SET question_text = ?, category_id = ? WHERE id = ?");
                $stmt->execute([$new_question_text, $new_category_id, $question_id]);

                // 2. Supprimer les anciennes options de la question
                $stmt = $pdo->prepare("DELETE FROM options WHERE question_id = ?");
                $stmt->execute([$question_id]);

                // 3. Insérer les nouvelles options
                foreach ($new_options_texts as $index => $option_text) {
                    if (!empty(trim($option_text))) {
                        $is_correct = ($index == $new_correct_option_index) ? 1 : 0;
                        $stmt = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                        $stmt->execute([$question_id, trim($option_text), $is_correct]);
                    }
                }
                $pdo->commit(); // Confirmer toutes les modifications
                $message = 'Question mise à jour avec succès !';
                header('Location: admin.php?msg=' . urlencode($message)); // Retourner à admin.php
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack(); // Annuler si erreur
                $message = 'Erreur lors de la mise à jour : ' . $e->getMessage();
                // Recharger les données pour que le formulaire reste rempli en cas d'erreur
                $stmt = $pdo->prepare("SELECT id, question_text, category_id FROM questions WHERE id = ?");
                $stmt->execute([$question_id]);
                $question = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt = $pdo->prepare("SELECT id, option_text, is_correct FROM options WHERE question_id = ? ORDER BY id ASC");
                $stmt->execute([$question_id]);
                $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    }

} catch (PDOException $e) {
    $message = 'Erreur de base de données : ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">|
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Question - Quiz Dev Web</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles de base pour la page d'édition. Idéalement dans style.css */
        .edit-page .container { padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"], .form-group textarea, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .option-input-group { display: flex; align-items: center; margin-bottom: 10px; }
        .option-input-group input[type="text"] { flex-grow: 1; margin-right: 10px; }
        .option-input-group label { margin-bottom: 0; display: flex; align-items: center; }
        .option-input-group input[type="radio"] { margin-right: 5px; }
        .btn { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; text-decoration: none; display: inline-block; margin-right: 5px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-cancel { background-color: #6c757d; color: white; }

        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; }
        .message.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Modifier une Question</h1>
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

    <main class="edit-page">
        <div class="container">
            <h2>Modifier la question (ID: <?php echo htmlspecialchars($question['id']); ?>)</h2>
            <?php if (!empty($message)): ?>
                <p class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>"><?php echo $message; ?></p>
            <?php endif; ?>

            <form action="edit_question.php?id=<?php echo htmlspecialchars($question_id); ?>" method="POST" class="edit-question-form">
                <div class="form-group">
                    <label for="question_text">Question:</label>
                    <textarea id="question_text" name="question_text" rows="3" required><?php echo htmlspecialchars($question['question_text'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="category_id">Catégorie:</label>
                    <select id="category_id" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($question['category_id']) && $question['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="options-container">
                    <h4>Options de réponse:</h4>
                    <?php
                    $correct_option_index = -1; // Pour retrouver l'index de la bonne réponse actuelle
                    foreach ($options as $index => $option) {
                        if ($option['is_correct']) {
                            $correct_option_index = $index;
                        }
                        ?>
                        <div class="option-input-group">
                            <input type="text" name="options_text[]" placeholder="Option <?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($option['option_text']); ?>" required>
                            <label><input type="radio" name="correct_option" value="<?php echo $index; ?>" <?php echo $option['is_correct'] ? 'checked' : ''; ?> required> Correcte</label>
                        </div>
                        <?php
                    }
                    // Ajout de champs vides si moins de 4 options existent
                    for ($i = count($options); $i < 4; $i++) {
                        ?>
                        <div class="option-input-group">
                            <input type="text" name="options_text[]" placeholder="Option <?php echo $i + 1; ?>" required>
                            <label><input type="radio" name="correct_option" value="<?php echo $i; ?>" required> Correcte</label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <button type="submit" name="update_question" class="btn btn-primary">Mettre à jour la question</button>
                <a href="admin.php" class="btn btn-cancel">Annuler</a>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Quiz Dev Web. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>