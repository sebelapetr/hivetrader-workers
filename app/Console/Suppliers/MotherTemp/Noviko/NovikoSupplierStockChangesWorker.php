<?php declare(strict_types = 1);

namespace App\Console\Suppliers\MotherTemp\Noviko;

use App\Console\Suppliers\MotherTemp\BaseMotherTempCommand;
use App\Model\Orm;
use App\Model\Services\ConsoleLogger\ConsoleLoggerService;
use App\Model\Services\Suppliers\Collectors\Noviko\ProductsCollector;
use App\Model\Services\Suppliers\MotherTemp\Noviko\StockChangesWorker;
use Exception;
use Nextras\Dbal\Connection;
use Nette\DI\Container;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

final class NovikoSupplierStockChangesWorker extends BaseMotherTempCommand
{
    public StockChangesWorker $stockChangesWorker;
    public string $logFolder;

    public function __construct(Orm $orm, Container $container)
    {
        $this->logFolder = "suppliers/mothertemp/noviko/stockchanges";

        /** @var StockChangesWorker $stockChangesWorker */
        $stockChangesWorker = $container->getByName('motherTempStockChangesWorker');
        $this->stockChangesWorker = $stockChangesWorker;

        parent::__construct($orm, $this->logFolder, $container);
    }

    protected function configure(): void
    {
        $this->setName('suppliers:mothertemp:noviko:stock-changes');
        $this->setDescription('Process noviko stock changes');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->beginExecution(self::LOCK_NOVIKO_STOCK_CHANGES, 'Start noviko stock changes', $output);

        $this->processImportBatches($output);

        $this->endExecution($output);
        return 0;
    }

    protected function processBatch(OutputInterface $output, int $batchNumber): array
    {
        $this->stockChangesWorker->processData($this->connection);
        return [
            "status" => "OK"
        ];
    }

}
