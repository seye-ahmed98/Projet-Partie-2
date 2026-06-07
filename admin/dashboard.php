<?php
session_start();
require_once '../config/connexion.php';
require_once '../fonctions.php';

// Vérifier si l'administrateur est connecté
if (empty($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header("Location: connexion.php");
    exit;
}

// 1. Nombre total de projets
$stmt = $pdo->query('SELECT COUNT(*) FROM projets');
$nb_projets = $stmt->fetchColumn();

// 2. Nombre de messages de contact non lus
$stmt = $pdo->query('SELECT COUNT(*) FROM messages_contact WHERE lu = 0');
$nb_messages = $stmt->fetchColumn();

// 3. Nombre de demandes de projet non lues (table : demandes_projet)
$stmt = $pdo->query('SELECT COUNT(*) FROM demandes_projet WHERE lu = 0');
$nb_demandes = $stmt->fetchColumn();

// 4. Les 5 dernières visites
$stmt = $pdo->query('SELECT * FROM visites ORDER BY date_visite DESC LIMIT 5');
$dernieres_visites = $stmt->fetchAll();

// 5. Les 5 dernières demandes de projet
$stmt = $pdo->query('SELECT * FROM demandes_projet ORDER BY date_demande DESC LIMIT 5');
$dernieres_demandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administration | Portfolio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styles Globaux */
        * { margin: 0; padding: 0; box-shadow: none; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f172a; color: #f8fafc; display: flex; min-height: 100vh; }

        /* Menu Latéral (Sidebar) */
        .sidebar { width: 260px; background: #1e293b; padding: 25px 20px; display: flex; flex-direction: column; justify-content: space-between; border-right: 1px solid #334155; position: fixed; height: 100vh; }
        .sidebar-brand { font-size: 1.3rem; font-weight: bold; margin-bottom: 40px; color: #3b82f6; display: flex; align-items: center; gap: 10px; }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 12px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; padding: 12px; border-radius: 8px; font-weight: 500; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .btn-logout { background: #ef4444; color: white !important; text-align: center; justify-content: center; margin-top: auto; }
        .btn-logout:hover { background: #dc2626 !important; }

        /* Contenu Principal */
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .header-dash { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; border-bottom: 1px solid #334155; padding-bottom: 20px; }
        .header-dash h1 { font-size: 1.8rem; font-weight: 600; }
        .header-dash span { color: #3b82f6; }

        /* Grille des Cartes Statistiques */
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px; }
        .card { background: #1e293b; padding: 25px; border-radius: 12px; border-top: 4px solid transparent; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .card-projets { border-top-color: #10b981; }
        .card-messages { border-top-color: #f59e0b; }
        .card-demandes { border-top-color: #3b82f6; }
        
        .card-info h3 { font-size: 0.9rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .card-info p { font-size: 1.8rem; font-weight: bold; }
        .card-icon { font-size: 2.2rem; opacity: 0.8; }
        .card-projets .card-icon { color: #10b981; }
        .card-messages .card-icon { color: #f59e0b; }
        .card-demandes .card-icon { color: #3b82f6; }

        /* Section Tableaux */
        .tables-section { display: grid; grid-template-columns: 1fr; gap: 35px; }
        .table-container { background: #1e293b; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .table-container h2 { font-size: 1.2rem; margin-bottom: 20px; color: #cbd5e1; display: flex; align-items: center; gap: 10px; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #334155; font-size: 0.95rem; }
        th { background: rgba(15, 23, 42, 0.3); color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
        tr:hover td { background: rgba(255, 255, 255, 0.01); }
        
        /* Badges de couleur */
        .ip-badge { background: #334155; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 0.85rem; }
        .budget-badge { color: #f59e0b; font-weight: bold; }
        .link-view { color: #3b82f6; text-decoration: none; font-size: 0.85rem; font-weight: 500; }
        .link-view:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <div class="sidebar-brand">
                <i class="fas fa-user-shield"></i> Admin Panel
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-chart-pie"></i> Vue d'ensemble</a></li>
                <li><a href="projets_liste.php"><i class="fas fa-tasks"></i> Gérer les projets</a></li>
                <li><a href="demandes_contact.php"><i class="fas fa-envelope"></i> Messages rapides</a></li>
                <li><a href="demandes_projets.php"><i class="fas fa-folder-open"></i> Demandes projets</a></li>
                <li><a href="administrateurs.php"><i class="fas fa-users-cog"></i> Administrateurs</a></li>
            </ul>
        </div>
        <a href="deconnexion.php" class="sidebar-menu a btn-logout"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
    </div>

    <div class="main-content">
        <div class="header-dash">
            <h1>Bonjour, <span><?= nettoyer($_SESSION['admin_prenom']); ?></span> !</h1>
            <div style="color: #94a3b8; font-size: 0.9rem;">
                <i class="far fa-calendar-alt"></i> <?= date('d/m/Y'); ?>
            </div>
        </div>

        <div class="cards-grid">
            <div class="card card-projets">
                <div class="card-info">
                    <h3>Total Projets</h3>
                    <p><?= $nb_projets; ?></p>
                </div>
                <div class="card-icon"><i class="fas fa-laptop-code"></i></div>
            </div>

            <div class="card card-messages">
                <div class="card-info">
                    <h3>Messages non lus</h3>
                    <p><?= $nb_messages; ?></p>
                </div>
                <div class="card-icon"><i class="fas fa-comments"></i></div>
            </div>

            <div class="card card-demandes">
                <div class="card-info">
                    <h3>Demandes Projets</h3>
                    <p><?= $nb_demandes; ?></p>
                </div>
                <div class="card-icon"><i class="fas fa-briefcase"></i></div>
            </div>
        </div>

        <div class="tables-section">
            
            <div class="table-container">
                <h2><i class="fas fa-paper-plane" style="color: #3b82f6;"></i> Dernières demandes de projet</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Email</th>
                            <th>Type de projet</th>
                            <th>Budget</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dernieres_demandes)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">Aucune demande de projet récente.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dernieres_demandes as $demande): ?>
                            <tr>
                                <td><strong><?= nettoyer($demande['nom']); ?></strong></td>
                                <td><?= nettoyer($demande['email']); ?></td>
                                <td><?= nettoyer($demande['type_projet']); ?></td>
                                <td class="budget-badge"><?= $demande['budget'] ? nettoyer($demande['budget']) . ' FCFA' : 'Non spécifié'; ?></td>
                                <td style="color: #94a3b8;"><?= date('d/m/Y H:i', strtotime($demande['date_demande'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h2><i class="fas fa-eye" style="color: #10b981;"></i> Journal des dernières visites sur le site</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Adresse IP</th>
                            <th>Page visitée</th>
                            <th>Date & Heure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dernieres_visites)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #94a3b8; padding: 20px;">Aucun historique de visite disponible.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dernieres_visites as $visite): ?>
                            <tr>
                                <td><span class="ip-badge"><?= nettoyer($visite['adresse_ip']); ?></span></td>
                                <td style="color: #cbd5e1;"><?= nettoyer($visite['page']); ?></td>
                                <td style="color: #94a3b8;"><?= date('d/m/Y H:i', strtotime($visite['date_visite'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>