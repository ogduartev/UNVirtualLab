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
         echo $linea;
       }
    }
  }
}

?>
