<?php

namespace App\Model\Services\Suppliers\Collectors\Noviko;

use Nextras\Dbal\Connection;

interface ICollector
{
    public function collect(Connection $dbConnection, string $logFolder): void;

    public function processData(string $tmpFile): bool;
}
