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