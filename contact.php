<?php 
/**
 * CONTACT.PHP - Portfolio Ahmed SEYE
 * Gestion des formulaires avec validation, sécurité et persistance.
 */

require_once 'fonctions.php'; 
require_once 'composants/navigation.php'; 

// 1. Initialisation des variables
$nom_projet = ""; $budget = ""; $type_projet = ""; $besoin = "";
$nom_contact = ""; $email_contact = ""; $message_contact = "";
$erreur_nom = ""; $erreur_email = ""; // Ajout pour la validation de l'email
$succes = false; $type_alerte = "";
$demande = []; // Tableau associatif exigé pour la section 5.2

// 2. Logique de traitement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // FORMULAIRE PROJET (Section 5.2)
    if (isset($_POST['submit_projet'])) {
        // Stockage direct dans un tableau associatif
        $demande = [
            'nom'         => $_POST['projet_nom'] ?? "",
            'budget'      => $_POST['budget'] ?? "",
            'type_projet' => $_POST['type_prestation'] ?? "",
            'description' => $_POST['besoin'] ?? ""
        ];
        
        // Extraction pour la persistance dans le HTML
        $nom_projet  = $demande['nom'];
        $budget      = $demande['budget'];
        $type_projet = $demande['type_projet'];
        $besoin      = $demande['description'];
        
        if (champ_requis($nom_projet) && champ_requis($type_projet) && champ_requis($besoin)) {
            $succes = true;
            $type_alerte = "projet";
        }
    } 
    
    // FORMULAIRE MESSAGE RAPIDE (Section 5.1)
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
        
        // CORRECTION COMPLÈTE : Validation de l'email avec filter_var côté serveur
        if (!champ_requis($email_contact)) {
            $erreur_email = "L'adresse e-mail est obligatoire.";
        } elseif (!filter_var($email_contact, FILTER_VALIDATE_EMAIL)) {
            $erreur_email = "Format d'adresse e-mail invalide.";
        }
        
        // Validation globale
        if (empty($erreur_nom) && empty($erreur_email) && champ_requis($message_contact)) {
            $succes = true;
            $type_alerte = "contact";
        }
    }
}
?>

<main class="container" style="padding-top: 120px; padding-bottom: 60px;">
    
    <?php if ($succes): ?>
        <div class="alert-success">
            <?php if ($type_alerte === "projet"): ?>
                Proposition pour '<strong><?= nettoyer($nom_projet) ?></strong>' (<?= nettoyer($type_projet) ?>) reçue. Merci !
            <?php else: ?>
                Message de <strong><?= nettoyer($nom_contact) ?></strong> envoyé avec succès !
            <?php endif; ?>
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
                
                <input type="text" name="projet_nom" value="<?= nettoyer($nom_projet) ?>" placeholder="Nom du Projet" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.1); border: 1px solid #334155; color: white; border-radius: 5px;">
                
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