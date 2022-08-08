<?php declare(strict_types = 1);

namespace App\Console\Suppliers\Collectors;

use App\Model\Erp\Exception\LoggedException;
use App\Model\Orm;
use App\Model\Services\ConsoleLogger\ConsoleLoggerService;
use Nette\Database\Connection;
use Nette\DI\Container;
use Nextras\Dbal\Utils\DateTimeImmutable;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

abstract class BaseSupplierCollectorsCommand extends Command
{

	use LockableTrait;

	protected Orm $orm;

    public \Nextras\Dbal\Connection $connection;

    public string $errorLogFolder;
    public string $infoLogFolder;
    public string $launchLogFolder;

	public const LOCK_NOVIKO_PRODUCTS = 'lock_noviko_products';
	public const LOCK_NOVIKO_STOCK = 'lock_noviko_stock';

	protected static $defaultName = 'suppliers:collectors';

	protected const COMMAND_RUN_TIMER = 'commandRun';
	protected const COMMAND_BATCH_TIMER = 'commandBatch';

	protected int $limit = PHP_INT_MAX;

	protected int $batchLimit = PHP_INT_MAX;

	protected int $processed = 0;

	public function __construct(Orm $orm, string $logFolder, Container $container)
	{
		parent::__construct(null);
		$this->orm = $orm;
        $this->errorLogFolder = $logFolder . "/error/error-".date("Y-m-d-H");
        $this->infoLogFolder = $logFolder . "/info/info-".date("Y-m-d-H");
        $this->launchLogFolder = $logFolder . "/launch/launch-".date("Y-m-d-H");

        /** @var \Nextras\Dbal\Connection $connection */
        $connection = $container->getByName('dbal.connection');
        $this->connection = $connection;
	}

	protected function configure(): void
	{
		$this->setDescription('Foo Commands base');
	}

	/**
	 * @throws RuntimeException
	 */
	protected function checkLock(string $name): void
	{
		if (!$this->lock($name)) {
			throw new RuntimeException('The command is already running in another process.');
		}
	}

    /**
     * @param array<string, mixed> $res
     */
	protected function writeResult(array $res, OutputInterface $output): void
	{
		foreach ($res as $key => $value) {
			if (is_array($value)) {
				$value = implode(', ', $value);
			}

			$output->writeln($key . ': ' . $value);
		}
	}

	protected function startTimer(string $timer): void
	{
		Debugger::timer($timer);
	}

	protected function printTime(string $timer, OutputInterface $output): void
	{
		$output->writeln('Time: ' . number_format(Debugger::timer($timer), 3, '.', ' ') . ' s');
	}

	protected function beginExecution(string $lock, string $message, OutputInterface $output): void
	{
		$this->startTimer(self::COMMAND_RUN_TIMER);
		$output->writeln($message);
		$this->checkLock($lock);
	}

	protected function endExecution(OutputInterface $output): void
	{
		$output->writeln('---');
		$this->printTime(self::COMMAND_RUN_TIMER, $output);
		$output->writeln('DONE');
	}

    /**
     * @return array<mixed>
     */
	protected function processImportBatches(OutputInterface $output): array
	{
		$res = $this->processBatches($output);
		return $res;
	}

    /**
     * @return array<mixed>
     */
	protected function processBatches(OutputInterface $output): array
	{
        Debugger::log("launch", $this->launchLogFolder);
		$batchNumber = 0;
		$this->startTimer(self::COMMAND_BATCH_TIMER);
		$res = [];
		try {
			$res = $this->processBatch($output, $batchNumber);
			$this->orm->clear();
			$output->writeln("\nBatch " . ($batchNumber + 1) . ':');
			$this->writeResult($res, $output);

			$processed = $res['Count'] ?? 0;
			$this->processed += $processed;
		} catch (\Exception $e) {
            Debugger::log($e->getMessage(), $this->errorLogFolder);
			$output->writeln('Exception: ' . $e->getMessage());
		}

		$this->printTime(self::COMMAND_BATCH_TIMER, $output);
		$this->batchLimit = min($this->batchLimit, $this->limit - $this->processed);

		return $res;
	}

	/**
	 * Note that ORM is cleared after each batch.
	 *
	 * @return mixed[] - any report data, should contain 'Count' - number of processed items, when the 'Count' == 0, the loop ends
	 */
	protected function processBatch(OutputInterface $output, int $batchNumber): array
	{
		return ['Count' => 0];
	}

}
