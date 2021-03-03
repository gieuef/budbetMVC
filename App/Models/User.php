<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
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
  public function __construct($data)
  {
    foreach ($data as $key => $value) {
      $this->$key = $value;
    };
  }

  /**
   * Save the user model with the current property values
   *
   * @return void
   */
  public function save()
  {
      $this->validate();

      if (empty($this->errors)) {

          $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

          $sql = 'INSERT INTO users (username, password, email)
            VALUES (:name, :password_hash, :email)';

          $db = static::getDB();
          $stmt = $db->prepare($sql);

          $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
          $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
          $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

          $stmt->execute();

          $last_id = $db->lastInsertId();

          $sql = 'INSERT INTO payment_methods_assigned_to_users (`user_id`,`name`) 
                                    SELECT :user_id,name FROM payment_methods_default';

          $stmt = $db->prepare($sql);
          $stmt->bindValue(':user_id', $last_id, PDO::PARAM_INT);
          $stmt->execute();


          $sql = 'INSERT INTO expenses_category_assigned_to_users (`user_id`,`name`) 
                                    SELECT :user_id,name FROM expenses_category_default';

          $stmt = $db->prepare($sql);
          $stmt->bindValue(':user_id', $last_id, PDO::PARAM_INT);
          $stmt->execute();


          $sql = 'INSERT INTO incomes_category_assigned_to_users (`user_id`,`name`) 
                                    SELECT :user_id,name FROM incomes_category_default';
          $stmt = $db->prepare($sql);
          $stmt->bindValue(':user_id', $last_id, PDO::PARAM_INT);

          return $stmt->execute();
      }

      return false;

  }


    public function validate()
    {
        // Name
        if ($this->name == '') {
            $this->errors[] = 'Imię jest wymagane';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Nieprawidłowy adres e-mail';
        }
        if ($this->emailExists($this->email)) {
            $this->errors[] = 'Podany e-mail jest już zajęty';
        }

        // Password
        if ($this->password != $this->password_confirmation) {
            $this->errors[] = 'Hasła muszą być takie same';
        }

        if (strlen($this->password) < 6) {
            $this->errors[] = 'Hasło musi składać się z minimum 6 znaków';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło musi posiadać co najmniej jedną literę';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło musi posiadać co najmniej jedną cyfrę';
        }
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    protected function emailExists($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch() !== false;
    }
}
