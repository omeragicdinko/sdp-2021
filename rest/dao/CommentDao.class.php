<?php
require_once 'BaseDao.class.php';

class CommentDao extends BaseDao
{
  public $table = 'comments';

  public function __construct()
  {
    parent::__construct($this->table);
  }

  public function get_comments()
  {
    $query = "SELECT * FROM comments order by id DESC";
    return $this->execute_query($query,[]);
  }
}