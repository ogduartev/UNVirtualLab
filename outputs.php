<?php
require_once('block.php');

class outputs extends block
{  
  function display()
  {
    $model_id=$this->model_id();
    $_POST['modelid']=$model_id;
    $S=new simulator();
    $S->connect();
    if($S->link)
    {
      $S->simulate($model_id);
      $S->disconnect();
    }
  }
}

?>
