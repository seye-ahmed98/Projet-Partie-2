<?php
// admin/projets_supprimer.php
session_start();

if (empty($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header("Location: connexion.php");
    exit;
}

require_once '../config/connexion.php';
require_once '../fonctions.php';

$csrf_token = generer_jeton_csrf();
$message_erreur = "";

// Récupération sécurisée de l'ID passé en GET lors du clic initial
$id_projet = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Traitement de la suppression si le formulaire intermédiaire est validé (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirmer_suppression') {
    verifier_csrf();
    $id_projet_post = (int)$_POST['id'];

    if ($id_projet_post > 0) {
        try {
            // Chercher l'image associée pour nettoyage du disque dur
            $stmt_img = $pdo->prepare("SELECT image FROM projets WHERE id = ?");
            $stmt_img->execute([$id_projet_post]);
            $projet = $stmt_img->fetch();

            if ($projet) {
                // Supprimer la ligne de la base de données
                $stmt_del = $pdo->prepare("DELETE FROM projets WHERE id = ?");
                $stmt_del->execute([$id_projet_post]);

                // Nettoyage : Suppression du fichier image physique du serveur (évite d'encombrer XAMPP)
                if (!empty($projet['image']) && $projet['image'] !== 'default.jpg') {
                    $chemin_image = '../images/projets/' . $projet['image'];
                    if (file_exists($chemin_image)) {
                        unlink($chemin_image);
                    }
                }
                
                // Redirection vers la liste globale avec notification de succès
                header("Location: projets_liste.php?statut=supprime");
                exit;
            }
        } catch (PDOException $e) {
            // Journalisation interne conforme à la section 3.1
            error_log("Erreur SQL lors de la suppression : " . $e->getMessage());
            $message_erreur = "Une erreur technique est survenue lors de la suppression du projet.";
        }
    }
}

// Récupération dynamique du titre du projet pour l'affichage de confirmation
$stmt = $pdo->prepare("SELECT titre FROM projets WHERE id = ?");
$stmt->execute([$id_projet]);
$projet_a_supprimer = $stmt->fetch();

// Sécurité : Si l'ID n'existe pas ou a déjà été supprimé, retour direct
if (!$projet_a_supprimer) {
    header("Location: projets_liste.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmer la suppression | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box-confirmation { background: #1e293b; padding: 40px; border-radius: 12px; max-width: 500px; text-align: center; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3); border: 1px solid #ef4444; box-sizing: border-box; }
        .icon-warn { color: #ef4444; font-size: 3.5rem; margin-bottom: 20px; }
        h2 { margin-bottom: 15px; font-size: 1.5rem; }
        p { color: #94a3b8; line-height: 1.6; margin-bottom: 30px; }
        .actions-form { display: flex; justify-content: center; gap: 15px; align-items: center; }
        .btn { padding: 12px 24px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; border: none; font-size: 0.9rem; display: inline-flex; align-items: center; justify-content: center; transition: background 0.2s; }
        .btn-cancel { background: #334155; color: white; }
        .btn-cancel:hover { background: #475569; }
        .btn-confirm { background: #ef4444; color: white; }
        .btn-confirm:hover { background: #dc2626; }
        .alert-danger { background: #ef4444; color: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="box-confirmation">
    <i class="fas fa-exclamation-triangle icon-warn"></i>
    <h2>Confirmation de suppression</h2>
    
    <?php if ($message_erreur): ?>
        <div class="alert-danger"><?= nettoyer($message_erreur) ?></div>
    <?php endif; ?>

    <p>Êtes-vous sûr de vouloir supprimer définitivement la réalisation <br><strong style="color: white;">"<?= nettoyer($projet_a_supprimer['titre']) ?>"</strong> ?<br>Cette action videra la base de données et détruira le fichier image associé.</p>

    <div class="actions-form">
        <a href="projets_liste.php" class="btn btn-cancel">Annuler</a>

        <form action="projets_supprimer.php?id=<?= $id_projet ?>" method="POST" style="margin: 0;">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="action" value="confirmer_suppression">
            <input type="hidden" name="id" value="<?= $id_projet ?>">
            <button type="submit" class="btn btn-confirm">Oui, supprimer</button>
        </form>
    </div>
</div>

</body>
</html>