<?php
require_once 'BaseDao.class.php';

class CarDao extends BaseDao
{
  public $table = 'cars';

  public function __construct()
  {
    parent::__construct($this->table);
  }

  public function get_car_info()
  {
    $query = "SELECT cars.*, bases.name base_name,bases.location, bases.phone_number FROM cars JOIN bases WHERE cars.id_base=bases.id";
    return $this->execute_query($query,[]);
  }

  public function update_availability_to_NO($id)
  {
    $query = "UPDATE cars SET availability='NO' WHERE id = :id";
    return @($this->execute_query1($query,["id" => $id]))[0];
  }

  public function update_availability_to_YES($id)
  {
    $query = "UPDATE cars SET availability='YES' WHERE id = :id";
    return @($this->execute_query1($query,["id" => $id]))[0];
  }

  public function get_car_by_id($id)
  {
    $query = "SELECT * FROM cars WHERE id=:id";
    return @($this->execute_query($query, ['id' => $id]))[0];
  }

  public function update_car($car, $car_id)
  {
    $entity[':id'] = $car_id;
    $query= 'UPDATE '.  $this->table . ' SET ';
    foreach ($car as $key => $value) {
      $query .= $key . '=:' . $key . ', ';
      $entity[':' . $key] = $value;
    }
    $query = rtrim($query,', ') . ' WHERE id=:id';
    return $this->update($entity, $query);
  }

  public function delete_car($id)
  {
    $query = "DELETE FROM cars WHERE id =:id";
    return $this->execute_query1($query, ['id' => $id]);
  }
}