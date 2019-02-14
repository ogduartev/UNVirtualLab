<?php
require_once('block.php');

class documentation extends block
{  
  function display()
  {
    $model_id=$this->model_id();
    if($model_id==0)
    { 
      $this->displayMessage();
      return;
    }
    $fn="files/".$model_id."/doc/documentation.pdf";
    if(file_exists($fn))
    {
      echo "  <object class='documentation' data='".$fn."#view=fith&navpanes=0' type='application/pdf'>\n";
      echo "  </object>\n";
    }else
    {
      echo $this->text("description_Documentation_file_not_found")."\n";
    }
  }
  
  function displayMessage()
  {
    echo "  <div class='messagenomodel'>\n";
    echo $this->text("description_Select_an_experimentation_plant")."\n";
    echo "  </div>\n";
  }
}
/*
$M=new documentation();
if($M->connect())
{
  $M->display();
}else
{
  echo "error en la conexión\n";
}
*/
?>
