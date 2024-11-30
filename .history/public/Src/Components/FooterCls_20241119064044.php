<?PHP
    namespace App\Src\Components;

use App\Managers\DispalyManager;
use App\View\FooterView;

class FooterCls
{
    private FooterView $view;
    private DispalyManager $twigService;
    private $language;

    public function __construct(FooterView $footerView, DispalyManager $twigService, $language = 'nl')
    {
        $this->view = $footerView;
        $this->twigService = $twigService;
        $this->language = $language;
    }

    public function render(): string
    {
        // Optionally, you could fetch dynamic content for the footer
        $data = [
            'footer_content' => $this->view->getFooterContent(), // Assuming getFooterContent fetches data
            'language' => $this->language,
        ];

        return $this->twigService->render('footer.twig', $data);
    }
}