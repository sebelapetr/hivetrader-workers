<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\Orm;
use Nette;
use Tracy\Debugger;


class BaseAdminPresenter extends Nette\Application\UI\Presenter
{

    /** @inject  */
    public Orm $orm;

    public function startup()
    {
        parent::startup();
        if (!$this->getSession()->getSection('admin')->logedIn)
            $this->redirect('Login:');
    }
}
