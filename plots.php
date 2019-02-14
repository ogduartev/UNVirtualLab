<?php
require_once('block.php');
require_once('svgplot.php');
require_once('csvreader.php');

class plots extends block
{

  function displayPlot($plot)
  {  
    $graph = new SVGplot();
    $graph->connect();
    if($graph->link)
    {
      $graph->settings($plot);
      $graph->data($plot,$_POST['res_file']);
      $graph->display();
      $graph->disconnect();
    }
  }

  function display()
  {
    $sql="SELECT plots.*,variables.modelicaname AS MN FROM plots 
                         INNER JOIN variables ON plots.variable_id=variables.id 
                         WHERE plots.model_id='".$_POST['modelid']."'";
    $sql.=$this->enabled("plots");
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $this->displayPlot($linea);
      }
    }
  }

}

?>


