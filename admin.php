<?php
// Active l'affichage de toutes les erreurs PHP pour le débogage.
// À retirer ou commenter en production pour des raisons de sécurité.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Le chemin vers db_connect.php doit être correct.
// Si admin.php est dans public/ et db_connect.php est dans includes/,
// le chemin correct est '../includes/db_connect.php'.
require_once 'db_connect.php'; 

// Rediriger si l'utilisateur n'est pas connecté ou n'est pas un administrateur
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php'); // Rediriger si non admin
    exit();
}

$message = '';
$questions = [];
$categories = [];

try {
    // Récupérer toutes les questions existantes
    $stmt = $pdo->query("SELECT q.id, q.question_text, c.name AS category_name
                         FROM questions q
                         JOIN categories c ON q.category_id = c.id
                         ORDER BY q.id DESC");
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les catégories pour les formulaires
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Gérer les actions (Ajouter, Supprimer) ---

    // Ajouter une question
    if (isset($_POST['add_question'])) {
        $question_text = trim($_POST['question_text']);
        $category_id = (int)$_POST['category_id'];
        $options = $_POST['options']; // Tableau d'options
        $correct_option_index = (int)$_POST['correct_option'];

        // S'assurer qu'il y a au moins 2 options (un quiz à choix multiples en a besoin)
        // et que le texte de la question et les options ne sont pas vides
        if (empty($question_text) || count($options) < 2 || !isset($options[$correct_option_index])) {
            $message = 'Veuillez remplir le texte de la question, fournir au moins deux options et sélectionner la bonne réponse.';
        } else {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("INSERT INTO questions (question_text, category_id) VALUES (?, ?)");
                $stmt->execute([$question_text, $category_id]);
                $question_id = $pdo->lastInsertId();

                foreach ($options as $index => $option_text) {
                    // S'assurer que l'option_text n'est pas vide avant l'insertion
                    if (!empty(trim($option_text))) {
                        $is_correct = ($index == $correct_option_index) ? 1 : 0;
                        $stmt = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                        $stmt->execute([$question_id, trim($option_text), $is_correct]);
                    }
                }
                $pdo->commit();
                $message = 'Question ajoutée avec succès !';
                // Utilisation de la redirection pour éviter la resoumission du formulaire
                header('Location: admin.php?msg=' . urlencode($message)); 
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                $message = 'Erreur lors de l\'ajout de la question : ' . $e->getMessage();
            }
        }
    }

    // Supprimer une question
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $question_id = (int)$_GET['id'];
        try {
            // Les options seront supprimées en cascade grâce à la contrainte FOREIGN KEY
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
            $stmt->execute([$question_id]);
            $message = 'Question supprimée avec succès !';
            header('Location: admin.php?msg=' . urlencode($message)); // Recharger la page après suppression
            exit();
        } catch (PDOException $e) {
            $message = 'Erreur lors de la suppression de la question : ' . $e->getMessage();
        }
    }

    // Le code pour l'action 'edit' a été supprimé ici car il est maintenant géré par edit_question.php

    // Récupérer le message après une redirection (ajout ou suppression)
    if (isset($_GET['msg'])) {
        $message = htmlspecialchars($_GET['msg']);
    }

} catch (PDOException $e) {
    // Cette erreur capture les problèmes initiaux de base de données (comme la connexion)
    // Mais l'ini_set en haut est là pour les erreurs de code PHP.
    $message = 'Erreur de base de données principale : ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Quiz Dev Web</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* Styles spécifiques à la page admin ou manquants */
        .admin-page .container {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .admin-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #fcfcfc;
        }
        h3 {
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Inclut le padding dans la largeur totale */
            font-size: 1em;
        }
        .option-input-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .option-input-group input[type="text"] {
            flex-grow: 1;
            margin-right: 10px;
        }
        .option-input-group label {
            margin-bottom: 0;
            display: flex;
            align-items: center;
            white-space: nowrap; /* Empêche le retour à la ligne pour "Correcte" */
        }
        .option-input-group input[type="radio"] {
            margin-right: 5px;
            transform: scale(1.2); /* Agrandit un peu le radio bouton */
        }
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.95em;
            text-decoration: none; /* Pour les liens stylisés en bouton */
            display: inline-block; /* Pour que padding et margin fonctionnent bien */
            margin-right: 8px; /* Espace entre les boutons */
            transition: background-color 0.2s ease;
        }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-success:hover { background-color: #218838; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-danger:hover { background-color: #c82333; }
        .btn-edit { background-color: #ffc107; color: #333; }
        .btn-edit:hover { background-color: #e0a800; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn-delete:hover { background-color: #c82333; }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .admin-table th, .admin-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }
        .admin-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #444;
        }
        .admin-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            border-left: 5px solid;
        }
        .message.success { 
            background-color: #d4edda; 
            color: #155724; 
            border-color: #28a745; 
        }
        .message.error { 
            background-color: #f8d7da; 
            color: #721c24; 
            border-color: #dc3545; 
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Administration Quiz Dev Web</h1>
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

    <main class="admin-page">
        <div class="container">
            <h2>Gestion des Questions et Catégories</h2>
            <?php if (!empty($message)): ?>
                <p class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>"><?php echo $message; ?></p>
            <?php endif; ?>

            <div class="admin-section">
                <h3>Ajouter une nouvelle question</h3>
                <form action="admin.php" method="POST" class="add-question-form">
                    <div class="form-group">
                        <label for="question_text">Question:</label>
                        <textarea id="question_text" name="question_text" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Catégorie:</label>
                        <select id="category_id" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="options-container">
                        <h4>Options de réponse:</h4>
                        <div class="option-input-group">
                            <input type="text" name="options[]" placeholder="Option 1" required>
                            <label><input type="radio" name="correct_option" value="0" required> Correcte</label>
                        </div>
                        <div class="option-input-group">
                            <input type="text" name="options[]" placeholder="Option 2" required>
                            <label><input type="radio" name="correct_option" value="1" required> Correcte</label>
                        </div>
                        <div class="option-input-group">
                            <input type="text" name="options[]" placeholder="Option 3" required>
                            <label><input type="radio" name="correct_option" value="2" required> Correcte</label>
                        </div>
                        <div class="option-input-group">
                            <input type="text" name="options[]" placeholder="Option 4" required>
                            <label><input type="radio" name="correct_option" value="3" required> Correcte</label>
                        </div>
                    </div>
                    <button type="submit" name="add_question" class="btn btn-success">Ajouter la question</button>
                </form>
            </div>

            <div class="admin-section">
                <h3>Liste des questions existantes</h3>
                <?php if (empty($questions)): ?>
                    <p>Aucune question n'a été ajoutée pour le moment.</p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Catégorie</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $q): ?>
                                <tr>
                                    <td><?php echo $q['id']; ?></td>
                                    <td><?php echo htmlspecialchars(substr($q['question_text'], 0, 100)); ?>...</td>
                                    <td><?php echo htmlspecialchars($q['category_name']); ?></td>
                                    <td>
                                        <a href="edit_question.php?id=<?php echo $q['id']; ?>" class="btn btn-edit">Modifier</a>
                                        <a href="admin.php?action=delete&id=<?php echo $q['id']; ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Quiz Dev Web. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>