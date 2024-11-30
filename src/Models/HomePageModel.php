<?php
namespace App\Src\Models;
// src/Models/HomepageModel.php

class HomepageModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getWelcomeMessage() {
        // Example of fetching data from a table (e.g., "settings" or "content")
        // $stmt = $this->pdo->query("SELECT `description` FROM homepage_content WHERE type = 'welcome_message'");
        $stmt = $this->pdo->query("SELECT `description` FROM aboutus WHERE display = 1");
        return $stmt->fetchColumn();
    }
}