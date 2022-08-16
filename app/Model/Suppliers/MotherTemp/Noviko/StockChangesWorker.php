<?php

namespace App\Model\Services\Suppliers\MotherTemp\Noviko;

use Nextras\Dbal\Connection;

class StockChangesWorker extends BaseWorker
{
    public function processData(Connection $dbConnection): bool
    {
        $this->dbConnection = $dbConnection;

        $products = $this->dbConnection->query('SELECT id FROM supplier_products ORDER BY id');

         foreach ($products as $product) {


             $supplierProductIdId = $product->id;
             $lastLogChange = $this->dbConnection->query('SELECT * FROM supplier_product_stock_changes WHERE supplier_product_id = %i ORDER BY created_at DESC LIMIT 1', $supplierProductIdId)->fetch();
             $lastLogStock = $this->dbConnection->query('SELECT * FROM supplier_product_history WHERE supplier_product_id = %i ORDER BY created_at DESC LIMIT 1', $supplierProductIdId)->fetch();

             if ($lastLogStock == null) {
                 //log product has not stock log
                 continue;
             }

             $values = [
                 'supplier_product_id' => $supplierProductIdId,
                 'actual_quantity' => $lastLogStock->min_stock_level,
                 'created_at' => date("Y-m-d H:i:s"),
             ];

             if ($lastLogChange !== null) {
                $values["difference"] = ($lastLogStock->min_stock_level - $lastLogChange->actual_quantity);
             } else {
                $values["difference"] = 0;
             }

             $this->dbConnection->query('INSERT INTO supplier_product_stock_changes %values', $values);

         }


        //todo PROCESSED 100 ITEMS LOG
        return true;
    }
}
