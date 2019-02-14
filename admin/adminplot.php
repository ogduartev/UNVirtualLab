<?php
require_once("admin/adminblock.php");
require_once("svgplot.php");

class adminplot extends adminblock
{  

  function createFields()
  {
    $this->varpar="variable";
    $this->blockname=$this->text("adminplot_Plot");
    $this->table="plots";
    $this->sectionidname="plotid";
    $this->idname="id";
    $this->idvalue=$this->plot_id();
    $this->fields=array();
    $this->fields[]=array("dbname"=>"id",
                          "showname"=>$this->text("adminplot_Identifier"),
                          "type"=>"fixed");
    $this->fields[]=array("dbname"=>"model_id",
                          "showname"=>$this->text("adminplot_Model_identifier"),
                          "type"=>"model link");
    $this->fields[]=array("dbname"=>"name",
                          "showname"=>$this->text("adminplot_Plot_name"),
                          "type"=>"text");
    $this->fields[]=array("dbname"=>"description",
                          "showname"=>$this->text("adminplot_Description"),
                          "type"=>"longtext");
    $this->fields[]=array("dbname"=>"enabled",
                          "showname"=>$this->text("adminplot_Enabled"),
                          "type"=>"bool");
    $this->fields[]=array("dbname"=>"variable_id",
                          "showname"=>$this->text("adminplot_Horizontal_variable"),
                          "model_id"=>$this->findModelId(),
                          "type"=>"select variable");
    $this->fields[]=array("dbname"=>"minX",
                          "showname"=>$this->text("adminplot_Minimum_horizontal_value"),
                          "type"=>"float");
    $this->fields[]=array("dbname"=>"maxX",
                          "showname"=>$this->text("adminplot_Maximum_horizontal_value"),
                          "type"=>"float");
    $this->fields[]=array("dbname"=>"gridX",
                          "showname"=>$this->text("adminplot_Horizontal_divisions"),
                          "type"=>"int");
    $this->fields[]=array("dbname"=>"autoscaleX",
                          "showname"=>$this->text("adminplot_Horizontal_autoscale"),
                          "value"=>$this->findAutoscale("X"),
                          "type"=>"bool");
    $this->fields[]=array("dbname"=>"minY",
                          "showname"=>$this->text("adminplot_Minimum_vertical_value"),
                          "type"=>"float");
    $this->fields[]=array("dbname"=>"maxY",
                          "showname"=>$this->text("adminplot_Maximum_vertical_value"),
                          "type"=>"float");
    $this->fields[]=array("dbname"=>"gridY",
                          "showname"=>$this->text("adminplot_Vertical_divisions"),
                          "type"=>"int");
    $this->fields[]=array("dbname"=>"autoscaleY",
                          "showname"=>$this->text("adminplot_Vertical_autoscale"),
                          "value"=>$this->findAutoscale("Y"),
                          "type"=>"bool");
    $this->fields[]=array("dbname"=>"firstdata",
                          "showname"=>$this->text("adminplot_First_data"),
                          "type"=>"int");
  }  
   
  function createRelations1N1N()
  {
    $this->Relations1N1N=array();
    $R=array(
             "title"=>$this->text("adminplot_Curves"),
             "table"=>"curves",
             "linkname"=>"curveid",
             "id1"=>"id",
             "id2"=>"plot_id",
             "idvalue"=>$this->plot_id(),
             "showdbname"=>"name",
             "show"=>$this->text("adminplot_Curve_name"),
             "subfields"=>array());
             
    $R["subfields"][]=array("dbname"=>"id",
                          "showname"=>$this->text("adminplot_Identifier"),
                          "type"=>"fixed");
    $R["subfields"][]=array("dbname"=>"name",
                          "showname"=>$this->text("adminplot_Curve_name"),
                          "type"=>"text");
    $R["subfields"][]=array("dbname"=>"legend",
                          "showname"=>$this->text("adminplot_Legend"),
                          "type"=>"text");
    $R["subfields"][]=array("dbname"=>"enabled",
                          "showname"=>$this->text("adminplot_Enabled"),
                          "type"=>"bool");
    $R["subfields"][]=array("dbname"=>"description",
                          "showname"=>$this->text("adminplot_Description"),
                          "type"=>"longtext");
    $R["subfields"]['variable_id']=array("dbname"=>"variable_id",
                          "showname"=>$this->text("adminplot_Vertical_variable"),
                          "model_id"=>$this->findModelId(),
                          "type"=>"select variable");
    $R["subfields"][]=array("dbname"=>"colorRGB",
                          "showname"=>$this->text("adminplot_Color"),
                          "type"=>"color");
    $this->Relations1N1N[]=$R;
  }  
  
  function findModelId()
  {
    $sql="SELECT model_id FROM plots WHERE id=".$this->plot_id();
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      return($linea['model_id']);
    }
    return 0;
  }
  
  function findAutoscale($axis)
  {
    if(isset($_POST['db_autoscale'.$axis]))
    {
      return $_POST['db_autoscale'.$axis];
    }
    $sql="SELECT autoscale".$axis." AS A FROM plots WHERE id=".$this->plot_id();
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      return($linea['A']);
    }
    return 0;
  }
  
  function displayPostOwn()
  {
    $this->displayPlot();
  }
  
  function displayPlot()
  {  
    $sql="SELECT plots.*,variables.modelicaname AS MN,models.exename FROM plots 
                      INNER JOIN variables ON plots.variable_id=variables.id
                      INNER JOIN models ON plots.model_id=models.id 
                      WHERE plots.id='".$_POST['plotid']."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      $graph = new svgplot();
      $graph->connect();
      if($graph->link)
      {
        $graph->settings($linea);
        $graph->data($linea,"files/".$linea['model_id']."/bin/".$linea['exename']."_res.csv");
        echo "<div style=\"text-align:center;\">\n";
        $graph->display(); 
        echo "</div>\n";
        $graph->disconnect();
        $fsvg=fopen("files/".$linea['model_id']."/graphics/expPlot_".$linea['id'].".svg","w");
        fwrite($fsvg,"<?xml-stylesheet type=\"text/css\" href=\"themes/".$this->configurationSettings['theme']."/styleSVG.css\" ?> \n");
        fwrite($fsvg,$graph->svgStr);
        fclose($fsvg);
      }
    }
    $dir="files/".$linea['model_id']."/graphics/";
    $orden= "cd ".$dir."; tar -czf graphics.tar.gz *.svg ";
    passthru($orden);
  }  
  
  function update()
  {
    if(isset($_POST['update']))
    {
      $DB=new databasemanager();
      if(isset($_POST['curveid']))
      {
        $DB->update("curves","id",$_POST['curveid']);
      }else
      {
        $DB->update($this->table,$this->idname,$this->idvalue);
      }
    }
  }
    
}

?>
