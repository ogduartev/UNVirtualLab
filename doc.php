<?php
require_once('block.php');
require_once('menu.php');
require_once('title.php');
require_once('credits.php');
require_once('documentation.php');
require_once('controls.php');
require_once('simulator.php');
require_once('outputs.php');
require_once('plots.php');
require_once('anim2d.php');
require_once('tables.php');
require_once('erase.php');

$B=new block();
if($B->connect())
{
  $xmlFN="page_structure/doc.xml";

  if(isset($_POST['simulate']))
  {
    $model_id=$B->model_id();
  }

  $B->html($xmlFN);
}
?>
