<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Pdf\BasePdf;
use App\Model\Order;
use Nette;
use App\AdminModule\Pdf\ExportPdf;
use Tracy\Debugger;


class OrdersPresenter extends BaseAdminPresenter
{
    public Order $order;

    /** @inject */
    public ExportPdf $exportPdf;

    public function actionDefault(): void
    {
    }

    public function renderDefault(): void
    {
        $this->template->orders = $this->orm->orders->findAll();
    }

    public function actionDetail(int $id): void
    {
        $this->order = $this->orm->orders->getById($id);
    }

    public function renderDetail()
    {
        $this->template->order = $this->order;
    }

    public function handleDownloadPdf(int $posterId)
    {
        $poster = $this->orm->posters->getById($posterId);
        $this->exportPdf->setPoster($poster);
        $this->exportPdf->setTemplateData( ['poster' => $poster] );
        $this->exportPdf->generatePdf(  $poster->hash.'.pdf', BasePdf::EXPORT_AS_SHOW, ['poster' => $poster] );
    }

}
