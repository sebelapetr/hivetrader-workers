<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette;
use Tracy\Debugger;


class LoginPresenter extends Nette\Application\UI\Presenter
{
    public function actionDefault(): void
    {

    }

    public function createComponentLoginForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('password', 'Heslo')
            ->setHtmlType('password');
        $form->addSubmit('send', 'Přihlásit se');
        $form->onSuccess[] = [$this, 'login'];
        return $form;
    }

    public function login(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();
        if ($values->password === 'npgadmin1')
        {
            $this->getSession()->getSection('admin')->logedIn = true;
            $this->redirect('Orders:');
        }
    }
}
