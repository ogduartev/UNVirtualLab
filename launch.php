<?php
require_once('block.php');

class launch extends block
{  
  function display()
  {
    $model_id=$this->model_id();
    if($model_id==0)
    {
      return;
    }
    echo "<script type=\"text/javascript\">function launch(modelid)\n";
    echo "{";
    echo "  window.open('alone.php?modelid='+modelid,'_blank');";
    echo "}";
    echo "</script>";
    echo "<a onclick='launch(\"".$model_id."\")'><img src='themes/".$this->configurationSettings['theme']."/img/launch.gif'></a>";
  }
}
?>


