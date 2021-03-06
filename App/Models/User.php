<?php

namespace App\Models;

use \App\Token;
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
  public function __construct($data =[])
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
            $this->errors[] = 'Imi?? jest wymagane';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Nieprawid??owy adres e-mail';
        }

        if (static::emailExists($this->email)) {
            $this->errors[] = 'Podany e-mail jest ju?? zaj??ty';
        }

        // Password
        if ($this->password != $this->password_confirmation) {
            $this->errors[] = 'Has??a musz?? by?? takie same';
        }

        if (strlen($this->password) < 6) {
            $this->errors[] = 'Has??o musi sk??ada?? si?? z minimum 6 znak??w';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Has??o musi posiada?? co najmniej jedn?? liter??';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Has??o musi posiada?? co najmniej jedn?? cyfr??';
        }
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */

    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

    /**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }
}
