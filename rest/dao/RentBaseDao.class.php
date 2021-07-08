<?php
require_once 'BaseDao.class.php';

class RentBaseDao extends BaseDao
{
  public $table = 'bases';

  public function __construct()
  {
    parent::__construct($this->table);
  }

  public function delete_base($id)
  {
    $query = "DELETE FROM bases WHERE id =:id";
    return $this->execute_query1($query, ['id' => $id]);
  }

  public function update_base($base, $base_id)
  {
    $entity[':id'] = $base_id;
    $query= 'UPDATE '.  $this->table . ' SET ';
    foreach ($base as $key => $value) {
      $query .= $key . '=:' . $key . ', ';
      $entity[':' . $key] = $value;
    }
    $query = rtrim($query,', ') . ' WHERE id=:id';
    return $this->update($entity, $query);
  }
}