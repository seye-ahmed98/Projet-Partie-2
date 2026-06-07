<?php 
/**
 * CONTACT.PHP - Portfolio Ahmed SEYE
 * Gestion des formulaires avec validation, sécurité, persistance et CSRF.
 */

// 1. DÉMARRAGE DE LA SESSION EN TOUTE PREMIÈRE LIGNE
session_start();

// 2. INCLUSIONS OBLIGATOIRES
require_once 'fonctions.php'; 
require_once 'config/connexion.php';

// 3. JOURNALISATION DE LA VISITE AUTOMATIQUE
enregistrer_visite($pdo);

// 4. GÉNÉRATION DU JETON CSRF POUR LES FORMULAIRES
$csrf_token = generer_jeton_csrf();

$titre_page = "Contact | Ahmed SEYE";
require_once 'composants/navigation.php'; 

// Initialisation des variables
$nom_projet = ""; $budget = ""; $type_projet = ""; $besoin = "";
$email_projet = ""; $telephone_projet = ""; // Nouveaux champs pour le formulaire projet

$nom_contact = ""; $email_contact = ""; $message_contact = "";
$erreur_nom = ""; $erreur_email = ""; $erreur_general = "";
$succes = false; $type_alerte = "";
$demande = []; 

// 5. LOGIQUE DE TRAITEMENT DES FORMULAIRES (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ÉTAPE SÉCURITÉ CRUCIALE : Vérification commune du jeton CSRF
    verifier_csrf();
    
    // FORMULAIRE PROJET (Section 5.2) -> Insertion dans 'demandes_projet'
    if (isset($_POST['submit_projet'])) {
        
        $demande = [
            'nom'         => trim($_POST['projet_nom'] ?? ""),
            'email'       => trim($_POST['projet_email'] ?? ""), // Récupération de l'email projet
            'telephone'   => trim($_POST['projet_tel'] ?? ""),   // Récupération du téléphone projet
            'budget'      => trim($_POST['budget'] ?? ""),
            'type_projet' => trim($_POST['type_prestation'] ?? ""),
            'description' => trim($_POST['besoin'] ?? "")
        ];
        
        $nom_projet       = $demande['nom'];
        $email_projet     = $demande['email'];
        $telephone_projet = $demande['telephone'];
        $budget           = $demande['budget'];
        $type_projet      = $demande['type_projet'];
        $besoin           = $demande['description'];
        
        // Validation des champs requis
        if (champ_requis($nom_projet) && champ_requis($email_projet) && champ_requis($type_projet) && champ_requis($besoin)) {
            
            if (!filter_var($email_projet, FILTER_VALIDATE_EMAIL)) {
                $erreur_general = "Format d'adresse e-mail invalide pour le projet.";
            } else {
                try {
                    // Requête préparée pour la table demandes_projet avec email et téléphone dynamiques
                    $stmt = $pdo->prepare('
                        INSERT INTO demandes_projet (nom, email, telephone, type_projet, description, budget, lu, date_demande) 
                        VALUES (?, ?, ?, ?, ?, ?, 0, NOW())
                    ');
                    
                    $stmt->execute([$nom_projet, $email_projet, $telephone_projet, $type_projet, $besoin, $budget]);
                    
                    $succes = true;
                    $type_alerte = "projet";
                    
                    // Réinitialisation des champs après succès
                    $nom_projet = $budget = $type_projet = $besoin = $email_projet = $telephone_projet = "";
                } catch (PDOException $e) {
                    error_log("Erreur insertion demandes_projet : " . $e->getMessage());
                    $erreur_general = "Une erreur technique est survenue lors de l'envoi de la proposition.";
                }
            }
        } else {
            $erreur_general = "Veuillez remplir tous les champs obligatoires du formulaire projet.";
        }
    } 
    
    // FORMULAIRE MESSAGE RAPIDE (Section 5.1) -> Insertion dans 'messages_contact'
    elseif (isset($_POST['submit_contact'])) {
        $nom_contact     = $_POST['nom'] ?? "";
        $email_contact   = $_POST['email'] ?? "";
        $message_contact = $_POST['message'] ?? "";
        
        // Validation du nom
        if (!champ_requis($nom_contact)) {
            $erreur_nom = "Le nom est obligatoire.";
        } elseif (!nom_valide($nom_contact)) {
            $erreur_nom = "Nom invalide (lettres uniquement).";
        }
        
        // Validation de l'email
        if (!champ_requis($email_contact)) {
            $erreur_email = "L'adresse e-mail est obligatoire.";
        } elseif (!filter_var($email_contact, FILTER_VALIDATE_EMAIL)) {
            $erreur_email = "Format d'adresse e-mail invalide.";
        }
        
        // Validation globale et insertion
        if (empty($erreur_nom) && empty($erreur_email) && champ_requis($message_contact)) {
            try {
                // Requête préparée pour la table messages_contact
                $stmt = $pdo->prepare('
                    INSERT INTO messages_contact (nom, email, message, lu, date_envoi) 
                    VALUES (?, ?, ?, 0, NOW())
                ');
                $stmt->execute([$nom_contact, $email_contact, $message_contact]);
                
                $succes = true;
                $type_alerte = "contact";
                
                // Réinitialisation des champs après succès
                $nom_contact = $email_contact = $message_contact = "";
            } catch (PDOException $e) {
                error_log("Erreur insertion messages_contact : " . $e->getMessage());
                $erreur_general = "Une erreur technique est survenue lors de l'envoi du message.";
            }
        }
    }
}
?>

<main class="container" style="padding-top: 120px; padding-bottom: 60px;">
    
    <?php if ($succes): ?>
        <div class="alert-success">
            <?php if ($type_alerte === "projet"): ?>
                Proposition reçue avec succès ! Merci.
            <?php else: ?>
                Message envoyé avec succès !
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($erreur_general): ?>
        <div class="alert-danger" style="background: #ff4d4d; color: white; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
            <?= nettoyer($erreur_general) ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        
        <div class="contact-card" style="background: rgba(0,123,255,0.05); padding: 30px; border-radius: 12px; border: 1px solid rgba(0,123,255,0.2);">
            <h2 style="font-size: 1.5rem; margin-bottom: 25px;">Infos <span class="text-blue">Directes</span></h2>
            
            <p style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                <i class="fas fa-envelope contact-icon" style="color: #007bff; width: 20px; text-align: center; font-size: 1.1rem;"></i>
                <span>
                    <strong>Email:</strong> 
                    <a href="mailto:seyea094@gmail.com" class="contact-link">seyea094@gmail.com</a>
                </span>
            </p>
            
            <p style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                <i class="fab fa-whatsapp contact-icon" style="color: #25D366; width: 20px; text-align: center; font-size: 1.3rem;"></i>
                <span>
                    <strong>WhatsApp:</strong> 
                    <a href="https://wa.me/221773898796" target="_blank" class="contact-link">+221 77 389 87 96</a>
                </span>
            </p>
            
            <p style="display: flex; align-items: center; gap: 12px;">
                <i class="fab fa-linkedin contact-icon" style="color: #0A66C2; width: 20px; text-align: center; font-size: 1.3rem;"></i>
                <span>
                    <strong>LinkedIn:</strong> 
                    <a href="https://www.linkedin.com/in/ahmed-seye" target="_blank" class="contact-link">Ahmed SEYE</a>
                </span>
            </p>
        </div>

        <div class="contact-card" style="background: #1e293b; padding: 30px; border-radius: 12px; border-top: 5px solid #007bff; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <h2 style="font-size: 1.3rem; margin-bottom: 20px;">Présenter un <span class="text-blue">projet</span></h2>
            <form action="contact.php" method="POST" style="display: flex; flex-direction: column; gap: 12px;">
                
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <input type="text" name="projet_nom" value="<?= nettoyer($nom_projet) ?>" placeholder="Nom du Projet" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.1); border: 1px solid #334155; color: white; border-radius: 5px;">
                
                <input type="email" name="projet_email" value="<?= nettoyer($email_projet) ?>" placeholder="Votre adresse email *" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.1); border: 1px solid #334155; color: white; border-radius: 5px;">
                
                <input type="tel" name="projet_tel" value="<?= nettoyer($telephone_projet) ?>" placeholder="Numéro de téléphone" style="width: 100%; padding: 10px; background: rgba(0,0,0,0.1); border: 1px solid #334155; color: white; border-radius: 5px;">
                
                <select name="type_prestation" required style="width: 100%; padding: 10px; background: #1e293b; border: 1px solid #334155; color: white; border-radius: 5px;">
                    <option value="">Type de service...</option>
                    <option value="Développement" <?= $type_projet == 'Développement' ? 'selected' : '' ?>>Développement (Java/PHP/Python)</option>
                    <option value="Architecture 3D" <?= $type_projet == 'Architecture 3D' ? 'selected' : '' ?>>Architecture & 3D (AutoCAD/SketchUp)</option>
                    <option value="Cybersécurité" <?= $type_projet == 'Cybersécurité' ? 'selected' : '' ?>>Sécurité & Réseaux (pfSense)</option>
                    <option value="Agriculture" <?= $type_projet == 'Agriculture' ? 'selected' : '' ?>>Agriculture & Élevage</option>
                </select>

                <input type="number" name="budget" value="<?= nettoyer($budget) ?>" placeholder="Budget estimé (FCFA)" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.1); border: 1px solid #334155; color: white; border-radius: 5px;">

                <textarea name="besoin" rows="3" placeholder="Détails du besoin..." required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.1); border: 1px solid #334155; color: white; border-radius: 5px;"><?= nettoyer($besoin) ?></textarea>
                
                <button type="submit" name="submit_projet" class="btn-terminal">> Envoyer la proposition</button>
            </form>
        </div>

        <div class="contact-card" style="background: #1e293b; padding: 30px; border-radius: 12px; border-left: 5px solid #007bff;">
            <h2 style="font-size: 1.3rem; margin-bottom: 20px;">Message <span class="text-blue">Rapide</span></h2>
            <form action="contact.php" method="POST" style="display: flex; flex-direction: column; gap: 12px;">
                
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <input type="text" name="nom" value="<?= nettoyer($nom_contact) ?>" placeholder="Votre nom" required 
                           pattern="[a-zA-ZÀ-ÿ\s\-]+" title="Lettres uniquement"
                           style="width: 100%; padding: 10px; background: rgba(0,0,0,0.1); border: 1px solid <?= $erreur_nom ? '#ff4d4d' : '#334155' ?>; color: white; border-radius: 5px;">
                    <?php if ($erreur_nom): ?>
                        <span style="color: #ff4d4d; font-size: 0.8rem;"><?= $erreur_nom ?></span>
                    <?php endif; ?>
                </div>

                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <input type="email" name="email" value="<?= nettoyer($email_contact) ?>" placeholder="votre@email.com" required 
                           style="width: 100%; padding: 10px; background: rgba(0,0,0,0.1); border: 1px solid <?= $erreur_email ? '#ff4d4d' : '#334155' ?>; color: white; border-radius: 5px;">
                    <?php if ($erreur_email): ?>
                        <span style="color: #ff4d4d; font-size: 0.8rem;"><?= $erreur_email ?></span>
                    <?php endif; ?>
                </div>
                
                <textarea name="message" rows="5" placeholder="Votre message..." required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.1); border: 1px solid #334155; color: white; border-radius: 5px;"><?= nettoyer($message_contact) ?></textarea>
                
                <button type="submit" name="submit_contact" class="btn-terminal">> Exécuter</button>
            </form>
        </div>

    </div>
</main>

<style>
    .alert-success { background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px; font-weight: bold; }
    .btn-terminal { background: #007bff; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; transition: 0.3s; font-family: monospace; font-weight: bold; }
    .btn-terminal:hover { background: #0056b3; transform: translateY(-2px); }
    .text-blue { color: #007bff; }
    .contact-link { color: #ffffff; text-decoration: none; transition: color 0.3s ease; }
    .contact-card p:hover .contact-link { color: #007bff; text-decoration: underline; }
    .contact-icon { transition: transform 0.3s ease; }
    .contact-card p:hover .contact-icon { transform: scale(1.2); }
</style>

<?php require_once 'composants/pied-de-page.php'; ?>