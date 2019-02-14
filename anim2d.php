<?php
require_once('block.php');
require_once('svganim2d.php');
require_once('csvreader.php');

class anim2d extends block
{

  function displayAnim2d($animation2d)
  {
    $graph = new animation2dSVG();
    $graph->connect();
    if($graph->link)
    {
      $graph->animateObject($animation2d,$_POST['res_file']);
      echo "<div class='animation' title='".$animation2d['description']."'>\n";
      echo "  <div class='animationTitle'>".$animation2d['name']."</div>\n";
      echo $graph->writeXmlString();
      echo "</div>\n";
      $graph->disconnect();
    }
  }

  function display()
  {
    $sql="SELECT * FROM 2danimations WHERE model_id='".$_POST['modelid']."'";
    $sql.=$this->enabled("2danimations");
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $this->displayAnim2d($linea);
      }
    }
  }

}

?>


