<?php

class HomeController {
    /**
     * Affiche la page d'accueil.
     */
    public function index() {
        // Charge la vue de la page d'accueil
        require_once __DIR__ . '/../views/home.php';
    }
}
