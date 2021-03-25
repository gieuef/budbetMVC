<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Flash;
use App\Models\Incomes;
use App\Models\Balances;

class Balance extends Authenticated
{

    public function currentMonthAction()
    {

        $currentMonth = new Balances();
        $currentMonth->getDataOfCurrentMonth();

        View::renderTemplate('Balance/show-balance.html',[
            'incomesByCategory' => $currentMonth->getIncomesByCategory(),
            'expensesByCategory' => $currentMonth->getExpensesByCategory(),
            'detailedIncomesByCategory' => $currentMonth-> getDetailedIncomesByCategory(),
            'detailedExpensesByCategory' => $currentMonth-> getDetailedExpensesByCategory(),
            'balance' => $currentMonth->getBalance()
        ]);
    }

    public function previousMonthAction()
    {

        $previousMonth = new Balances();
        $previousMonth->getDataOfPreviousMonth();

        View::renderTemplate('Balance/show-balance.html',[
            'incomesByCategory' => $previousMonth->getIncomesByCategory(),
            'expensesByCategory' => $previousMonth->getExpensesByCategory(),
            'detailedIncomesByCategory' => $previousMonth-> getDetailedIncomesByCategory(),
            'detailedExpensesByCategory' => $previousMonth-> getDetailedExpensesByCategory(),
            'balance' => $previousMonth->getBalance()
        ]);
    }

    public function currentYearAction()
    {

        $currentYear = new Balances();
        $currentYear->getDataOfCurrentYear();

        View::renderTemplate('Balance/show-balance.html',[
            'incomesByCategory' => $currentYear->getIncomesByCategory(),
            'expensesByCategory' => $currentYear->getExpensesByCategory(),
            'detailedIncomesByCategory' => $currentYear-> getDetailedIncomesByCategory(),
            'detailedExpensesByCategory' => $currentYear-> getDetailedExpensesByCategory(),
            'balance' => $currentYear->getBalance()
        ]);
    }



    public function periodDates()
    {
        $periodDates = new Balances($_POST);
        $periodDates->getDataOfPeriodDates();

        View::renderTemplate('Balance/show-balance.html',[
            'incomesByCategory' => $periodDates->getIncomesByCategory(),
            'expensesByCategory' => $periodDates->getExpensesByCategory(),
            'detailedIncomesByCategory' => $periodDates-> getDetailedIncomesByCategory(),
            'detailedExpensesByCategory' => $periodDates-> getDetailedExpensesByCategory(),
            'balance' => $periodDates->getBalance()
        ]);



    }

}
