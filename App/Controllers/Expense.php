<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Flash;
use App\Models\Expenses;

class Expense extends Authenticated
{

    public function indexAction()
    {
        View::renderTemplate('Expense/index.html',[
            'userExpenseCategories' => Expenses::getUserExpenseCategories(),
            'userPaymentMethods' => Expenses::getPaymentMethods()
        ]);
    }

    public function createAction()
    {
        $expense = new Expenses($_POST);

        if ($expense->save()){

            Flash::addMessage('Wydatek został dodany');

            $this->redirect('/expense/index');

        } 
        else {

            Flash::addMessage('Wydatek nie został dodany. Spróbuj ponownie', Flash::WARNING);

            $this->redirect('/expense/index');

        }

    }

}