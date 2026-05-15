<?php
// On récupère le nom du fichier actuel
$page_courante = basename($_SERVER['PHP_SELF']);

/**
 * CONFIGURATION DES CHEMINS :
 * Puisque tous les fichiers principaux sont à la racine, le préfixe reste vide.
 */
$prefix = ''; 

// Titre par défaut si non défini en amont
$titre = isset($titre_page) ? htmlspecialchars($titre_page) : "Ahmed SEYE | Portfolio";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titre ?></title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <input type="checkbox" id="mode-toggle" class="mode-switch">

    <div class="main-wrapper">
        
        <nav class="navbar">
            <div class="nav-logo">
                <a href="index.php#accueil">@-$EYE</a>
            </div>
            
            <div class="nav-right">
                <ul class="nav-links">
                    <li><a href="index.php#accueil">Accueil</a></li>
                    <li><a href="index.php#apropos">À propos</a></li>
                    <li><a href="index.php#parcours">Parcours</a></li>
                    <li><a href="index.php#competences">Compétences</a></li>
                    <li><a href="projets.php">Projets</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                
                <label for="mode-toggle" class="theme-toggle" aria-label="Changer de thème">
                    <i class="fa-solid fa-sun sun-icon"></i>
                    <i class="fa-solid fa-moon moon-icon"></i>
                </label>
            </div>
        </nav>