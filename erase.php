<?php
require_once('block.php');

class erase extends block
{
  function display()
  {
    $tmp_file=str_replace("_res.csv","",$_POST['res_file']);
    unlink($tmp_file);
    unlink($_POST['res_file']);
    unset($_POST['res_file']);
  }
}


?>


