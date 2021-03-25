<?php

namespace App\Models;

use \App\Token;
use PDO;
use \DateTime;


class Balances extends \Core\Model
{



    public function getDataOfCurrentMonth()
    {
        $this->beginDate = static::getBeginDateOfCurrentMonth();
        $this->endDate = static::getEndDateOfCurrentMonth();
    }


    public function getDataOfPreviousMonth()
    {
        $this->beginDate = static::getBeginDateOfPreviousMonth();
        $this->endDate = static::getEndDateOfPreviousMonth();

    }


    public function getDataOfCurrentYear()
    {
        $this->beginDate = static::getBeginDateOfCurrentYearh();
        $this->endDate = static::getEndDateOfCurrentYear();
    }
    

    public function getDataOfPeriodDates()
    {
        $this->beginDate = $_POST['beginDate'];
        $this->endDate = $_POST['endDate'];
    }



    public function getExpensesByCategory()
    {

        $sql = "SELECT expenses_category_assigned_to_users.name, SUM(amount) AS sumOfCategory 
        FROM expenses, expenses_category_assigned_to_users 
        WHERE expenses.user_id=:user_id AND expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id 
        AND date_of_expense BETWEEN '{$this->beginDate}' AND '{$this->endDate}'
        GROUP BY name ORDER BY sumOfCategory DESC";
    
        $db = static::getDB();
        $stmt = $db->prepare($sql);
    
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    
        $this -> expensesByCategory = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $this -> expensesByCategory;

    }

    public function getIncomesByCategory()
    {

        $sql = "SELECT incomes_category_assigned_to_users.name, SUM(amount) AS sumOfCategory 
        FROM incomes, incomes_category_assigned_to_users 
        WHERE incomes.user_id=:user_id AND income_category_assigned_to_user_id = incomes_category_assigned_to_users.id 
        AND date_of_income BETWEEN '{$this->beginDate}' AND '{$this->endDate}'
        GROUP BY name ORDER BY sumOfCategory DESC";
    
        $db = static::getDB();
        $stmt = $db->prepare($sql);
    
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    
        $this -> incomesByCategory = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $this ->incomesByCategory;

    }



    public function getDetailedIncomesByCategory()
    {


        $sql = "SELECT ica.name, i.amount, i.date_of_income, i.income_comment 
        FROM incomes AS i, incomes_category_assigned_to_users AS ica
        WHERE i.id=:user_id AND i.income_category_assigned_to_user_id = ica.id 
        AND date_of_income BETWEEN '{$this->beginDate}' AND '{$this->endDate}'
        ORDER BY amount DESC";
    
        $db = static::getDB();
        $stmt = $db->prepare($sql);
    
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    
        $this->detailedIncomesByCategory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->detailedIncomesByCategory;

    }

    public function getDetailedExpensesByCategory()
    {

        $sql = "SELECT eca.name, pay.name AS payMethod, e.amount, e.date_of_expense, e.expense_comment 
        FROM expenses AS e, expenses_category_assigned_to_users AS eca, payment_methods_assigned_to_users as pay
        WHERE e.user_id=:user_id AND e.expense_category_assigned_to_user_id = eca.id AND e.payment_method_assigned_to_user_id = pay.id
        AND date_of_expense BETWEEN '{$this->beginDate}' AND '{$this->endDate}'
        ORDER BY amount DESC";
    
        $db = static::getDB();
        $stmt = $db->prepare($sql);
    
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    
        $this->detailedExpensesByCategory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->detailedExpensesByCategory;
    }



        public static function getBeginDateOfCurrentMonth()
        {
            $beginDate = new DateTime('first day of this month');
            
            return $beginDate->format('Y-m-d');
        }

        public static function getBeginDateOfPreviousMonth()
        {
            $beginDate = new DateTime('first day of previous month');
            
            return $beginDate->format('Y-m-d');
        }

        public static function getEndDateOfCurrentMonth()
        {
            $endDate = new DateTime();
            
            return $endDate->format('Y-m-d');
        }

        public static function getEndDateOfPreviousMonth()
        {
            $endDate = new DateTime('last day of previous month');
            
            return $endDate->format('Y-m-d');
        }


        public static function getBeginDateOfCurrentYear()
        {
            $beginDate = new DateTime('first day of this year');
            
            return $beginDate->format('Y-m-d');
        }


        public static function getEndDateOfCurrentYear()
        {
            $endDate = new DateTime();
            
            return $endDate->format('Y-m-d');
        }

        public function getSumOfIncomes()
        {

            $this->sumOfIncomes = 0;

            foreach ( $this->incomesByCategory as $income)
            {
                $this->sumOfIncomes += $income['sumOfCategory'];
            }


            return $this->sumOfIncomes;
        }

        public function getSumOfExpenses()
        {

            $this->sumOfExpenses = 0;

            foreach ( $this->expensesByCategory as $expense)
            {
                $this->sumOfExpenses += $expense['sumOfCategory'];
            }

            return $this->sumOfExpenses;
        }

        public function getBalance()
        {
            return $this->getSumOfIncomes() - $this->getSumOfExpenses();
        }



    }