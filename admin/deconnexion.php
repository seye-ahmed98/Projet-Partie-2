<?php

//admin/déconnexion.php
session_start();

// On vide le tableau de session
$_SESSION = array();

// On détruit les cookies de session sur le navigateur
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// On détruit la session côté serveur
session_destroy();

// Redirection vers la page de connexion
header("Location: connexion.php");
exit;