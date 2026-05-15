<?php 
require_once 'fonctions.php';
$titre_page = "Mes Projets | Ahmed SEYE";
require_once 'composants/navigation.php'; 

// 2. Chargement des données
require_once 'composants/donnees-projets.php'; 

// 3. Logique de filtrage sécurisée et insensible à la casse
$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';
$recherche_minuscule = mb_strtolower($recherche, 'UTF-8');
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
                   value="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>"
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
        <?php 
        $count = 0;
        foreach ($mes_projets as $projet): 
            // Recherche robuste prenant en compte les caractères spéciaux (UTF-8)
            $matchTitre = mb_strpos(mb_strtolower($projet['titre'], 'UTF-8'), $recherche_minuscule) !== false;
            $matchTag = mb_strpos(mb_strtolower($projet['tag'], 'UTF-8'), $recherche_minuscule) !== false;
            $matchDesc = mb_strpos(mb_strtolower($projet['desc'], 'UTF-8'), $recherche_minuscule) !== false; // Ajout du filtre sur la description

            if ($recherche === '' || $matchTitre || $matchTag || $matchDesc): 
                $count++;
        ?>
                <div class="project-card">
                    <div class="project-img-container">
                        <img src="images/<?= htmlspecialchars($projet['img'], ENT_QUOTES, 'UTF-8') ?>" 
                             alt="<?= htmlspecialchars($projet['titre'], ENT_QUOTES, 'UTF-8') ?>" 
                             onerror="this.src='images/default-project.png';">
                    </div>
                    <div class="project-info">
                        <h3><?= htmlspecialchars($projet['titre'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="project-tag"><span><?= htmlspecialchars($projet['tag'], ENT_QUOTES, 'UTF-8') ?></span></p>
                        <p class="project-desc"><?= htmlspecialchars($projet['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
        <?php 
            endif; 
        endforeach; 

        if ($count === 0): ?>
            <div class="no-results">
                <i class="fas fa-search-minus fa-3x"></i>
                <p>Aucun projet ne correspond à "<strong><?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?></strong>"</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php 
require_once 'composants/pied-de-page.php'; 
?>