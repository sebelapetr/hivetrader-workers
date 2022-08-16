<?php

namespace App\Model\Services\Suppliers\MotherTemp\Noviko;

use Nextras\Dbal\Connection;

interface IWorker
{
    public function processData(Connection $dbConnection): bool;
}
