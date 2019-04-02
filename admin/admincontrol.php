<?php
require_once("admin/adminblock.php");

class admincontrol extends adminblock
{  

  function createFields()
  {
    $this->varpar="parameter";
    $this->blockname=$this->text("admincontrol_Control_group");
    $this->table="control_groups";
    $this->sectionidname="controlgroupid";
    $this->idname="id";
    $this->idvalue=$this->control_group_id();
    $this->fields=array();
    $this->fields[]=array("dbname"=>"id",
                          "showname"=>$this->text("admincontrol_Identifier"),
                          "type"=>"fixed");
    $this->fields[]=array("dbname"=>"model_id",
                          "showname"=>$this->text("admincontrol_Model_identifier"),
                          "type"=>"model link");
    $this->fields[]=array("dbname"=>"name",
                          "showname"=>$this->text("admincontrol_Name"),
                          "type"=>"text");
    $this->fields[]=array("dbname"=>"description",
                          "showname"=>$this->text("admincontrol_Description"),
                          "type"=>"longtext");
    $this->fields[]=array("dbname"=>"enabled",
                          "showname"=>$this->text("admincontrol_Enabled"),
                          "type"=>"bool");
  }  
  
  function createRelations1N1N()
  {
    $this->Relations1N1N=array();
    $R=array(
             "title"=>$this->text("admincontrol_Controls"),
             "table"=>"controls",
             "linkname"=>"controlid",
             "id1"=>"id",
             "id2"=>"control_group_id",
             "idvalue"=>$this->control_group_id(),
             "showdbname"=>"name",
             "show"=>$this->text("admincontrol_Control_name"),
             "subfields"=>array());
             
    $R["subfields"][]=array("dbname"=>"id",
                          "showname"=>$this->text("admincontrol_Identifier"),
                          "type"=>"fixed");
    $R["subfields"][]=array("dbname"=>"name",
                          "showname"=>$this->text("admincontrol_Name"),
                          "type"=>"text");
    $R["subfields"][]=array("dbname"=>"value",
                          "showname"=>$this->text("admincontrol_Value"),
                          "type"=>"text");
    $R["subfields"][]=array("dbname"=>"enabled",
                          "showname"=>$this->text("admincontrol_Enabled"),
                          "type"=>"bool");
    $R["subfields"][]=array("dbname"=>"min",
                          "showname"=>$this->text("admincontrol_Minimum"),
                          "type"=>"text");
    $R["subfields"][]=array("dbname"=>"max",
                          "showname"=>$this->text("admincontrol_Maximum"),
                          "type"=>"text");
    $R["subfields"][]=array("dbname"=>"step",
                          "showname"=>$this->text("admincontrol_Step"),
                          "type"=>"text");
    $R["subfields"][]=array("dbname"=>"allowedvalues",
                          "showname"=>$this->text("admincontrol_Allowed_values"),
                          "type"=>"longtext");
    $R["subfields"][]=array("dbname"=>"description",
                          "showname"=>$this->text("admincontrol_Description"),
                          "type"=>"longtext");
    $R["subfields"]['parameter_id']=array("dbname"=>"parameter_id",
                          "showname"=>$this->text("admincontrol_Parameter"),
                          "model_id"=>$this->findModelId(),
                          "type"=>"select parameter");
    $this->Relations1N1N[]=$R;
  }  

  function findModelId()
  {
    $sql="SELECT model_id FROM control_groups WHERE id=".$this->idvalue;
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      return($linea['model_id']);
    }
    return 0;
  }
  
  function update()
  {
    if(isset($_POST['update']))
    {
      $DB=new databasemanager();
      if(isset($_POST['controlid']))
      {
        $DB->update("controls","id",$_POST['controlid']);
      }else
      {
        $DB->update($this->table,$this->idname,$this->idvalue);
      }
    }
  }
    
  
}
?>
