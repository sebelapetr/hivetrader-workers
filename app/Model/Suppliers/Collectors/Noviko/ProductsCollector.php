<?php

namespace App\Model\Services\Suppliers\Collectors\Noviko;

use Nextras\Dbal\Connection;

class ProductsCollector extends BaseCollector
{
    public function collect(Connection $dbConnection, string $logFolder): void
    {
        $url = "restapi/b2b/zbozi";
        $this->getData($dbConnection, $url, $logFolder);
    }

    public function processData(\SimpleXMLElement $data): void
    {
        foreach ($data->product as $product) {
            $this->dbConnection->query("
                UPDATE `supplier_products` SET
                `product_brand_id` = NULL,
                `catalog_price_without_vat` = '" . $product->catalogPrice . "',
                `catalog_price_with_vat` = '" . $product->catalogPriceVAT . "',
                `supplier_price_without_vat` = '" . $product->clientPrice . "',
                `supplier_price_with_vat` = '" . $product->clientPriceVAT . "',
                `ean` = '" . $product->ean . "',
                `name` = '" . $product->productName . "',
                `vat` = '" . intval($product->VAT) . "',
                `weight` = '" . floatval($product->weight) . "'
                WHERE ((`supplier_id` = '" . $product->productInternalId . "'));
            ");
        }
    }
}
