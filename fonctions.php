<?php
/**
 * Nettoie une valeur pour l'affichage HTML (Sécurité XSS)
 */
function nettoyer(string $valeur): string {
    return htmlspecialchars(trim($valeur), ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie si un champ est vide après avoir retiré les espaces
 */
function champ_requis(string $valeur): bool {
    return !empty(trim($valeur));
}

/**
 * Vérifie si le nom est valide (uniquement lettres, espaces, tirets)
 */
function nom_valide(string $valeur): bool {
    // Cette expression régulière autorise les lettres (avec accents), les espaces et tirets
    return preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/", trim($valeur));
}


// ============================================
// CSRF — Générer un jeton
// ============================================
function generer_jeton_csrf() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// ============================================
// CSRF — Vérifier un jeton
// ============================================
function verifier_csrf() {
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die('Requête invalide. Jeton CSRF manquant ou incorrect.');
    }
}

// ============================================
// VISITES — Enregistrer une visite (Optimisée)
// ============================================
function enregistrer_visite($pdo) {
    // ÉVITER DE LOGUER L'ADMINISTRATEUR : Si la session admin est active, on arrête tout de suite
    if (!empty($_SESSION['admin_connecte']) && $_SESSION['admin_connecte'] === true) {
        return;
    }

    // Gestion des proxys : on récupère la première IP de la liste s'il y en a plusieurs
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $les_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($les_ips[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    // Détection automatique du nom de la page courante (ex: /index.php)
    $page = $_SERVER['SCRIPT_NAME']; 

    try {
        $stmt = $pdo->prepare('INSERT INTO visites (adresse_ip, page) VALUES (?, ?)');
        $stmt->execute([$ip, $page]);
    } catch (PDOException $e) {
        error_log("Erreur lors de la journalisation : " . $e->getMessage());
    }
}