<?php
require_once 'BaseDao.class.php';

class ReservationDao extends BaseDao
{
  public $table = 'reservations';

  public function __construct()
  {
    parent::__construct($this->table);
  }

  public function delete_reservation($id)
  {
    $query = "DELETE FROM reservations WHERE id =:id";
    return $this->execute_query1($query, ['id' => $id]);
  }

  public function update_status($reservation, $reservation_id)
  {
    $entity[':id'] = $reservation_id;
    $query= 'UPDATE '.  $this->table . ' SET ';
    foreach ($reservation as $key => $value) {
      $query .= $key . '=:' . $key . ', ';
      $entity[':' . $key] = $value;
    }
    $query = rtrim($query,', ') . ' WHERE id=:id';
    return $this->update($entity, $query);
  }
}