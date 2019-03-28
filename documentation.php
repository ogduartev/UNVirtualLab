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
      echo "  <div class='messagenomodel'>\n";
      echo $this->text("description_Select_an_experimentation_plant")."\n";
      echo "  </div>\n";
    }
  }
  
  function displayMessage()
  {
      echo "    <video controls=\"controls\" src=\"info/inicio.ogv\" type=\"video/ogg\" class=\"unvlvideo\" autoplay>\n";
      echo $this->text("description_Select_an_experimentation_plant")."\n";
      echo "    </video>\n";
  }
}
?>
