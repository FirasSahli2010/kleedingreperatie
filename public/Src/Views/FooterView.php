<?PHP
namespace App\Src\View;

use App\Managers\DatabaseManager;

    class FooterView
    {
        private DatabaseManager $dbService;
        private $language;

        public function __construct(DatabaseManager $dbService, $language = 'nl')
        {
            $this->language = $language;
            $this->dbService = $dbService;
        }

        public function getFooterLinks(): array
        {
            // return $this->dbService->query("SELECT name, link FROM footer_links ORDER BY position");
            return [];
        }

        public function getFooterContent() {

        }
    }