<?php declare(strict_types = 1);

namespace App\Console\Foo;

use App\Model\Erp\Exception\LoggedException;
use App\Model\Orm;
use App\Model\Services\ConsoleLogger\ConsoleLoggerService;
use Nextras\Dbal\Utils\DateTimeImmutable;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

abstract class BaseCommand extends Command
{

	use LockableTrait;

	protected Orm $orm;

    public ConsoleLoggerService $consoleLoggerService;

	public const FOO_LOCK = 'foo_lock';

	protected static $defaultName = 'erp:foo';

	protected const COMMAND_RUN_TIMER = 'commandRun';
	protected const COMMAND_BATCH_TIMER = 'commandBatch';

	protected int $limit = PHP_INT_MAX;

	protected int $batchLimit = PHP_INT_MAX;

	protected int $processed = 0;

	protected array $config;

	public function __construct(Orm $orm, ConsoleLoggerService $consoleLoggerService)
	{
		parent::__construct(null);
		$this->orm = $orm;
        $this->consoleLoggerService = $consoleLoggerService;
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

	protected function processImportBatches(OutputInterface $output): array
	{
		$res = $this->processBatches($output);
		return $res;
	}

	protected function processBatches(OutputInterface $output): array
	{
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
            $this->consoleLoggerService->consoleLogger->error($e->getMessage());
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
