<?php

namespace App\Model\Services\Suppliers\Collectors\Noviko;

use Nextras\Dbal\Connection;

class ProductsCollector extends BaseCollector
{
    public function collect(Connection $dbConnection, string $logFolder): void
    {
        $this->dbConnection = $dbConnection;
        $url = "restapi/b2b/zbozi";
        $this->getData($dbConnection, $url, $logFolder);
    }

    public function processData(string $tmpFile): bool
    {
        $xml = file_get_contents($tmpFile);
        if ($xml === false) {
            return false;
        }
        $data = new \SimpleXMLElement($xml);

        foreach ($data->product as $product) {

            $values = [
                'supplier_id' => $product->productInternalId,
                'catalog_price_without_vat' => $product->catalogPrice,
                'catalog_price_with_vat' => $product->catalogPriceVAT,
                'supplier_price_without_vat' => $product->clientPrice,
                'supplier_price_with_vat' => $product->clientPriceVAT,
                'ean' => $product->ean,
                'name' => $product->productName,
                'vat' => $product->VAT,
                'weight' => strval($product->weight) !== "" ? intval($product->weight) : null,
                'to_order' => strval($product->toOrder) === "false" ? 0 : 1,
                'updated_at' => date("Y-m-d H:i:s"),
            ];

            $this->dbConnection->query('INSERT INTO supplier_products %values ON DUPLICATE KEY UPDATE %set', $values, $values);
        }


        //todo PROCESSED 100 ITEMS LOG
        return true;
    }
}
