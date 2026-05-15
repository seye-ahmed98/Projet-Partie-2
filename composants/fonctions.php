<?php
/**
 * Nettoie une valeur pour éviter les failles XSS et les espaces inutiles.
 */
function nettoyer(string $valeur): string {
    return htmlspecialchars(trim($valeur));
}

/**
 * Vérifie si un champ est vide.
 */
function champ_requis(string $valeur): bool {
    return !empty(trim($valeur));
}
?>