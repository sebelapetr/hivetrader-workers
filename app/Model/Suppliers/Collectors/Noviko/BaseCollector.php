<?php

namespace App\Model\Services\Suppliers\Collectors\Noviko;

use App\Model\Services\Connectors\NovikoConnector;
use Nette\Utils\Random;
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

    public function getData(Connection $dbConnection, string $url): void
    {
        $this->dbConnection = $dbConnection;
        $connection = $this->connector->getConnection();
        $connection->get($url);

        if ($connection->error) {
            throw new \Exception($connection->errorCode . ': ' . $connection->errorMessage . " - " . $connection->response);
        } else {
            $response = $connection->response;
            if ($response instanceof \SimpleXMLElement) {
                $response = $response->asXML();
            }
            $fileName = Random::generate(15).'.xml';
            $tmpFile = ROOT_DIR . '/import-data/' . $fileName;
            if (file_put_contents ($tmpFile, $response) !== false) {
                $processData = $this->processData($tmpFile);
                if ($processData === true) {
                    unlink($tmpFile);
                } else {
                    throw new \Exception('Error processing data file ' . $fileName);
                }
            } else {
                throw new \Exception('Error file put contents of ' . $fileName);
            }
        }

        $connection->close();
    }
}
