<?php
require_once 'BaseDao.class.php';

class UserDao extends BaseDao
{
  public $table = 'users';

  public function __construct()
  {
    parent::__construct($this->table);
  }

  public function get_user_by_email($email)
  {
    $query = "SELECT * FROM users WHERE email=:email";
    return @($this->execute_query($query, ['email' => $email]))[0];
  }

  public function get_user_by_id($id)
  {
    $query = "SELECT * FROM users WHERE id=:id";
    return @($this->execute_query($query, ['id' => $id]))[0];
  }

  public function get_workers()
  {
    $query = "SELECT * FROM users WHERE job != '-'";
    return $this->execute_query($query, []);
  }

  public function delete_user($id)
  {
    $query = "DELETE FROM users WHERE id =:id";
    return $this->execute_query1($query, ['id' => $id]);
  }

  public function get_non_workers()
  {
    $query = "SELECT * FROM users WHERE job LIKE '-'";
    return $this->execute_query($query, []);
  }

  public function delete_non_worker($id)
  {
    $query = "DELETE * FROM users WHERE id =:id";
    return $this->execute_query1($query, ['id' => $id]);
  }

  public function update_user($user, $user_id)
  {
    $entity[':id'] = $user_id;
    $query= 'UPDATE '.  $this->table . ' SET ';
    foreach ($user as $key => $value) {
      $query .= $key . '=:' . $key . ', ';
      $entity[':' . $key] = $value;
    }
    $query = rtrim($query,', ') . ' WHERE id=:id';
    return $this->update($entity, $query);
  }

  public function update_OTP_secret($email, $otp_secret)
  {
      $query = "UPDATE users SET otp_secret = :otp_secret WHERE email=:email";
      return @($this->execute_query1($query, ['email' => $email, 'otp_secret' => $otp_secret]))[0];
  }
  
  public function update_OTP_enabled_to_0($email)
  {
      $query = "UPDATE users SET otp_setup = '0' WHERE email=:email";
      return @($this->execute_query1($query, ['email' => $email]))[0];
  }

  public function update_OTP_enabled_to_1($email)
  {
      $query = "UPDATE users SET otp_setup = '1' WHERE email=:email";
      return @($this->execute_query1($query, ['email' => $email]))[0];
  }

  public function update_password($id, $new_password)
  {
      $query = "UPDATE users SET password = :new_password WHERE id=:id";
      return @($this->execute_query1($query, ['id' => $id, 'new_password' => $new_password]))[0];
  }
}
?>
