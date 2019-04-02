<?php
require_once('block.php');

class credits extends block
{  
  function display()
  {
    $model_id=$this->model_id();
    if($model_id==0)
    {
      return;
    }
    echo "<script type=\"text/javascript\">function about(modelid)\n";
    echo "{";
    echo "  window.open('about.php?modelid='+modelid,'','scrollbars=yes,menubar=no,height=".$this->configurationSettings['aboutHeight'].",width=".$this->configurationSettings['aboutWidth'].",resizable=yes,toolbar=no,location=no,status=no');";
    echo "}";
    echo "</script>";
    echo "<a onclick='about(\"".$model_id."\")'><div class='credits'></div></a>";
  }
}
?>


