<?PHP
namespace App\Components;

use Bootstrap;

    class MainMenuCls {
        private $twig;
        private $db;
        public function __construct($twig, $db) {
            if($twig) {
                $this->twig = $twig;
            }
            if ($db) {
                $this->db = $db;
            }
        }
        public function DisplayFooter(): void {
            $twig = Bootstrap::initTwig();
            
        }

        private function renderMenu() {
            $this->db->select('users', ['id', 'name', 'title'], ['display' => 1]);
            $menuItemArray = array();
            while ($row = $this->db->fetch()) {
                $menuItemArray[] =  $row->id . ', ' . $row->name . ', ' . $row->email;
            }
            $template = $this->twig->load('mainmenu.html');
            echo $template->render();
        }
    }