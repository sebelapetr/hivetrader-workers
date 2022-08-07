<?php
declare(strict_types=1);

namespace App\AdminModule\Pdf;

//include __DIR__.'/../../../vendor/mpdf/mpdf/src/mpdf.php';

use Nette\Http\UrlScript;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use App\Model\Orm;
use Mpdf\Mpdf;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Http\Request;
use Nette\Http\Url;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;

abstract class BasePdf
{

	const EXPORT_AS_DEFAULT = '';
	const EXPORT_AS_MPDF = NULL;
	const EXPORT_AS_DOWNLOAD = 'D';
	const EXPORT_AS_SHOW = 'I';
	const EXPORT_AS_FILE = 'F';
	const EXPORT_AS_STRING = 'S';

	const VALIDATION_PARAMETER_INTEGER = "int";
	const VALIDATION_PARAMETER_BOOLEAN = "boolean";
	const VALIDATION_PARAMETER_STRING = "string";
	const VALIDATION_PARAMETER_ARRAY = "array";

	// PDF settings
	protected string $format = 'A4';
	protected int $default_font_size = 0;
	protected string $default_font = '';
	protected int $mgl = 15;
	protected int $mgr = 15;
	protected int $mgt = 16;
	protected int $mgb = 16;
	protected int $mgh = 9;
	protected int $mgf = 9;
	protected string $orientation = 'P';

	protected Orm $orm;
	protected TemplateFactory $templateFactory;
	private UrlScript $url;
	protected Template $template;

	protected string $layout = '../@layout.latte';
	protected bool $validateParameters = TRUE;
	private bool $wasValidated = FALSE;

	public function __construct(Orm $orm, TemplateFactory $templateFactory, Request $request)
	{
		$this->url = $request->getUrl();
		$this->orm = $orm;
		$this->templateFactory = $templateFactory;
	}

	/**
	 * @return string
	 * @throws \Throwable
	 */
	public function getHtml(): string
	{
		$this->checkWasValidated();
		return $this->template->__toString();
	}

	/**
	 * @param string $name
	 * @param string $exportAs
	 * @return string|null
	 * @throws \Mpdf\MpdfException
	 * @throws \Throwable
	 */
	public function generatePdf(string $name, string $exportAs = '', array $params = null): ?string
	{
		$mpdf = $this->createMpdf($params);
		$mpdf->writeHTML($this->getHtml());
		return $mpdf->Output($name, $exportAs);
	}

	public function setTemplateData(array $params = []): void
	{
        $this->setupTemplate();
		if($this->validateParameters){
			$this->validateParameters($params);
			$this->wasValidated = TRUE;
		}
		$this->prepareTemplateData($params);
		$this->template->basePath = $this->url->getBasePath();
		$this->template->baseUrl = $this->url->getBaseUrl();
	}

	protected abstract function prepareTemplateData(array $params): void;

	public function getValidatedParameters(): array
	{
		return [];
	}

	/**
	 * @return Mpdf
	 * @throws \Mpdf\MpdfException
	 */
	protected function createMpdf(array $params = null): Mpdf
	{
		$mpdf = new Mpdf(['UTF-8', $this->format, $this->default_font_size, $this->default_font, $this->mgl, $this->mgr, $this->mgt, $this->mgb, $this->mgh, $this->mgf, $this->orientation]);

		$mpdf->debug = true;
		$mpdf->showImageErrors = true;

		return $mpdf;
	}

	protected function setupTemplate(): void
	{
		/** @var Template $template */
		$template = $this->templateFactory->createTemplate();
		$this->template = $template;
		$reflection = new \ReflectionClass(static::class);
		$className = $reflection->getShortName();
		$this->template->setFile(__DIR__."/".StringUtils::cutEnd($reflection->getShortName(), "Pdf")."/".$className.".latte");
	}

	private function checkWasValidated(): void
	{
		if(!$this->wasValidated && $this->validateParameters && ($this->getValidatedParameters() != NULL)){
			throw new InvalidStateException("Unprocessed parameters validation! Set basic document values by method setTemplateData(). If you want validation turn-off set \$validateParameters = FALSE");
		}
	}

	private function validateParameters(array $params): void
	{
		foreach($this->getValidatedParameters() as $paramName => $type) {
			if(!array_key_exists($paramName, $params)){
				throw new InvalidStateException("In received \$params missing parameter '".$paramName."'");
			}elseif($type == self::VALIDATION_PARAMETER_ARRAY && !is_array($params[$paramName])){
				throw new InvalidArgumentException("Received parameter '".$paramName."' is ".gettype($params[$paramName]).", array expected.");
			}elseif($type == self::VALIDATION_PARAMETER_INTEGER && !is_int($params[$paramName])){
				throw new InvalidArgumentException("Received parameter '".$paramName."' is ".gettype($params[$paramName]).", integer expected.");
			}elseif($type == self::VALIDATION_PARAMETER_STRING && !is_string($params[$paramName])){
				throw new InvalidArgumentException("Received parameter '".$paramName."' is ".gettype($params[$paramName]).", string expected.");
			}elseif($type == self::VALIDATION_PARAMETER_BOOLEAN && !is_bool($params[$paramName])){
				throw new InvalidArgumentException("Received parameter '".$paramName."' is ".gettype($params[$paramName]).", boolean expected.");
			}elseif(strpos($type, '\\') !== FALSE){
				if(!($params[$paramName] instanceof $type)){
					throw new InvalidArgumentException("Received parameter '".$paramName."' is ".gettype($params[$paramName])." ".$type." expected.");
				}
			}
		}
	}
}