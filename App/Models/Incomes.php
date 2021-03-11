<?php

namespace App\Models;

use \App\Token;
use PDO;

class Incomes extends \Core\Model
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


public static function getUserIncomeCategories()
{
    $sql = 'SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    $incomeCategories = $stmt->fetchAll();

    return $incomeCategories;
    }

    public function save()
    {
        $sql = 'INSERT INTO incomes VALUES (NULL, :user_id, :incomeCategoryAssignedToUserId, :amount, :date, :comment)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':incomeCategoryAssignedToUserId', $this->getIncomeCategoryAssignedToUserId() , PDO::PARAM_INT);
        $stmt->bindValue(':amount', $this->amount , PDO::PARAM_STR);
        $stmt->bindValue(':date', $this->dateOfIncome , PDO::PARAM_STR);
        $stmt->bindValue(':comment', $this->incomeComment , PDO::PARAM_STR);

        return $stmt->execute();

    }

    public function getIncomeCategoryAssignedToUserId()
    {
        $sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $this->incomeCategory , PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);



        return $result['id'];
    }


}