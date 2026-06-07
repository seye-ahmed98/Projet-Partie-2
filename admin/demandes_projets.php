<?php
// admin/demandes_projets.php
session_start();

// 1. Sécurité : Vérification de la session admin
if (empty($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header("Location: connexion.php");
    exit;
}

require_once '../config/connexion.php';
require_once '../fonctions.php';

// Génération du jeton CSRF pour sécuriser l'action de traitement
$csrf_token = generer_jeton_csrf();

$message_succes = "";
$message_erreur = "";

// 2. LOGIQUE DE MISE À JOUR : Marquer comme traité (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'marquer_traite') {
    // Vérification stricte du jeton CSRF
    verifier_csrf();

    $id_demande = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id_demande > 0) {
        try {
            // Requête préparée pour passer la colonne 'lu' à 1 (indiquant que c'est traité)
            $stmt = $pdo->prepare("UPDATE demandes_projet SET lu = 1 WHERE id = ?");
            $stmt->execute([$id_demande]);
            $message_succes = "La demande de projet a été marquée comme traitée.";
        } catch (PDOException $e) {
            error_log("Erreur mise à jour demandes_projet : " . $e->getMessage());
            $message_erreur = "Impossible de modifier le statut de la demande.";
        }
    }
}

// 3. RÉCUPÉRATION DE TOUTES LES DEMANDES DE PROJET
// Les plus récentes s'affichent en premier
try {
    $stmt = $pdo->query("SELECT * FROM demandes_projet ORDER BY date_demande DESC");
    $demandes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur récupération demandes_projet : " . $e->getMessage());
    die("Une erreur technique est survenue.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes Projets | Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: white; padding: 30px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { padding: 8px 12px; border-radius: 5px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back { background: #334155; color: white; }
        .btn-back:hover { background: #475569; }
        .btn-action { background: #10b981; color: white; }
        .btn-action:hover { background: #059669; }
        .btn-reply { background: #3b82f6; color: white; }
        .btn-reply:hover { background: #2563eb; }
        
        table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 8px; overflow: hidden; margin-top: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        th, td { padding: 14px 15px; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #1e293b; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
        tr:hover td { background: rgba(255, 255, 255, 0.01); }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; display: inline-block; }
        .badge-en-attente { background: #3b82f6; color: #fff; }
        .badge-traite { background: #10b981; color: #fff; }
        .budget-badge { color: #f59e0b; font-weight: bold; font-family: monospace; }
        
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #10b981; color: white; }
        .alert-danger { background: #ef4444; color: white; }
        .description-texte { color: #cbd5e1; max-width: 350px; word-wrap: break-word; font-size: 0.9rem; }
        
        .actions-container { display: inline-flex; gap: 10px; align-items: center; }
    </style>
</head>
<body>

<div class="header-actions">
    <a href="dashboard.php" class="btn btn-back">← Retour au Dashboard</a>
    <h1>Gestion des Demandes de <span style="color: #3b82f6;">Projets</span></h1>
    <div></div>
</div>

<?php if ($message_succes): ?>
    <div class="alert alert-success"><?= $message_succes ?></div>
<?php endif; ?>

<?php if ($message_erreur): ?>
    <div class="alert alert-danger"><?= $message_erreur ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Statut</th>
            <th>Client / Contact</th>
            <th>Type de Projet</th>
            <th>Budget Estimé</th>
            <th>Description du Besoin</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($demandes)): ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #94a3b8; padding: 25px;">Aucune demande de projet reçue pour le moment.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($demandes as $d): ?>
                <tr style="<?= $d['lu'] == 0 ? 'background: rgba(59, 130, 246, 0.02);' : '' ?>">
                    <td>
                        <?php if ($d['lu'] == 0): ?>
                            <span class="badge badge-en-attente">En attente</span>
                        <?php else: ?>
                            <span class="badge badge-traite">Traité</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= nettoyer($d['nom']) ?></strong>
                        <br><span style="font-size: 0.8rem; color: #64748b;"><?= nettoyer($d['email']) ?></span>
                    </td>
                    <td>
                        <span style="color: #3b82f6; font-weight: 600;"><i class="fas fa-folder-open" style="font-size: 0.85rem; margin-right: 5px;"></i><?= nettoyer($d['type_projet']) ?></span>
                    </td>
                    <td>
                        <span class="budget-badge"><?= $d['budget'] ? nettoyer($d['budget']) . ' FCFA' : 'Non spécifié'; ?></span>
                    </td>
                    <td class="description-texte">
                        <?= nl2br(nettoyer($d['description'] ?? '')); ?>
                    </td>
                    <td style="color: #94a3b8; font-size: 0.85rem;"><?= date('d/m/Y à H:i', strtotime($d['date_demande'])) ?></td>
                    <td>
                        <div class="actions-container">
                            <a href="mailto:<?= nettoyer($d['email']) ?>?subject=Suite à votre demande de projet : <?= urlencode($d['type_projet']) ?>" 
                               class="btn btn-reply" 
                               title="Contacter le client par email">
                                <i class="fas fa-paper-plane"></i> Contacter
                            </a>

                            <?php if ($d['lu'] == 0): ?>
                                <form action="demandes_projets.php" method="POST" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="action" value="marquer_traite">
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button type="submit" class="btn btn-action" onclick="return confirm('Confirmer que cette demande a été prise en compte ?');">
                                        <i class="fas fa-check"></i> Traité
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: #64748b; font-size: 0.85rem; font-style: italic;"><i class="fas fa-check-double" style="color: #10b981; margin-right: 4px;"></i>Archivé</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>