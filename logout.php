<?php
session_start(); // Démarre la session

// Détruit toutes les variables de session.
$_SESSION = array();

// Si vous voulez détruire complètement la session, effacez également le cookie de session.
// Note : Cela détruira la session, et pas seulement les données de session !
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruit la session.
session_destroy();

// Redirige l'utilisateur vers la page de connexion ou la page d'accueil.
// Vous pouvez choisir 'login.php' ou 'index.php' selon ce que vous préférez.
header("Location: login.php"); // Redirection vers la page de connexion
exit(); // Assure que le script s'arrête ici et que la redirection est effectuée
?>