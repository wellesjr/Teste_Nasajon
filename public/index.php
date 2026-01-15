<?php
declare(strict_types=1);
require __DIR__ . '/../app/Support/Env.php';
\App\Support\Env::load(__DIR__ . '/../.env');

require __DIR__ . '/../app/Support/Config.php';
require __DIR__ . '/../app/Support/HttpClient.php';
require __DIR__ . '/../app/Support/Normalizer.php';

require __DIR__ . '/../app/Models/MunicipioIbge.php';
require __DIR__ . '/../app/Models/ResultadoLinha.php';

require __DIR__ . '/../app/Services/CsvService.php';
require __DIR__ . '/../app/Services/IbgeService.php';
require __DIR__ . '/../app/Services/MatcherService.php';
require __DIR__ . '/../app/Services/StatsService.php';
require __DIR__ . '/../app/Services/SubmitService.php';

require __DIR__ . '/../app/Controllers/IbgeController.php';

use App\Controllers\IbgeController;

$controller = new IbgeController();
$controller->run($argv);
