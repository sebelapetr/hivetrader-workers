<?php declare(strict_types = 1);

namespace App\Console\Foo;

use App\Model\Orm;
use App\Model\Services\ConsoleLogger\ConsoleLoggerService;
use Exception;
use Nette\DI\Container;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FooCommand extends BaseCommand
{

	public function __construct(Orm $orm, Container $container)
	{
        $consoleLoggerService = $container->getByName('consoleLogger');
		parent::__construct($orm, $consoleLoggerService);
	}

	protected function configure(): void
	{
		$this->setName('foo:foo');
		$this->setDescription('Foo');
	}

	/**
	 * @throws Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->beginExecution(self::FOO_LOCK, 'Foo', $output);

		$this->processImportBatches($output);

		$this->endExecution($output);
		return 0;
	}

	protected function processBatch(OutputInterface $output, int $batchNumber): array
	{
        $a = $b;
		return ["foo"];
	}

}
