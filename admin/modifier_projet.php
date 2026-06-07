<?php 
/**
 * MODIFIER_PROJET.PHP - Administration Portfolio Ahmed SEYE
 * Modification d'une réalisation existante avec design intégré et redirection vers projets_liste.php
 */

// 1. DÉMARRAGE DE LA SESSION
session_start();

if (empty($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header("Location: connexion.php");
    exit;
}

// 2. INCLUSIONS OBLIGATOIRES
require_once '../config/connexion.php';
require_once '../fonctions.php';

// Génération du jeton CSRF requis pour sécuriser le formulaire
$csrf_token = generer_jeton_csrf();

// 3. VÉRIFICATION ET RÉCUPÉRATION DE LA RÉALISATION À MODIFIER
$id_projet = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_projet) {
    header('Location: projets_liste.php'); 
    exit();
}

// Chargement initial des données depuis la table 'projets'
try {
    $stmt = $pdo->prepare('SELECT * FROM projets WHERE id = ?');
    $stmt->execute([$id_projet]);
    $projet = $stmt->fetch();

    if (!$projet) {
        header('Location: projets_liste.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Erreur récupération projet : " . $e->getMessage());
    die("Une erreur technique est survenue.");
}

// Liaison avec vos vrais champs imposés de la base de données
$titre_projet   = $projet['titre'];
$categorie      = $projet['categorie']; 
$description    = $projet['description'];
$technologies   = $projet['technologies']; // Requis - Section 2.1
$lien           = $projet['lien'] ?? '';   // Requis - Section 2.1
$image_actuelle = $projet['image'] ?? ""; 

$erreur_general = "";

// 4. TRAITEMENT DU FORMULAIRE (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_modifier_projet'])) {
        
        // Sécurité obligatoire : vérification du jeton CSRF de session (Section 3.2)
        verifier_csrf();
        
        $titre_projet = trim($_POST['projet_titre'] ?? "");
        $categorie    = trim($_POST['projet_categorie'] ?? "");
        $description  = trim($_POST['projet_description'] ?? "");
        $technologies = trim($_POST['projet_technologies'] ?? ""); // Récupération technologies
        $lien         = trim($_POST['projet_lien'] ?? "");         // Récupération lien externe
        
        $nom_image_final = $image_actuelle; 
        
        if (!empty($titre_projet) && !empty($categorie) && !empty($description) && !empty($technologies)) {
            
            // --- TRAITEMENT DE LA NOUVELLE IMAGE ---
            if (isset($_FILES['projet_image']) && $_FILES['projet_image']['error'] === UPLOAD_ERR_OK) {
                
                $file_tmp   = $_FILES['projet_image']['tmp_name'];
                $file_name  = $_FILES['projet_image']['name'];
                $file_size  = $_FILES['projet_image']['size'];
                $upload_dir = '../images/projets/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Ajout de l'extension 'gif' exigée à la section 5.3
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                if (!in_array($file_ext, $allowed_extensions)) {
                    $erreur_general = "Extension non autorisée (JPG, JPEG, PNG, WEBP, GIF uniquement).";
                } elseif ($file_size > 5 * 1024 * 1024) {
                    $erreur_general = "L'image ne doit pas dépasser 5 Mo.";
                } else {
                    $new_file_name = uniqid('proj_', true) . '.' . $file_ext;
                    $dest_path = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($file_tmp, $dest_path)) {
                        $nom_image_final = $new_file_name;
                        
                        // Suppression physique de la photo précédente pour éviter d'encombrer le serveur
                        if (!empty($image_actuelle) && $image_actuelle !== 'default.jpg' && file_exists($upload_dir . $image_actuelle)) {
                            unlink($upload_dir . $image_actuelle);
                        }
                    } else {
                        $erreur_general = "Erreur lors du transfert de l'image.";
                    }
                }
            }
            
            // Enregistrement si aucune erreur n'a été levée
            if (empty($erreur_general)) {
                try {
                    // Mise à jour complète incluant 'technologies' et 'lien'
                    $stmt = $pdo->prepare('
                        UPDATE projets 
                        SET titre = ?, categorie = ?, description = ?, technologies = ?, image = ?, lien = ?
                        WHERE id = ?
                    ');
                    $stmt->execute([$titre_projet, $categorie, $description, $technologies, $nom_image_final, $lien, $id_projet]);
                    
                    header('Location: projets_liste.php?statut=modifie');
                    exit();
                    
                } catch (PDOException $e) {
                    error_log("Erreur SQL : " . $e->getMessage());
                    $erreur_general = "Erreur lors de la mise à jour en base de données.";
                }
            }
        } else {
            $erreur_general = "Veuillez remplir tous les champs obligatoires (*).";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Projet | Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: white; padding: 30px; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 90vh; margin: 0; }
        .container { width: 100%; max-width: 600px; }
        .header-actions { margin-bottom: 25px; width: 100%; display: flex; justify-content: flex-start; }
        .btn { padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-back { background: #334155; color: white; }
        .btn-back:hover { background: #475569; }
        .btn-submit { background: #3b82f6; color: white; padding: 12px; font-size: 0.95rem; justify-content: center; }
        .btn-submit:hover { background: #2563eb; }
        
        .card { background: #1e293b; padding: 30px; border-radius: 12px; border: 1px solid #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.3); width: 100%; box-sizing: border-box; }
        h1 { font-size: 1.6rem; margin-top: 0; margin-bottom: 25px; color: white; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
        label { color: #94a3b8; font-size: 0.85rem; font-weight: 600; }
        input[type="text"], input[type="url"], select, textarea { width: 100%; padding: 11px; background: #0f172a; border: 1px solid #334155; color: white; border-radius: 6px; box-sizing: border-box; font-family: inherit; font-size: 0.9rem; }
        input:focus, select:focus, textarea:focus { border-color: #3b82f6; outline: none; }
        
        .image-manager { background: rgba(15, 23, 42, 0.6); padding: 15px; border-radius: 6px; border: 1px dashed #475569; margin-bottom: 18px; }
        .thumb-preview { width: 70px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #334155; }
        .alert-danger { background: #ef4444; color: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; font-size: 0.9rem; text-align: center; }
    </style>
</head>
<body>

<div class="container">

    <div class="header-actions">
        <a href="projets_liste.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Annuler et retourner</a>
    </div>

    <?php if ($erreur_general): ?>
        <div class="alert-danger"><?= nettoyer($erreur_general) ?></div>
    <?php endif; ?>

    <div class="card">
        <h1>Modifier la réalisation <span style="color: #3b82f6;">#<?= $id_projet ?></span></h1>
        
        <form action="modifier_projet.php?id=<?= $id_projet ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="form-group">
                <label>Titre du projet *</label>
                <input type="text" name="projet_titre" value="<?= nettoyer($titre_projet) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Catégorie *</label>
                <select name="projet_categorie" required>
                    <option value="Architecture 3D" <?= $categorie == 'Architecture 3D' ? 'selected' : '' ?>>Architecture 3D / AutoCAD</option>
                    <option value="Développement Web" <?= $categorie == 'Développement Web' ? 'selected' : '' ?>>Développement Web (PHP/Java)</option>
                    <option value="Réseau & Sécurité" <?= $categorie == 'Réseau & Sécurité' ? 'selected' : '' ?>>Réseau & Sécurité (pfSense)</option>
                    <option value="IoT" <?= $categorie == 'IoT' ? 'selected' : '' ?>>IoT / Arduino</option>
                </select>
            </div>

            <div class="form-group">
                <label>Technologies utilisées *</label>
                <input type="text" name="projet_technologies" value="<?= nettoyer($technologies) ?>" required placeholder="Ex: AutoCAD, SketchUp ou PHP, MySQL, CSS">
            </div>

            <div class="image-manager">
                <label style="display:block; margin-bottom: 8px;">Image d'illustration actuelle</label>
                <?php if (!empty($image_actuelle) && file_exists('../images/projets/' . $image_actuelle)): ?>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <img src="../images/projets/<?= $image_actuelle ?>" alt="Aperçu" class="thumb-preview">
                        <span style="font-size: 0.8rem; color: #94a3b8;">Fichier actuel : <code><?= nettoyer($image_actuelle) ?></code></span>
                    </div>
                <?php endif; ?>
                <input type="file" name="projet_image" accept="image/png, image/jpeg, image/jpg, image/webp, image/gif">
                <small style="color: #64748b; font-size: 0.8rem; display: block; margin-top: 4px;">Laissez vide pour conserver l'image existante.</small>
            </div>

            <div class="form-group">
                <label>Lien externe (GitHub ou Démo)</label>
                <input type="url" name="projet_lien" value="<?= nettoyer($lien) ?>" placeholder="https://...">
            </div>

            <div class="form-group">
                <label>Description du projet *</label>
                <textarea name="projet_description" rows="5" required><?= nettoyer($description) ?></textarea>
            </div>
            
            <button type="submit" name="submit_modifier_projet" class="btn btn-submit" style="width: 100%; margin-top: 10px;">
                <i class="fas fa-save"></i> Mettre à jour le projet
            </button>
        </form>
    </div>
    
</div>

</body>
</html>