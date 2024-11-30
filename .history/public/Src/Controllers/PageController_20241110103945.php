<?php
    namespace App\Public\Src\Controllers;

use App\Components\FooterCls;
use App\Components\HeaderCls;
use App\Managers\DatabaseManager;
use App\Managers\DispalyManager;
use App\Models\AboutModel;
use App\Models\ContactModel;
use App\Models\ReviewModel;
use App\View\FooterView;
use App\View\HeaderView;
use App\Models\GalleryModel;
use App\Models\TreatmentModel;
use Migliori\PowerLitePdo\Exception\PaginationException;
use App\Models\BlockModel;
use App\Models\CarouselModel;

class PageController
{
    private DatabaseManager $dbManager;
    private $language = 'nl';

    public function __construct(DatabaseManager $dbManager, $language = 'nl') {

        $this->dbManager = $dbManager;
        $this->language = $language;
        $_SESSION['language'] = $language;
    }
    public function home($language = 'nl'): void
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $this->language = $language;
        $headerModel = new HeaderView($this->dbManager, $this->language);
        $footerModel = new FooterView($this->dbManager, $this->language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $this->language);
        $footer = new FooterCls($footerModel, $twigService, $this->language);
        $blockModle = new BlockModel($this->dbManager, $this->language);
        $blockList = $blockModle->getBlocks($this->language);
        $reviewsModel = new ReviewModel($this->dbManager);
        $reviews = $reviewsModel->getLatestReviews(3, $this->language); // Fetch the last three reviews
        $carouselModel = new CarouselModel($this->dbManager);
        $carouselItems = $carouselModel->getAllCarouselItems($this->language);
        $treatmentModel = new TreatmentModel($this->dbManager);
        $categories = $treatmentModel->getCategories($this->language);
        echo $header->render();
        echo $this->renderView('blocks.twig',  [
                                        'blocks'=>$blockList,
                                        'reviews'=>isset($reviews)?$reviews:[],
                                        'categories'=>$categories,
                                        'carouselItems' => isset($carouselItems)?$carouselItems:[],
                                        'language' => $this->language,
                                    ]);
        echo $footer->render();
    }

    public function contact($language = 'nl'): void
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $headerModel = new HeaderView($this->dbManager);
        $footerModel = new FooterView($this->dbManager);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);
        $openingHours = $this->getOpeningHours();
        echo $header->render();
        // Render the gallery overview page with the fetched galleries
        echo $this->renderContactUsPage('contact.twig', $language);
        echo $footer->render();
    }

    public function saveInfo($language = 'nl') {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $data = array(
            'name' => isset($_POST['name'])?$_POST['name']:'',
            'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
            'subject' => htmlspecialchars($_POST['subject']),
            'message'=> htmlspecialchars($_POST['message']),
            'language' => $language,

        );
        $this->handleContactFormSubmission($data);
        header('Location: /contact?status=created');
    }

    public function galleryItem($id, $language = 'nl'): void
    {
        echo "Gallery Page for photo ID: " . htmlspecialchars($id);
    }

    public function gallery($language = 'nl'): void
    {
        $headerModel = new HeaderView($this->dbManager, $language);
        $footerModel = new FooterView($this->dbManager, $language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);


        $galleryModel = new GalleryModel($this->dbManager);
        $galleries = $galleryModel->getAllGalleries($language);
        echo $header->render();
        // Render the gallery overview page with the fetched galleries
        echo $this->renderView('gallery.twig', ['galleries' => $galleries, 'language' => $language]);
        echo $footer->render();
    }

    public function about($language = 'nl', $id = null): void
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $headerModel = new HeaderView($this->dbManager, $language);
        $footerModel = new FooterView($this->dbManager, $language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);
        if(! isset($id)) {
            $aboutModel = new AboutModel($this->dbManager);
            $aboutContent = $aboutModel->getAboutContent($language);
            $data = [];
            // $teamMembers = $aboutModel->getTeamMembers();
            // foreach ($aboutContent as $aboutUsRecord ) {
            //     $data[] = [
            //         'id' => $aboutUsRecord['id'],
            //         'title' => $aboutUsRecord['title'] ?? 'About Us',
            //         'image1' => $aboutUsRecord['image1'] ?? '',
            //         'content' => $this->trimDescription($aboutUsRecord['description'] ?? 'We are a company dedicated to providing the best services...', 500),
            //         'language' => $language,
            //         // 'team' => $teamMembers
            //     ];
            // }

            foreach ($aboutContent as $aboutUsRecord ) {
                $data[] = [
                    'id' => $aboutUsRecord['id'],
                    'title' => $aboutUsRecord['title'] ?? 'About Us',
                    'image1' => $aboutUsRecord['image1'] ?? '',
                    'content' => ($aboutUsRecord['id'] !== 9)?$this->trimDescription($aboutUsRecord['description'] ?? 'We are a company dedicated to providing the best services...', 500) : $aboutUsRecord['description'],
                    'language' => $language,
                    // 'team' => $teamMembers
                ];
            }

            echo $header->render();
            echo $this->renderView('about.twig', ['records' => $data]);
            echo $footer->render();
        } else {
            $aboutModel = new AboutModel($this->dbManager);
            $aboutContent = $aboutModel->getAboutContentWithId($id, $language);
            $data = [];
            // $teamMembers = $aboutModel->getTeamMembers();
            if (isset($aboutContent) ) {
                $data = [
                    'id' => $aboutContent['id'],
                    'title' => $aboutContent['title'] ?? 'About Us',
                    'image1' => $aboutContent['image1'] ?? '',
                    'content' => $aboutContent['description'] ?? 'We are a company dedicated to providing the best services...',
                    'language' => $language,
                ];
            }
            echo $header->render();
            echo $this->renderView('aboutdetails.twig', ['record' => $data]);
            echo $footer->render();
        }
    }

    // Define a helper function to trim the description safely
function trimDescription($description, $maxLength = 300) {
    // Check if the description is longer than the specified maximum length
    if (strlen($description) > $maxLength) {
        // Get a substring of the description up to the max length
        $trimmedText = substr($description, 0, $maxLength);

        // Find the last space in the trimmed text to avoid cutting words
        $lastSpace = strrpos($trimmedText, ' ');

        // If there's a space, trim the description up to that point
        if ($lastSpace !== false) {
            $trimmedText = substr($trimmedText, 0, $lastSpace);
        }

        // Add an ellipsis or any other suffix to indicate that the text has been trimmed
        $trimmedText .= '...';

        return $trimmedText;
    }

    // Return the full description if it's shorter than the max length
    return $description;
}


    public function treatments($language = 'nl')
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $headerModel = new HeaderView($this->dbManager, $language);
        $footerModel = new FooterView($this->dbManager, $language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);

        //$dbManager = new DatabaseManager();
        $page = $_GET['p'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $treatmentModel = new TreatmentModel($this->dbManager);
        // $treatments = $treatmentModel->getTreatments();

        // Fetch treatments with pagination, sorted by display_order
        $this->dbManager->pagination->select('treatment', '*', ['display' => 1, 'language' => $language], [
            'orderBy' => 'display_order ASC',
        ]);

        $treatments = array();
        while ( $row = $this->dbManager->pagination->fetch()   ) {
            $treatments[] = $row;
        }

        $this->dbManager->db->select('treatment_categories', '*', ['language' => $language]);
        $categories = $this->dbManager->db->fetchAll();

        $total = $this->dbManager->db->selectCount(
            'treatment',
            ['*' => 'rowsCount'],
            ['display' => 1, 'language' => $language],

        );
        if (!$total || !is_object($total) || !property_exists($total, 'rowsCount')) {
            throw new PaginationException('Error getting the total number of records');
        }
        $rowCount = $total->rowsCount;
        $totalPages = intval($rowCount / $limit) + 1;

        echo $header->render();
        echo $this->renderView('treatments.twig', [
            'treatments' => $treatments,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'language' => $language
        ]);
        echo $this->dbManager->pagination->pagine('http://xbeuaty.test/treatments');
        echo $footer->render();
    }

    public function treatmentsById($language = 'nl', $id = null)
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $headerModel = new HeaderView($this->dbManager, $language);
        $footerModel = new FooterView($this->dbManager, $language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);

        //$dbManager = new DatabaseManager();
        $page = $_GET['p'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $treatmentModel = new TreatmentModel($this->dbManager);
        // $treatments = $treatmentModel->getTreatments();

        // Fetch treatments with pagination, sorted by display_order
        $this->dbManager->db->select('treatment', '*', ['display' => 1, 'id' => $id, 'language' => $language], [
            'orderBy' => 'display_order ASC',
        ]);

        $treatments = array();
        while ( $row = $this->dbManager->db->fetch()   ) {
            $treatments[] = $row;
        }

        $this->dbManager->db->select('treatment_categories', '*');
        $categories = $this->dbManager->db->fetchAll();

        $total = $this->dbManager->db->selectCount(
            'treatment',
            ['*' => 'rowsCount'],
            ['display' => 1, 'language' => $language],

        );
        if (!$total || !is_object($total) || !property_exists($total, 'rowsCount')) {
            throw new PaginationException('Error getting the total number of records');
        }
        $rowCount = $total->rowsCount;
        $totalPages = intval($rowCount / $limit) + 1;

        echo $header->render();
        echo $this->renderView('treatmentsById.twig', [
            'treatments' => $treatments,
            'categories' => $categories,
            'language' => $language,
            // 'currentPage' => $page,
            // 'totalPages' => $totalPages
        ]);
        // echo $this->dbManager->pagination->pagine('http://xbeuaty.test/treatmentsById');
        echo $footer->render();
    }

    public function searchTreatments($language = 'nl')
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $headerModel = new HeaderView($this->dbManager, $language);
        $footerModel = new FooterView($this->dbManager, $language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);
        $searchTerm = $_GET['q'] ?? '';
        $categoryId = $_GET['category'] ?? null;
        $conditions = [];

        if (!empty($categoryId) && !empty($searchTerm)) {

            $where = [ 'category_treatment_id' => $categoryId, 'title LIKE '=> '%'.$searchTerm.'%', 'display'=>1, 'language' => $language];
        } elseif (!empty($categoryId)) {

            $where = [ 'category_treatment_id' => $categoryId, 'display'=>1, 'language' => $language];
        } elseif (!empty($searchTerm)) {

            $where = [ 'title LIKE '=> '%'.$searchTerm.'%', 'display'=>1, 'language' => $language];
        } else {

            $where = [ 'display'=>1, 'language' => $language];
        }

        $this->dbManager->pagination->select('treatment', '*', $where, [
            'orderBy' => 'display_order ASC',
        ]);

        $treatments = [];
        while ($row = $this->dbManager->pagination->fetch()) {
            $treatments[] = $row;
        }


        $this->dbManager->db->select('treatment_categories', '*', ['language' => $language]);
        $categories = $this->dbManager->db->fetchAll();

        echo $header->render();
        echo $this->renderView('treatments.twig', [
            'treatments' => $treatments,
            'categories' => $categories,
            'searchTerm' => $searchTerm,
            'language' => $language
        ]);
        echo $this->dbManager->pagination->pagine('http://xbeuaty.test/treatments');
        echo $footer->render();
    }

    public function categoryTreatments($language = 'nl', $id = null)
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $headerModel = new HeaderView($this->dbManager, $language);
        $footerModel = new FooterView($this->dbManager, $language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);

        //$dbManager = new DatabaseManager();
        $page = $_GET['p'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $this->dbManager->pagination->select('treatment', '*', [
            'category_treatment_id' => $id,
            'display' => 1,
            'language' => $language
        ], [
            'orderBy' => 'display_order ASC',
        ]);
        $treatments = array();
        while ($row = $this->dbManager->pagination->fetch()) {
            $treatments[] = $row;
        }

        $this->dbManager->db->select('treatment_categories', '*', ['language' => $language]);
        $categories = $this->dbManager->db->fetchAll();
        echo $header->render();
        echo $this->renderView('treatments.twig', [
            'treatments' => $treatments,
            'categories' => $categories,
            'currentCategory' => $id,
            'language' => $language
        ]);
        echo $footer->render();
    }

    public function categoryTreatmentsList($language = 'nl')
    {

        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $headerModel = new HeaderView($this->dbManager, $language);
        $footerModel = new FooterView($this->dbManager, $language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);

        //$dbManager = new DatabaseManager();
        $page = $_GET['p'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;


        // $treatments = $this->dbManager->db->fetchAll();
        $this->dbManager->pagination->select('treatment', '*', [
            'display' => 1,
            'language' => $language,
        ], [
            'orderBy' => 'display_order ASC',
        ]);
        $treatments = array();

        while ($row = $this->dbManager->pagination->fetch()) {
            if( empty($row->image) || !file_exists(__DIR__.'/../assets/img/treatment_images/'.$row->image )) {
                $row->image = 'logo.png';
            }
            $treatments[] = $row;
        }
        $treatmentModel = new TreatmentModel($this->dbManager);
        $categories = $treatmentModel->getCategories($language);
        echo $header->render();
        echo $this->renderView('treatmentCategorries.twig', [
            'categories' => $categories,
            // 'treatments' => $treatments,
            'language' => $language
        ]);
        echo $footer->render();
    }

    public function tarief($language = 'nl', $id = null)
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $headerModel = new HeaderView($this->dbManager, $language);
        $footerModel = new FooterView($this->dbManager, $language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);

        //$dbManager = new DatabaseManager();
        $page = $_GET['p'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $this->dbManager->db->select('treatment_categories', '*', [
            'display' => 1,
            'language' => $language
        ], [
            'orderBy' => 'display_order ASC',
        ]);
        $categories = $this->dbManager->db->fetchAll();
        $treatments = array();

        foreach ($categories as $category) {
           $this->dbManager->db->select('treatment', '*', ['language' => $language, 'category_id'=> $category->id ]);
           $treatments = $this->dbManager->db->fetchAll();
           $category->treatments = $treatments;
        }
        echo $header->render();
        echo $this->renderView('tarief.twig', [
            'categories' => $categories,
            'language' => $language
        ]);
        echo $footer->render();
    }

    public function getOpeningHours() {
        return $this->dbManager->query("SELECT * FROM opening_hours ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
    }

    // Method to display details of a specific gallery or image
    public function galleryDetail($id): void
    {
        $galleryModel = new GalleryModel($this->dbManager);
        $gallery = $galleryModel->getGalleryById($id);

        if (!$gallery) {
            echo "Gallery not found";
            return;
        }

        // Render the gallery detail page with the fetched gallery data
        echo $this->renderView('gallery-detail.twig', ['gallery' => $gallery]);
    }

    public function review($language = 'nl'): void
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $headerModel = new HeaderView($this->dbManager, $language);
        $footerModel = new FooterView($this->dbManager, $language);
        $twigService = new DispalyManager();
        // Instantiate components
        $header = new HeaderCls($headerModel, $twigService, $language);
        $footer = new FooterCls($footerModel, $twigService, $language);

        $reviewModel = new ReviewModel($this->dbManager);
        $reviewContent = $reviewModel->getAboutContent($language);
        $data = [];
        // $teamMembers = $aboutModel->getTeamMembers();
        foreach ($reviewContent as $reviewRecord ) {
            $data[] = [
                'id' => $reviewRecord['id'],
                'name' => $reviewRecord['name'] ?? '-----',
                'stars' => $reviewRecord['stars'] ?? 5,
                'email' => $reviewRecord['email'] ?? '',
                'review' => $reviewRecord['review'] ?? 'We are a company dedicated to providing the best services...',
                'replay' => $reviewRecord['replay'] ?? 'We are a company dedicated to providing the best services...',
                'language' => $language
                // 'team' => $teamMembers
            ];
        }
        echo $header->render();
        // Render the gallery overview page with the fetched galleries
        echo $this->renderView('review.twig', ['records'=>$data]);
        echo $footer->render();
    }


    public function handleFormSubmission(): void
    {
        // Handle form data
        echo "Form Submitted!";
    }

    private function renderView(string $template, array $data = []): string
    {
        $twigService = new DispalyManager();

        return $twigService->render($template, $data);
    }

    public function renderContactUsPage(string $template, $language = 'nl')
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        $contactModel = new ContactModel($this->dbManager);
        $twigService = new DispalyManager();
        $contactInfoDetails = $contactModel->getContactInfo($language);
        $openingHours = $this->getOpeningHours();

        // Pass $openingHours to the template
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array(
                'name' => isset($_POST['name'])?$_POST['name']:'',
                'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
                'subject' => htmlspecialchars($_POST['subject']),
                'message'=> htmlspecialchars($_POST['message']),

            );

        }
        // Contact Information
        $contactInfo = [
            'clinic_address' => $contactInfoDetails[0]['address'],
            'clinic_postcode' => $contactInfoDetails[0]['postcode'].', '.$contactInfoDetails[0]['city'],
            'clinic_phone' => $contactInfoDetails[0]['phone'],
            'clinic_Mobile' => $contactInfoDetails[0]['mobile'],
            'clinic_email' => $contactInfoDetails[0]['email'],
            'clinic_hours' => 'Mon-Fri: 9:00 AM - 6:00 PM',
            'clinic_lat' => 52.268,
            'clinic_lng' => 5.1722,
            'opening_hours' => $openingHours,
            'language' => $language
        ];
        return $twigService->render($template, $contactInfo);
    }

    private function handleContactFormSubmission($data)
    {
        // Sanitize and validate data
        $name = htmlspecialchars($data['name']);
        // $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $email = $data['email'];
        $subject = htmlspecialchars($data['subject']);
        $message = htmlspecialchars($data['message']);
        $language = $data['language'];

        if ($email) {
            // Save the message in the database
            $this->dbManager->query("INSERT INTO messages (name, email, subject, `description`, created_at) VALUES (?, ?, ?, ?, ?, NOW())", [$name, $email, $subject, $language, $message]);
        }
    }

    public function submitReview($language = 'nl')
    {
        if( (!isset($_SESSION['language'])) || (empty($_SESSION['language'])) ) {
            $_SESSION['language'] = $language;
            $this->language = $language;
        }
        if (empty($language) && (isset($_SESSION['language'])) || ( isset($_SESSION['language']) && !empty($language) && $language !== $_SESSION['language']  )) {
            $language = $_SESSION['language'];
        }
        // Validate input
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $review = $_POST['review'];

        $reviewModel = new ReviewModel($this->dbManager);

        // Save the review to the database
        $reviewModel->insertReview($name, $email, $phone, $review);

        // Redirect to a confirmation page
        header('Location: /klantbeoordelingen');
    }
}
