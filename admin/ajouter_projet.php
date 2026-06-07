<?php
// admin/ajouter_projet.php
session_start();
require_once '../config/connexion.php';
require_once '../fonctions.php';

if (empty($_SESSION['admin_connecte'])) { 
    header("Location: connexion.php"); 
    exit; 
}

$csrf_token = generer_jeton_csrf();
$erreur = '';
$succes = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifier_csrf();

    $titre = trim($_POST['titre']);
    $categorie = $_POST['categorie'];
    $description = trim($_POST['description']);
    $technologies = trim($_POST['technologies']); // Récupération du champ obligatoire
    $lien = trim($_POST['lien_github_ou_demo']);

    // Gestion de l'upload d'image
    $image_nom = "default.jpg";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        // Ajout du format 'gif' exigé par la section 5.3 du cahier des charges
        $extensions_valides = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($extension, $extensions_valides)) {
            $image_nom = time() . '_' . uniqid() . '.' . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], '../images/projets/' . $image_nom);
        } else {
            $erreur = "Format d'image non supporté (Formats acceptés : JPG, JPEG, PNG, WEBP, GIF).";
        }
    }

    if (empty($erreur)) {
        try {
            // Insertion incluant la colonne obligatoire 'technologies'
            $stmt = $pdo->prepare("INSERT INTO projets (titre, categorie, description, technologies, image, lien, date_creation) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$titre, $categorie, $description, $technologies, $image_nom, $lien]);
            
            // Redirection directe vers la liste avec un flag de succès personnalisé
            header('Location: projets_liste.php?statut=ajoute');
            exit();
        } catch (PDOException $e) {
            $erreur = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Projet | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: white; padding: 40px; }
        .form-container { max-width: 800px; margin: 0 auto; background: #1e293b; padding: 30px; border-radius: 12px; }
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
        input, select, textarea { padding: 12px; background: #0f172a; border: 1px solid #334155; color: white; border-radius: 6px; font-family: inherit; font-size: 0.95rem; }
        input:focus, select:focus, textarea:focus { border-color: #10b981; outline: none; }
        .btn-submit { background: #10b981; color: white; border: none; padding: 15px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.2s; }
        .btn-submit:hover { background: #059669; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; }
        .alert-danger { background: #ef4444; color: white; }
    </style>
</head>
<body>

<div class="form-container">
    <a href="projets_liste.php" style="color: #94a3b8; text-decoration: none;"><i class="fas fa-arrow-left"></i> Annuler et retourner</a>
    <h1 style="margin: 20px 0;">Nouveau Projet</h1>

    <?php if ($erreur): ?><div class="alert alert-danger"><?= $erreur ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        
        <div class="form-group">
            <label>Titre du projet *</label>
            <input type="text" name="titre" required placeholder="Ex: Villa Médina Fall 3D">
        </div>

        <div class="form-group">
            <label>Catégorie *</label>
            <select name="categorie" required>
                <option value="Architecture 3D">Architecture 3D / AutoCAD</option>
                <option value="Développement Web">Développement Web (PHP/Java)</option>
                <option value="Réseau & Sécurité">Réseau & Sécurité (pfSense)</option>
                <option value="IoT">IoT / Arduino</option>
            </select>
        </div>

        <div class="form-group">
            <label>Description détaillée *</label>
            <textarea name="description" rows="5" required placeholder="Saisissez le résumé ou descriptif du projet..."></textarea>
        </div>

        <div class="form-group">
            <label>Technologies utilisées *</label>
            <input type="text" name="technologies" required placeholder="Ex: AutoCAD, SketchUp ou PHP, MySQL, CSS">
        </div>

        <div class="form-group">
            <label>Image de couverture</label>
            <input type="file" name="image" accept="image/png, image/jpeg, image/jpg, image/webp, image/gif">
        </div>

        <div class="form-group">
            <label>Lien (GitHub ou Démo)</label>
            <input type="url" name="lien_github_ou_demo" placeholder="https://...">
        </div>

        <button type="submit" class="btn-submit">Enregistrer le projet</button>
    </form>
</div>

</body>
</html>