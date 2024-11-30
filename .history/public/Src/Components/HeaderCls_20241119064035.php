<?PHP
    namespace  App\Src\Components;

use App\Managers\DatabaseManager;
use App\Managers\DispalyManager;

use App\Src\View\HeaderView;

    class HeaderCls
    {
        private HeaderView $view;
        private DispalyManager $twigService;
        private $language;
    
        public function __construct(HeaderView $headerView, DispalyManager $twigService, $language = 'nl')
        {
            $this->view = $headerView;
            $this->twigService = $twigService;
            $this->language = $language;
        }
    
        public function render(): string
        {
            $settings = (object)($this->view->getThemeSettings());
            $title = $this->view->getTitle();
            $logo = $this->view->getLogo();
            
            $data = [
                'logo' => $logo,
                'title' => $title,
                'backgroundcolor' => $settings->backgroundcolor,
                'linkcolor' => $settings->linkcolor,
                'navbar_items' => $this->view->getNavbarItems(),
                'color' => $settings->primarycolor,
                'fontsize' => ($settings->fontsize).'px',
                'language' => ($this->language ?? 'nl'),
            ];
    
            return $this->twigService->render('header.twig', $data);
        }
    }