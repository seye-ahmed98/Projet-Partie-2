<?php
// admin/demandes_contact.php
session_start();

// 1. Sécurité : Vérification de la session admin
if (empty($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header("Location: connexion.php");
    exit;
}

require_once '../config/connexion.php';
require_once '../fonctions.php';

// Génération du jeton CSRF pour sécuriser l'action de mise à jour
$csrf_token = generer_jeton_csrf();

$message_succes = "";
$message_erreur = "";

// 2. LOGIQUE DE MISE À JOUR : Marquer comme lu (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'marquer_lu') {
    // Vérification stricte du jeton CSRF
    verifier_csrf();

    $id_message = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id_message > 0) {
        try {
            // Requête préparée pour passer le statut 'lu' à 1
            $stmt = $pdo->prepare("UPDATE messages_contact SET lu = 1 WHERE id = ?");
            $stmt->execute([$id_message]);
            $message_succes = "Le message a été marqué comme lu.";
        } catch (PDOException $e) {
            error_log("Erreur mise à jour messages_contact : " . $e->getMessage());
            $message_erreur = "Impossible de modifier le statut du message.";
        }
    }
}

// 3. RÉCUPÉRATION DE TOUS LES MESSAGES DE CONTACT
$stmt = $pdo->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC");
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Messages | Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: white; padding: 30px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { padding: 8px 12px; border-radius: 5px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back { background: #334155; color: white; }
        .btn-back:hover { background: #475569; }
        .btn-action { background: #007bff; color: white; }
        .btn-action:hover { background: #0056b3; }
        .btn-reply { background: #3b82f6; color: white; }
        .btn-reply:hover { background: #2563eb; }
        
        table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 8px; overflow: hidden; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #1e293b; color: #94a3b8; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; display: inline-block; }
        .badge-non-lu { background: #f59e0b; color: #fff; }
        .badge-lu { background: #10b981; color: #fff; }
        
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #10b981; color: white; }
        .alert-danger { background: #ef4444; color: white; }
        .message-texte { color: #cbd5e1; max-width: 400px; word-wrap: break-word; font-style: italic; }
        
        .actions-container { display: inline-flex; gap: 10px; align-items: center; }
    </style>
</head>
<body>

<div class="header-actions">
    <a href="dashboard.php" class="btn btn-back">← Retour au Dashboard</a>
    <h1>Gestion des Messages <span style="color: #3b82f6;">Rapides</span></h1>
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
            <th>Expéditeur</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date de réception</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($messages)): ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #94a3b8;">Aucun message reçu pour le moment.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($messages as $m): ?>
                <tr style="<?= $m['lu'] == 0 ? 'background: rgba(245, 158, 11, 0.03);' : '' ?>">
                    <td>
                        <?php if ($m['lu'] == 0): ?>
                            <span class="badge badge-non-lu">Non lu</span>
                        <?php else: ?>
                            <span class="badge badge-lu">Lu</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= nettoyer($m['nom']) ?></strong></td>
                    <td><a href="mailto:<?= nettoyer($m['email']) ?>" style="color: #38bdf8; text-decoration: none;"><?= nettoyer($m['email']) ?></a></td>
                    <td class="message-texte">" <?= nettoyer($m['message']) ?> "</td>
                    <td style="color: #94a3b8;"><?= date('d/m/Y à H:i', strtotime($m['date_envoi'])) ?></td>
                    <td>
                        <div class="actions-container">
                            <a href="mailto:<?= nettoyer($m['email']) ?>?subject=RE: <?= urlencode($m['sujet'] ?? 'Votre message sur le Portfolio') ?>" 
                               class="btn btn-reply" 
                               title="Répondre par email">
                                <i class="fas fa-reply"></i> Répondre
                            </a>

                            <?php if ($m['lu'] == 0): ?>
                                <form action="demandes_contact.php" method="POST" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="action" value="marquer_lu">
                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-action">Marquer comme lu</button>
                                </form>
                            <?php else: ?>
                                <span style="color: #64748b; font-size: 0.85rem; font-style: italic;">Traité</span>
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