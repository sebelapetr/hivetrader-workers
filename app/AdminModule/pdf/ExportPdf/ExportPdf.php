<?php

namespace App\AdminModule\Pdf;


use App\Model\Poster;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use Tracy\Debugger;

class ExportPdf extends BasePdf
{

    protected int $mgt = 20;
    protected int $mgl = 20;
    protected int $mgr = 20;
    protected bool $use_kwt = true;

    public Poster $poster;

    public function setPoster(Poster $poster): void
    {
        $this->poster = $poster;
    }

    /**
     * @return Mpdf
     * @throws \Mpdf\MpdfException
     */
    protected function createMpdf(array $params = null): Mpdf
    {
        $parameters = $this->getBaseParameters();

        $mpdf = new Mpdf($parameters);

        $mpdf->SetCompression(false);

        $mpdf->SetTitle($this->poster->hash);


        $mpdf->showImageErrors = true;

        return $mpdf;
    }

    public function getBaseParameters(): array
    {
        $parameters = [
            'mode' => 'utf-8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'overflow' => 'hidden'
        ];

        $parameters['format'] = [297, 420];

        return $parameters;
    }

    protected function prepareTemplateData($parameters): void
    {
        $this->template->poster = $parameters['poster'];
    }


    public function getValidatedParameters(): array
    {
        return [
            'poster' => Poster::class
        ];
    }

    public function setupTemplate(): void
    {
        /** @var Template $template */
        $template = $this->templateFactory->createTemplate();
        $this->template = $template;
        $reflection = new \ReflectionClass(static::class);
        $className = $reflection->getShortName();

        $this->template->setFile(__DIR__ . "/ExportPdf.latte");
    }

}