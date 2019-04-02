<?php
require_once("admin/adminblock.php");
require_once('svganim2d.php');

class admin2deffect extends adminblock
{  

  function createFields()
  {
    $this->varpar="variable";
    $this->blockname=$this->text("admin2deffect_2D_effect");
    $this->table="2deffects";
    $this->sectionidname="2deffectid";
    $this->idname="id";
    $this->idvalue=$this->c2deffect_id();
    $this->fields=array();
    $this->fields[]=array("dbname"=>"id",
                          "showname"=>$this->text("admin2deffect_Identifier"),
                          "type"=>"fixed");
    $this->fields[]=array("dbname"=>"2danimation_id",
                          "showname"=>$this->text("admin2deffect_Animation_identifier"),
                          "type"=>"fixed");
    $this->fields[]=array("dbname"=>"name",
                          "showname"=>$this->text("admin2deffect_Name"),
                          "type"=>"text");
    $this->fields[]=array("dbname"=>"description",
                          "showname"=>$this->text("admin2deffect_Description"),
                          "type"=>"longtext");
    $this->fields[]=array("dbname"=>"enabled",
                          "showname"=>$this->text("admin2deffect_Enabled"),
                          "type"=>"bool");
    $this->fields[]=array("dbname"=>"type",
                          "showname"=>$this->text("admin2deffect_Type"),
                          "options"=>array("single"=>"single","double"=>"double","path"=>"path","color"=>"color"), //"text"=>"text",
                          "table"=>"2deffects",
                          "type"=>"select options");
    $this->fields[]=array("dbname"=>"svganimation_id",
                          "showname"=>$this->text("admin2deffect_SVG_animation_id"),
                          "type"=>"text");
    $this->fields[]=array("dbname"=>"variable_id",
                          "showname"=>$this->text("admin2deffect_Control_variable"),
                          "model_id"=>$this->findModelId(),
                          "type"=>"select variable");
    $this->fields[]=array("dbname"=>"variable_aux_id",
                          "showname"=>$this->text("admin2deffect_Auxiliar_variable"),
                          "model_id"=>$this->findModelId(),
                          "type"=>"select variable");
    $this->fields[]=array("dbname"=>"offset",
                          "showname"=>$this->text("admin2deffect_Offset"),
                          "type"=>"float");
    $this->fields[]=array("dbname"=>"scale",
                          "showname"=>$this->text("admin2deffect_Scale"),
                          "type"=>"float");
    $this->fields[]=array("dbname"=>"colorRGBmin",
                          "showname"=>$this->text("admin2deffect_RGB_minimum_color"),
                          "type"=>"color");
    $this->fields[]=array("dbname"=>"colorRGBmax",
                          "showname"=>$this->text("admin2deffect_RGB_maximum_color"),
                          "type"=>"color");
    $this->fields[]=array("dbname"=>"colorMin",
                          "showname"=>$this->text("admin2deffect_Minimum_color_value"),
                          "type"=>"float");
    $this->fields[]=array("dbname"=>"colorMax",
                          "showname"=>$this->text("admin2deffect_Maximum_color_value"),
                          "type"=>"float");
  }  
  
  function createRelations1N()
  {
    $this->Relations1N=array();
  }  
  
  function createRelations1N1N()
  {
    $this->Relations1N1N=array();
  }  
    
  function findModelId()
  {
    $sql="SELECT model_id FROM 2danimations 
                    INNER JOIN 2deffects ON 2deffects.2danimation_id=2danimations.id 
                    WHERE 2deffects.id=".$this->c2deffect_id();
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      return($linea['model_id']);
    }
    return 0;
  }
}

?>
