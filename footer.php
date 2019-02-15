<?php
require_once('block.php');

class footer extends block
{  
  function display()
  {
    $fHtml=$this->configurationSettings['unvlDir']."/themes/".$this->configurationSettings['theme']."/footer.html";
    if(file_exists($fHtml))
    {
       $f=file($fHtml);
       foreach($f as $linea)
       {
         $linea=str_replace("href=\"images","href=\"themes/".$this->configurationSettings['theme']."/images",$linea);
         $linea=str_replace("src=\"images" ,"src=\"themes/".$this->configurationSettings['theme']."/images",$linea);
         echo $linea;
       }
    }
  }
}

?>
