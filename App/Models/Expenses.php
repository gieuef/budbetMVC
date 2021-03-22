<?php

namespace App\Models;

use \App\Token;
use PDO;

class Expenses extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];
    /**
     * Class constructor
     *
     * @param array $data  Initial property values
     *
     * @return void
     */
    public function __construct($data =[])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }


public static function getUserExpenseCategories()
{
    $sql = 'SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = :user_id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    $expenseCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

 

    return $expenseCategories;
    }

    public static function getPaymentMethods()
    {
        $sql = 'SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id';
    
        $db = static::getDB();
        $stmt = $db->prepare($sql);
    
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    
        $paymentMethods = $stmt->fetchAll();
    
        return $paymentMethods;
        }


    public function save()
    {
        $sql = 'INSERT INTO expenses VALUES (NULL, :user_id, :expenseCategoryAssignedToUserId,
            :paymentMethod, :amount, :date, :comment)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':expenseCategoryAssignedToUserId', $this->getExpenseCategoryAssignedToUserId() , PDO::PARAM_INT);
        $stmt->bindValue(':paymentMethod', $this->getPaymentMethodAssignedToUserId() , PDO::PARAM_INT);        
        $stmt->bindValue(':amount', $this->amount , PDO::PARAM_STR);
        $stmt->bindValue(':date', $this->dateOfExpense , PDO::PARAM_STR);
        $stmt->bindValue(':comment', $this->expenseComment , PDO::PARAM_STR);

        return $stmt->execute();

    }

    public function getExpenseCategoryAssignedToUserId()
    {
        $sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $this->expenseCategory , PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);



        return $result['id'];
    }

    public function getPaymentMethodAssignedToUserId()
    {
        $sql = 'SELECT id FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $this->paymentMethod , PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);



        return $result['id'];
    }



}