<?php

namespace App\Model\Services\Suppliers\Collectors\Noviko;

use App\Model\Services\Connectors\NovikoConnector;
use Nextras\Dbal\Connection;
use Tracy\Debugger;

class StockCollector extends BaseCollector
{
    public function collect(Connection $dbConnection, string $logFolder): void
    {
        $url = "restapi/b2b/zbozi/stav";
        $this->getData($dbConnection, $url, $logFolder);
    }

    public function processData(\SimpleXMLElement $data): void
    {;
        foreach ($data->productStav as $stav) {
            $supplierId = strval($stav->productId);
            $productId = $this->dbConnection->query("
                SELECT id FROM supplier_products WHERE supplier_id = $supplierId
            ")->fetchField(0);

            if ($productId !== null) {
                $productId = strval($productId);
                $this->dbConnection->query("
                    INSERT INTO `stock` (`min_stock_level`, `productId`, `created_at`, `to_order`)
                    VALUES ($stav->minStockLevel, $productId, now(), $stav->toOrder);
                ");
            }

        }
    }
}
