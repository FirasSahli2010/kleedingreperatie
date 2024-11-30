<?php
namespace App\Components;

class GalleryCls {
    private $galleryView;
    private $twigService;

    public function __construct($galleryView, $twigService) {
        $this->galleryView = $galleryView;
        $this->twigService = $twigService;
    }

    public function displayGallery() {
        $photos = $this->galleryView->getGalleryPhotos();
        $categories = $this->galleryView->getCategories();

        // Render the gallery template with Twig
        return $this->twigService->render('gallery.twig', [
            'photos' => $photos,
            'categories' => $categories
        ]);
    }
}