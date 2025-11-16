<?php

/**
 * Échappe une chaîne de caractères pour l'afficher en toute sécurité dans du HTML.
 *
 * @param string|null $string La chaîne à échapper.
 * @return string La chaîne échappée.
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
