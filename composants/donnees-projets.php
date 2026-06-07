<?php
// Au tout début de votre fichier projets.php
require_once 'config/connexion.php';
require_once 'fonctions.php';

try {
    // Récupération des projets depuis la base de données
    $stmt = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC");
    $mes_projets_bdd = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur affichage projets : " . $e->getMessage());
    $mes_projets_bdd = [];
}
?>

<div class="projets-container">
    <?php if (empty($mes_projets_bdd)): ?>
        <p style="text-align: center; color: #94a3b8;">Aucun projet à afficher pour le moment.</p>
    <?php else: ?>
        <?php foreach ($mes_projets_bdd as $projet): ?>
            <div class="projet-card">
                <div class="projet-image">
                    <img src="images/projets/<?= nettoyer($projet['image']) ?>" alt="<?= nettoyer($projet['titre']) ?>">
                </div>
                <div class="projet-infos">
                    <span class="badge"><?= nettoyer($projet['categorie']) ?></span>
                    <h3><?= nettoyer($projet['titre']) ?></h3>
                    <p><?= nl2br(nettoyer($projet['description'])) ?></p>
                    
                    <?php if (!empty($projet['lien']) && $projet['lien'] !== '#'): ?>
                        <a href="<?= nettoyer($projet['lien']) ?>" target="_blank" class="btn-projet">Voir le projet</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>