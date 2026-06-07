<?php 
// Démarrage de la session en toute première ligne
session_start();

// Inclusion des fonctions et de la connexion à la base de données
require_once 'fonctions.php';
require_once 'config/connexion.php';

// CORRECTION : Ajout du second argument obligatoire pour identifier la page
enregistrer_visite($pdo, 'Projets');

$titre_page = "Mes Projets | Ahmed SEYE";
require_once 'composants/navigation.php'; 

// Recherche
$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';

// Requête SQL avec LIKE si recherche, sinon tous les projets
if ($recherche !== '') {
    $stmt = $pdo->prepare('
        SELECT * FROM projets 
        WHERE titre LIKE ? OR description LIKE ?
        ORDER BY date_creation DESC
    ');
    $terme = '%' . $recherche . '%';
    $stmt->execute([$terme, $terme]);
} else {
    $stmt = $pdo->query('SELECT * FROM projets ORDER BY date_creation DESC');
}

$projets = $stmt->fetchAll();
?>

<header class="container project-hero">
    <span class="parcours-subtitle">PORTFOLIO</span>
    <h1 class="parcours-main-title">Mes <span class="text-blue">Réalisations</span></h1>
    <p>Découvrez mes travaux en ingénierie logicielle, IoT, cybersécurité et conception architecturale.</p>
</header>

<main class="container py-5">
    <section class="search-section">
        <form action="projets.php" method="GET" class="search-form">
            <input type="text" 
                   name="recherche" 
                   placeholder="Rechercher (Java, Sécurité, AutoCAD...)" 
                   value="<?= nettoyer($recherche) ?>"
                   class="search-input">
            <button type="submit" class="btn-terminal btn-search">
                <i class="fas fa-search"></i>
            </button>
            <?php if ($recherche !== ''): ?>
                <a href="projets.php" class="btn-reset">
                    <i class="fas fa-times"></i>
                </a>
            <?php endif; ?>
        </form>
    </section>

    <div class="projects-grid">
        <?php if (empty($projets)): ?>
            <div class="no-results">
                <i class="fas fa-search-minus fa-3x"></i>
                <p>Aucun projet ne correspond à "<strong><?= nettoyer($recherche) ?></strong>"</p>
            </div>
        <?php else: ?>
            <?php foreach ($projets as $projet): ?>
                <div class="project-card">
                    <div class="project-img-container">
                        <img src="images/projets/<?= nettoyer($projet['image'] ?? 'default-project.png') ?>" 
                             alt="<?= nettoyer($projet['titre']) ?>"
                             onerror="this.src='images/default-project.png';">
                    </div>
                    <div class="project-info">
                        <h3><?= nettoyer($projet['titre']) ?></h3>
                        <p class="project-tag"><span><?= nettoyer($projet['technologies']) ?></span></p>
                        <p class="project-desc"><?= nettoyer($projet['description']) ?></p>
                        <?php if (!empty($projet['lien'])): ?>
                            <a href="<?= nettoyer($projet['lien']) ?>" target="_blank">Voir le projet</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'composants/pied-de-page.php'; ?>