<?php
require_once('block.php');

class logo extends block
{  
  function display()
  {
    echo "<a href='info.php' target='_blank' class='logo'></a>\n";
  }
}

?>
