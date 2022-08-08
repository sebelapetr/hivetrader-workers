<?php

namespace App\Model\Services\Suppliers\Collectors\Noviko;

use App\Model\Services\Connectors\NovikoConnector;
use Nextras\Dbal\Connection;
use Tracy\Debugger;

abstract class BaseCollector implements ICollector
{
    public NovikoConnector $connector;
    public Connection $dbConnection;

    public function __construct(NovikoConnector $connector)
    {
        $this->connector = $connector;
    }

    public function getData(Connection $dbConnection, string $url, string $logFolder): void
    {
        $errorLogFolder = $logFolder . "/error/error-".date("Y-m-d-H");
        $this->dbConnection = $dbConnection;
        $connection = $this->connector->getConnection();
        $connection->get($url);

        if ($connection->error) {
            Debugger::log('Error: ' . $connection->errorCode . ': ' . $connection->errorMessage, $errorLogFolder);
            Debugger::log($connection->response, $errorLogFolder);
            Debugger::log('----------------------------------------------------', $errorLogFolder);
        } else {
            $this->processData($connection->response);
        }

        $connection->close();
    }
}
