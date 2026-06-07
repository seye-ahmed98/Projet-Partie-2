<?php
// admin/administrateurs.php
session_start();

// 1. Sécurité : Vérification de la session admin
if (empty($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header("Location: connexion.php");
    exit;
}

require_once '../config/connexion.php';
require_once '../fonctions.php';

$csrf_token = generer_jeton_csrf();
$message_succes = "";
$message_erreur = "";

// 2. Traitement du formulaire d'ajout d'un nouvel administrateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_admin') {
    verifier_csrf();

    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    if (!empty($prenom) && !empty($nom) && !empty($email) && !empty($mot_de_passe)) {
        try {
            // Requête mise à jour sur la table 'administrateurs'
            $stmt_verif = $pdo->prepare("SELECT id FROM administrateurs WHERE email = ?");
            $stmt_verif->execute([$email]);
            
            if ($stmt_verif->fetch()) {
                $message_erreur = "Cet email est déjà utilisé par un autre administrateur.";
            } else {
                // Hachage sécurisé du mot de passe
                $mdp_hache = password_hash($mot_de_passe, PASSWORD_BCRYPT);

                // Insertion propre
                $stmt_ins = $pdo->prepare("INSERT INTO administrateurs (prenom, nom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
                $stmt_ins->execute([$prenom, $nom, $email, $mdp_hache]);
                $message_succes = "L'administrateur a été ajouté avec succès.";
            }
        } catch (PDOException $e) {
            $message_erreur = "Impossible d'ajouter l'administrateur : " . $e->getMessage();
        }
    } else {
        $message_erreur = "Veuillez remplir tous les champs.";
    }
}

// 3. LA LIGNE 53 CORRIGÉE ICI : Requête sur 'administrateurs' au lieu de 'utilisateurs'
try {
    $stmt = $pdo->query("SELECT id, prenom, nom, email, date_creation FROM administrateurs ORDER BY date_creation DESC");
    $admins = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Administrateurs | Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: white; padding: 30px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; }
        .btn-back { background: #334155; color: white; }
        .btn-submit { background: #3b82f6; color: white; padding: 12px; width: 100%; border-radius: 6px; font-weight: bold; margin-top: 10px; cursor: pointer; border: none; }
        .btn-submit:hover { background: #2563eb; }
        
        .layout-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px; align-items: start; }
        .card-box { background: #1e293b; padding: 25px; border-radius: 8px; border: 1px solid #334155; }
        h2 { font-size: 1.2rem; margin-top: 0; margin-bottom: 20px; color: #3b82f6; display: flex; align-items: center; gap: 10px; }
        
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; gap: 8px; }
        .form-group label { color: #94a3b8; font-size: 0.9rem; }
        .form-group input { padding: 10px; background: #0f172a; border: 1px solid #334155; color: white; border-radius: 6px; font-size: 0.95rem; }
        
        table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #111827; color: #94a3b8; font-weight: 600; }
        
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #10b981; color: white; }
        .alert-danger { background: #ef4444; color: white; }
        
        .user-avatar { width: 35px; height: 35px; background: #334155; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #3b82f6; font-weight: bold; }
    </style>
</head>
<body>

<div class="header-actions">
    <a href="dashboard.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Retour au Dashboard</a>
    <h1>Espace <span style="color: #3b82f6;">Administrateurs</span></h1>
    <div></div>
</div>

<?php if ($message_succes): ?><div class="alert alert-success"><?= $message_succes ?></div><?php endif; ?>
<?php if ($message_erreur): ?><div class="alert alert-danger"><?= $message_erreur ?></div><?php endif; ?>

<div class="layout-grid">
    
    <div class="card-box">
        <h2><i class="fas fa-users"></i> Gestionnaires actifs</h2>
        <table>
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Nom Complet</th>
                    <th>Email</th>
                    <th>Créé le</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td>
                            <div class="user-avatar">
                                <?= strtoupper(substr(nettoyer($admin['prenom']), 0, 1)) ?>
                            </div>
                        </td>
                        <td><strong><?= nettoyer($admin['prenom']) . ' ' . nettoyer($admin['nom']) ?></strong></td>
                        <td style="color: #cbd5e1;"><?= nettoyer($admin['email']) ?></td>
                        <td style="color: #94a3b8; font-size: 0.9rem;"><?= date('d/m/Y', strtotime($admin['date_creation'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card-box">
        <h2><i class="fas fa-user-plus"></i> Ajouter un administrateur</h2>
        <form method="POST" action="administrateurs.php">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="action" value="ajouter_admin">

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" required placeholder="Ex: Ahmed">
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required placeholder="Ex: SEYE">
            </div>

            <div class="form-group">
                <label for="email">Adresse Email</label>
                <input type="email" name="email" id="email" required placeholder="Ex: ahmed@example.com">
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-check"></i> Créer le compte
            </button>
        </form>
    </div>

</div>

</body>
</html>