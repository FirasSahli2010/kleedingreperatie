<?PHP
namespace App\Managers;

use Migliori\PowerLitePdo\Db;
use Migliori\PowerLitePdo\Pagination;
use PDO;

    class DatabaseManager
    {
       
        public $db;
        public $pagination;
        private PDO $pdo;
        public function __construct()
        {
            $container = require_once __DIR__ . '/../vendor/migliori/power-lite-pdo/src/bootstrap.php';
            $this->db = $container->get(Db::class);
            $this->pagination = $container->get(Pagination::class);

            $options = [
                'activeClass' => 'active-page',
                'navLength' => 5,
                'rewriteLinks' => false,
                'rewriteTransition' => '/',
                'rewriteExtension' => '.html'
            ];
            $this->pagination->setOptions($options);
            if (defined('PHPUNIT_TESTSUITE_RUNNIG') || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1') {
                $dsn = 'mysql:host=localhost;dbname=xbeuaty';
                $user = 'root';
                $password = '';
            } else {
                $dsn = 'mysql:host=localhost;dbname=h_000854b7_xbeuaty';
                $user = 'h_000854b7_xbeuaty';
                $password = '^4215Wzyv';
            }
    
            $this->pdo = new PDO($dsn, $user, $password);
        }
    
        public function query(string $sql, array $params = []): array
        {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }

        public function InitDatabasePagination () {
            $this->pagination = require_once __DIR__ . '/vendor/migliori/power-lite-pdo/src/bootstrap.php';
            return $this->pagination->get(Pagination::class);
        }
    }