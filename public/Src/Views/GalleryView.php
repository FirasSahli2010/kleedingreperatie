<?php
namespace App\Src\View;
use App\Managers\DatabaseManager;

class GalleryView {
    private $dbService;

    public function __construct() {
        $this->dbService = new DatabaseManager();
    }

    public function getGalleryPhotos() {
        // Fetch gallery photos from the database
        return $this->dbService->query("SELECT * FROM gallery_photos");
    }

    public function getCategories() {
        // Fetch photo categories
        return $this->dbService->query("SELECT * FROM gallery_categories");
    }
}