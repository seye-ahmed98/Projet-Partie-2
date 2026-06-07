<?php
// admin/projets_liste.php
session_start();

if (empty($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header("Location: connexion.php");
    exit;
}

require_once '../config/connexion.php';
require_once '../fonctions.php';

// Récupération de tous les projets
$stmt = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC");
$projets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Projets | Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: white; padding: 30px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s, transform 0.1s; }
        .btn-back { background: #334155; color: white; }
        .btn-add { background: #10b981; color: white; }
        .btn-add:hover { background: #059669; }
        
        /* Styles pour les boutons d'action de la table */
        .btn-edit { background: #3b82f6; color: white; padding: 6px 12px; }
        .btn-edit:hover { background: #2563eb; }
        .btn-delete { background: #ef4444; color: white; padding: 6px 12px; }
        .btn-delete:hover { background: #dc2626; }
        .actions-cell { display: flex; gap: 8px; justify-content: flex-start; align-items: center; }
        
        table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 8px; overflow: hidden; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #1e293b; color: #94a3b8; font-weight: 600; }
        
        .projet-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; background: #0f172a; border: 1px solid #334155; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; background: #3b82f6; color: #fff; }
        
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; background: #10b981; color: white; }
        .desc-cell { max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #cbd5e1; }
    </style>
</head>
<body>

<div class="header-actions">
    <a href="dashboard.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Retour au Dashboard</a>
    <h1>Liste de vos <span style="color: #3b82f6;">Réalisations</span></h1>
    <a href="ajouter_projet.php" class="btn btn-add"><i class="fas fa-plus"></i> Ajouter un projet</a>
</div>

<!-- Messages de notification de statut -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert">Le projet a été supprimé avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['statut']) && $_GET['statut'] === 'modifie'): ?>
    <div class="alert" style="background: #0ea5e9;">Le projet a été mis à jour avec succès !</div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Aperçu</th>
            <th>Titre du projet</th>
            <th>Catégorie</th>
            <th>Description</th>
            <th>Date d'ajout</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($projets)): ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px;">
                    <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                    Aucun projet enregistré en base de données.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($projets as $p): ?>
                <tr>
                    <td>
                        <img src="../images/projets/<?= nettoyer($p['image']) ?>" alt="" class="projet-thumb">
                    </td>
                    <td><strong><?= nettoyer($p['titre']) ?></strong></td>
                    <td><span class="badge"><?= nettoyer($p['categorie']) ?></span></td>
                    <td class="desc-cell" title="<?= nettoyer($p['description']) ?>"><?= nettoyer($p['description']) ?></td>
                    <td style="color: #94a3b8; font-size: 0.9rem;"><?= date('d/m/Y', strtotime($p['date_creation'])) ?></td>
                    <td>
                        <div class="actions-cell">
                            <!-- BOUTON MODIFIER -->
                            <a href="modifier_projet.php?id=<?= $p['id'] ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            
                            <!-- BOUTON SUPPRIMER -->
                            <a href="projets_supprimer.php?id=<?= $p['id'] ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réalisation ?');">
                                <i class="fas fa-trash-alt"></i> Supprimer
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>