<?php
// Inclusion des fonctions pour utiliser nettoyer() et champ_requis()
require_once 'fonctions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- CAS A : Formulaire "Présenter votre projet" ---
    if (isset($_POST['projet_nom'])) {
        // Utilisation de ta fonction personnalisée
        $projet_nom = nettoyer($_POST['projet_nom']);
        $budget     = nettoyer($_POST['budget']);
        $service    = nettoyer($_POST['service']);
        $besoin     = nettoyer($_POST['besoin']);

        // Validation utilisant ta fonction
        if (!champ_requis($projet_nom) || !champ_requis($besoin) || !champ_requis($service)) {
            header("Location: contact.php?statut=erreur");
            exit;
        }

        header("Location: contact.php?statut=success_projet");
        exit;
    }

    // --- CAS B : Formulaire "Me Contacter" ---
    if (isset($_POST['nom'])) {
        $nom     = nettoyer($_POST['nom']);
        $email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $message = nettoyer($_POST['message']);

        // Validation stricte
        if (!champ_requis($nom) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !champ_requis($message)) {
            header("Location: contact.php?statut=erreur");
            exit;
        }

        header("Location: contact.php?statut=success_contact");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}