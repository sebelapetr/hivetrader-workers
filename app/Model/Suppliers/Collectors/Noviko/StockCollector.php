<?php

namespace App\Model\Services\Suppliers\Collectors\Noviko;

use App\Model\Services\Connectors\NovikoConnector;
use Nextras\Dbal\Connection;
use Tracy\Debugger;

class StockCollector extends BaseCollector
{
    public function collect(Connection $dbConnection): void
    {
        $this->dbConnection = $dbConnection;
        $url = "restapi/b2b/zbozi/stav";
        $this->getData($dbConnection, $url);
    }

    public function processData(string $tmpFile): bool
    {
        $xml = file_get_contents($tmpFile);
        if ($xml === false) {
            return false;
        }
        $data = new \SimpleXMLElement($xml);

        foreach ($data->productStav as $stav) {

            $supplierId = strval($stav->productId);

            $productId = $this->dbConnection->query("
                SELECT id FROM supplier_products WHERE supplier_internal_id = $supplierId
            ")->fetchField(0);

            if ($productId !== null) {
                $productId = strval($productId);
                $values = [
                    "supplier_product_id" => $productId,
                    "min_stock_level" => $stav->minStockLevel,
                    "created_at" => date("Y-m-d H:i:s")
                ];
                $this->dbConnection->query('INSERT INTO supplier_product_history %values', $values);
            } else {
                // todo MISSING PRODUCT LOG
            }

        }

        // todo PROCESSED 1000 ITEMS
        return true;
    }
}
