<?PHP
namespace App\Managers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

    class DispalyManager
    {
        private Environment $twig;

        public function __construct()
        {
            $loader = new FilesystemLoader(__DIR__ . '/../templates');
            $this->twig = new Environment($loader, [
                // 'cache' => __DIR__ . '/../../cache',
                'cache' => false,
                'debug' => true,
            ]);
        }

        public function render(string $template, array $data = []): string
        {
            return $this->twig->render($template, $data);
        }

        public function getTwig(): Environment
        {
            return $this->twig;
        }
    }