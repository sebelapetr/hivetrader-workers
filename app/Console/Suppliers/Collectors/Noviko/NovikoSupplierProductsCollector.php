<?php declare(strict_types = 1);

namespace App\Console\Suppliers\Collectors\Noviko;

use App\Console\Suppliers\Collectors\BaseSupplierCollectorsCommand;
use App\Model\Orm;
use App\Model\Services\ConsoleLogger\ConsoleLoggerService;
use App\Model\Services\Suppliers\Collectors\Noviko\ProductsCollector;
use Exception;
use Nextras\Dbal\Connection;
use Nette\DI\Container;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

final class NovikoSupplierProductsCollector extends BaseSupplierCollectorsCommand
{
    public ProductsCollector $productsCollector;
    public string $logFolder;

    public function __construct(Orm $orm, Container $container)
    {
        $this->logFolder = "suppliers/collectors/noviko/products";

        /** @var ProductsCollector $productsCollector */
        $productsCollector = $container->getByName('novikoProductsCollector');
        $this->productsCollector = $productsCollector;

        parent::__construct($orm, $this->logFolder, $container);
    }

    protected function configure(): void
    {
        $this->setName('suppliers:collectors:noviko:products');
        $this->setDescription('Import noviko products');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->beginExecution(self::LOCK_NOVIKO_PRODUCTS, 'Start noviko products', $output);

        $this->processImportBatches($output);

        $this->endExecution($output);
        return 0;
    }

    protected function processBatch(OutputInterface $output, int $batchNumber): array
    {
        $this->productsCollector->collect($this->connection);
        return [
            "status" => "OK"
        ];
    }

}
