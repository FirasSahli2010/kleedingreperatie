<?php
namespace App;

use App\Src\Models\HomepageModel;
use App\View;

// Load project setup
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../src/Models/HomepageModel.php';

// Initialize the model
$model = new HomepageModel($GLOBALS['pdo']);

// Fetch data for the homepage
$welcomeMessage = $model->getWelcomeMessage();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - My Project</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome to My Project</h1>
    </header>
    <main>
        <p><?= htmlspecialchars($welcomeMessage) ?></p>
    </main>
</body>
</html>