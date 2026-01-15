<?php
declare(strict_types=1);

namespace App\Services;

use App\Support\Config;
use App\Support\HttpClient;

final class SubmitService
{
    public function submit(array $stats, string $token): array
    {
        return HttpClient::postJson(Config::SUBMIT_URL, ['stats' => $stats], $token, 30);
    }
}
