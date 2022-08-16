<?php

namespace App\Model\Services\Suppliers\MotherTemp\Noviko;

use Nextras\Dbal\Connection;

abstract class BaseWorker implements IWorker
{
    public Connection $dbConnection;
}
