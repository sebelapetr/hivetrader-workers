<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\Orm;
use Nette;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @inject */
    public Orm $orm;

    public function startup()
    {
        parent::startup();
    }

    public function beforeRender()
    {
        parent::beforeRender();
    }
}
