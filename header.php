<?php
require_once('block.php');

class header extends block
{  
  function display()
  {
    $fHtml="themes/".$this->configurationSettings['theme']."/header.html";
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
