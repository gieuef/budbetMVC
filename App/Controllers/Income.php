<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Flash;
use App\Models\Incomes;

class Income extends Authenticated
{



    public function indexAction()
    {
        View::renderTemplate('Income/index.html',[
            'userIncomeCategories' => Incomes::getUserIncomeCategories()
        ]);
    }

    public function createAction()
    {
        $income = new Incomes($_POST);

        if ($income->save()){

            Flash::addMessage('Przychód został dodany');

            $this->redirect('/income/index');

        }

    }


}
