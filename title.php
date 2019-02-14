<?php
require_once('block.php');

class title extends block
{  
  function display()
  {
    $model_id=$this->model_id();
    if($model_id==0)
    {
      return;
    }
    $sql="SELECT name,title FROM models WHERE id='".$model_id."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error().": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      echo $linea['title'];
    }
  }
}
?>
