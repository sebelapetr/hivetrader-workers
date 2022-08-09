<?php declare(strict_types = 1);

namespace App\Console\Suppliers\Collectors\Noviko;

use App\Console\Suppliers\Collectors\BaseSupplierCollectorsCommand;
use App\Model\Orm;
use App\Model\Services\Suppliers\Collectors\Noviko\ProductsCollector;
use App\Model\Services\Suppliers\Collectors\Noviko\StockCollector;
use Exception;
use Nette\DI\Container;
use Nextras\Dbal\Connection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class NovikoSupplierStockCollector extends BaseSupplierCollectorsCommand
{
    public StockCollector $stockCollector;
    public string $logFolder;

    public function __construct(Orm $orm, Container $container)
    {
        $this->logFolder = "suppliers/collectors/noviko/stock";

        /** @var StockCollector $stockCollector */
        $stockCollector = $container->getByName('novikoStockCollector');
        $this->stockCollector = $stockCollector;

        parent::__construct($orm, $this->logFolder, $container);
    }

    protected function configure(): void
    {
        $this->setName('suppliers:collectors:noviko:stock');
        $this->setDescription('Import noviko stock');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->beginExecution(self::LOCK_NOVIKO_STOCK, 'Start noviko stock', $output);

        $this->processImportBatches($output);

        $this->endExecution($output);
        return 0;
    }

    protected function processBatch(OutputInterface $output, int $batchNumber): array
    {
        $this->stockCollector->collect($this->connection);
        return [
            "status" => "OK"
        ];
    }

}
