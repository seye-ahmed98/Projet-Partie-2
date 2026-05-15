<?php 
require_once 'fonctions.php';
$titre_page = "Accueil | Ahmed SEYE";
require_once 'composants/navigation.php'; 
?>

<section id="accueil" class="hero-section">
    <div class="tech-bg-overlay"></div>
    <div class="matrix-code-lines"></div>

    <div class="container hero-flex">
        <div class="hero-text glass-cyber-card">
            <h1>Ahmed <span class="text-accent">SEYE</span></h1>
            <p class="subtitle">Étudiant en Génie Logiciel, Administration Réseau & Affaires.</p>
            <p class="hero-desc">Je combine la rigueur du code avec la précision des outils de DAO pour créer des solutions d'habitat intelligent et durable.</p>
    
            <div class="hero-btns">
                <a href="projets.php" class="btn-terminal">Consulter mes projets</a>
            </div>
        </div>

        <div class="hero-image">
            <div class="profile-pic-container">
                <div class="hologram-layer"></div>
                <img src="images/profil.jpeg" alt="Ahmed Seye" class="profile-pic">
            </div>
        </div>
    </div>
</section>

<section id="apropos" class="container about-section">
    <div class="about-flex">

        <div class="about-text">
            <span class="parcours-subtitle">QUI SUIS-JE ?</span>
            <h2 class="parcours-main-title">À PROPOS</h2>
    
            <div class="bio-content">
                <p>Je m'appelle <strong class="text-white">Ahmed SEYE</strong>, étudiant en <strong class="text-accent">GALR</strong> (Génie Architectural, Logiciel et Réseaux) à l'ESTM Dakar et en <strong class="text-accent">Administration des Affaires</strong>.</p>
                <p>Ma force réside dans la pluridisciplinarité : de la modélisation 3D sur AutoCAD à la cybersécurité offensive sous Kali Linux, tout en intégrant une vision stratégique et financière.</p>
                <p>Mon objectif est de fusionner mes compétences en design architectural et en ingénierie logicielle pour créer des solutions d'habitat intelligent, durable et hautement sécurisé.</p>
            </div>

            <div class="about-pillars">
                <div class="pillar-badge">
                    <i class="fas fa-code"></i>
                    <span>Génie Logiciel</span>
                </div>
                <div class="pillar-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>Cybersécurité</span>
                </div>
                <div class="pillar-badge">
                    <i class="fas fa-drafting-compass"></i>
                    <span>Architecture 3D</span>
                </div>
                <div class="pillar-badge">
                    <i class="fas fa-chart-line"></i>
                    <span>Management</span>
                </div>
            </div>
        </div>

        <div class="about-image-wrapper">
            <div class="image-frame">
                <img src="images/prof.jpeg" alt="Ahmed Seye" class="about-img">
            </div>
        </div>

    </div>
</section>

<section id="parcours" class="container">
    <div class="parcours-header">
        <span class="parcours-subtitle">EXPÉRIENCES & ÉTUDES</span>
        <h2 class="parcours-main-title">Mon <span class="text-blue">parcours</span></h2>
    </div>

    <div class="custom-timeline">
        <div class="timeline-block">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <p class="timeline-date">2025 — Présent</p>
                <h3 class="timeline-job">L2 en Génie Architectural, Logiciel et Réseaux (GALR)</h3>
                <p class="timeline-org">ESTM Dakar, Sénégal</p>
                <p class="timeline-text">Spécialisation en développement multi-langages (Java, PHP, Python...) et administration de systèmes sécurisés.</p>
            </div>
        </div>

        <div class="timeline-block">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <p class="timeline-date">2024 — Présent</p>
                <h3 class="timeline-job">Assistant du Directeur des Affaires Financières</h3>
                <p class="timeline-org">AGIMEX / AZ TRANSPORT</p>
                <p class="timeline-text">Gestion de trésorerie, suivi comptable et coordination administrative via des outils ERP.</p>
            </div>
        </div>

        <div class="timeline-block">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <p class="timeline-date">2023 — Présent</p>
                <h3 class="timeline-job">Administration des Affaires</h3>
                <p class="timeline-org">Collège La Cité, Ontario (Distance)</p>
                <p class="timeline-text">Formation en management, droit des affaires et finance d'entreprise.</p>
            </div>
        </div>
    </div>
</section>

<section class="testimonials-section container">
    <span class="parcours-subtitle">TÉMOIGNAGES</span>
    <h2 class="parcours-main-title">Ce qu'on dit de <span class="text-blue">mon travail</span></h2>
    <div class="testimonials-grid">

        <div class="testimonial-card">
            <span class="quote-icon">“</span>
            <p class="testimonial-text">"Ahmed fait preuve d'une grande rigueur dans ses projets de modélisation 3D. Sa vision technique est un atout majeur."</p>
            <div class="testimonial-meta">
                <strong class="author-name">Mme Maimouna</strong>
                <span class="author-title">Binôme de Projet</span>
            </div>
        </div>

        <div class="testimonial-card">
            <span class="quote-icon">“</span>
            <p class="testimonial-text">"Sa maîtrise d'AutoCAD et sa capacité à optimiser les structures logicielles ont été cruciales sur nos projets immobiliers."</p>                  
            <div class="testimonial-meta">
                <strong class="author-name">Aminata SY</strong>
                <span class="author-title">Technicienne à AUBI-SA</span>
            </div>
        </div>

        <div class="testimonial-card">
            <span class="quote-icon">“</span>
            <p class="testimonial-text">"Lors de nos labs de cybersécurité, il a démontré une persévérance remarquable pour résoudre des erreurs complexes."</p>
            <div class="testimonial-meta">
                <strong class="author-name">Dr Moussa Dethié SARR</strong>
                <span class="author-title">DSI de UIDT de Thiès</span>
            </div>
        </div>

        <div class="testimonial-card">
            <span class="quote-icon">“</span>
            <p class="testimonial-text">"Alliant discipline et humanité, sa curiosité et son humour ont fait de lui un moteur de motivation pour tous ses camarades."</p>
            <div class="testimonial-meta">
                <strong class="author-name">Serigne Dr Ahmed El Mansour</strong>
                <span class="author-title">Marabout et Diplomate</span>
            </div>
        </div>
    </div> 
</section>

<section id="competences" class="container skills-section">
    <div style="text-align: center; margin-bottom: 50px;">
        <span class="parcours-subtitle">MON BAGAGE</span>
        <h2 class="parcours-main-title">Expertise <span class="text-blue">Technique</span></h2>
    </div>

    <div class="skills-grid">
        <div class="skills-category-card">
            <div class="category-header">
                <i class="fas fa-code"></i>
                <h3>Développement Logiciel</h3>
            </div>
            <div class="tech-list">
                <div class="tech-item">
                    <i class="devicon-java-plain colored"></i>
                    <span>Java</span>
                </div>
                <div class="tech-item">
                    <i class="devicon-python-plain colored"></i>
                    <span>Python</span>
                </div>
                <div class="tech-item">
                    <i class="devicon-php-plain colored"></i>
                    <span>PHP</span>
                </div>
                <div class="tech-item">
                    <i class="devicon-c-plain colored"></i>
                    <span>Langage C</span>
                </div>
            </div>
        </div>

        <div class="skills-category-card">
            <div class="category-header">
                <i class="fas fa-shield-alt"></i>
                <h3>Sécurité & Réseaux</h3>
            </div>
            <div class="tech-list">
                <div class="tech-item">
                    <i class="devicon-linux-plain"></i>
                    <span>Kali Linux</span>
                </div>
                <div class="tech-item">
                    <i class="fas fa-server" style="color: #10b981;"></i>
                    <span>pfSense / IDS Suricata</span>
                </div>
                <div class="tech-item">
                    <i class="devicon-docker-plain colored"></i>
                    <span>Docker</span>
                </div>
            </div>
        </div>

        <div class="skills-category-card">
            <div class="category-header">
                <i class="fas fa-drafting-compass"></i>
                <h3>DAO & Modélisation 3D</h3>
            </div>
            <div class="tech-list">
                <div class="tech-item">
                    <i class="fas fa-pencil-ruler" style="color: #ea580c;"></i>
                    <span>AutoCAD</span>
                </div>
                <div class="tech-item">
                    <i class="fas fa-cube" style="color: #eab308;"></i>
                    <span>SketchUp</span>
                </div>
            </div>
        </div>

        <div class="skills-category-card">
            <div class="category-header">
                <i class="fas fa-chart-pie"></i>
                <h3>Systèmes & Gestion</h3>
            </div>
            <div class="tech-list">
                <div class="tech-item">
                    <i class="fab fa-windows" style="color: #0078d4;"></i>
                    <span>Dynamics 365</span>
                </div>
                <div class="tech-item">
                    <i class="fas fa-wallet" style="color: #f97316;"></i>
                    <span>Outils ERP & Trésorerie</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contact" style="display: none;">
    </section>

<?php 
require_once 'composants/pied-de-page.php'; 
?>