<?PHP
namespace App\Src\View;

use App\Managers\DatabaseManager;

    class HeaderView
    {
        private DatabaseManager $dbManager;
        private $language = 'nl';
    
        public function __construct(DatabaseManager $dbService, $langauge = 'nl')
        {
            $this->dbManager = $dbService;
            $this->language = $langauge;
        }
    
        public function getTitle(): string
        {
            $this->dbManager->db->select('settings', ['title'=> 'setting_value'], ['setting_key' => 'site_title', 'language' => $this->language]);
            $result = $this->dbManager->db->fetch();
            $this->dbManager->db->select('settings', ['title'=> 'setting_value'], ['setting_key' => 'site_name', 'language' => $this->language]);
            $resultTitle = $this->dbManager->db->fetch();
            return $resultTitle->setting_value .'::'.$result->setting_value;
        }

        public function getLogo(): string
        {
            $this->dbManager->db->select('settings', ['setting_value'], ['setting_key' => 'logo', 'language' => $this->language]);
            $result = $this->dbManager->db->fetch();
    
            return $result->setting_value;
        }
    
        public function getNavbarItems(): array
        {
            $this->dbManager->db->select('menu_items', ['id', 'name', 'title', 'link'], ['display' => 1, 'lang' => $this->language]);
            return $this->dbManager->db->fetchAll();
        }

        // src/Controller/BaseController.php

        public function getThemeSettings()
        {
            $this->dbManager->db->select('theme_settings', ['setting_key', 'setting_value'], ['language' => 'nl']);
            $dataset =  $this->dbManager->db->fetchAll();
            $settings = [];
            foreach ($dataset as $row) {
                $settings[$row->setting_key] = $row->setting_value;
            }
            return $settings;
        }

    }