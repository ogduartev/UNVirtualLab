<?php
require_once("admin/adminblock.php");

class adminmodel extends adminblock
{  

  function createFields()
  {
    $this->blockname=$this->text("adminmodel_Model");
    $this->table="models";
    $this->sectionidname="modelid";
    $this->idname="id";
    $this->idvalue=$this->model_id();
    $this->fields=array();
    $this->fields[]=array("dbname"=>"id",
                          "showname"=>$this->text("adminmodel_Identifier"),
                          "type"=>"fixed");
    $this->fields[]=array("dbname"=>"exename",
                          "showname"=>$this->text("adminmodel_Executable_name"),
                          "type"=>"fixed");
    $this->fields[]=array("dbname"=>"name",
                          "showname"=>$this->text("adminmodel_Model_name"),
                          "type"=>"text");
    $this->fields[]=array("dbname"=>"title",
                          "showname"=>$this->text("adminmodel_Model_title"),
                          "type"=>"text");
    $this->fields[]=array("dbname"=>"description",
                          "showname"=>$this->text("adminmodel_Description"),
                          "type"=>"longtext");
    $this->fields[]=array("dbname"=>"enabled",
                          "showname"=>$this->text("adminmodel_Enabled"),
                          "type"=>"bool");
    $this->fields[]=array("dbname"=>"section_id",
                          "showname"=>$this->text("adminsection_Father_section"),
                          "type"=>"select section");
  }  
  
  function createRelations1N()
  {
    $this->Relations1N=array();

    $sql="SELECT id FROM variables WHERE model_id='".$this->model_id()."' LIMIT 1";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error().": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);  
      $R=array(
             "title"=>$this->text("adminmodel_Plots"),
             "table"=>"plots",
             "linkname"=>"plotid",
             "id1"=>"id",
             "id2"=>"model_id",
             "idvalue"=>$this->model_id(),
             "id3"=>"variable_id",
             "idvalue3"=>$linea['id'],
             "showdbname"=>"name",
             "show"=>$this->text("adminmodel_Model_name"));
      $this->Relations1N[]=$R;
      $R=array(
             "title"=>$this->text("adminmodel_2D_Animations"),
             "table"=>"2danimations",
             "linkname"=>"2danimationid",
             "id1"=>"id",
             "id2"=>"model_id",
             "idvalue"=>$this->model_id(),
             "showdbname"=>"name",
             "show"=>$this->text("adminmodel_Animation_name"));
      $this->Relations1N[]=$R;
      $R=array(
             "title"=>$this->text("adminmodel_Control_Groups"),
             "table"=>"control_groups",
             "linkname"=>"controlgroupid",
             "id1"=>"id",
             "id2"=>"model_id",
             "idvalue"=>$this->model_id(),
             "showdbname"=>"name",
             "show"=>$this->text("adminmodel_Control_Group_name"));
      $this->Relations1N[]=$R;
      $R=array(
             "title"=>$this->text("adminmodel_Practices"),
             "table"=>"practices",
             "linkname"=>"practiceid",
             "id1"=>"id",
             "id2"=>"model_id",
             "idvalue"=>$this->model_id(),
             "showdbname"=>"name",
             "show"=>$this->text("adminmodel_Practices_name"));
      $this->Relations1N[]=$R;
    }
  }  
  
  function createRelations1NN1()
  {
    $this->Relations1NN1=array();
    $R=array(
             "title"=>$this->text("adminmodel_Modellers"),
             "tableLink"=>"modellers_models",
             "table2"=>"modellers",
             "linkname"=>"modellerid",
             "id1"=>"id",
             "id2"=>"id",
             "idLink1"=>"model_id",
             "idLink2"=>"modeller_id",
             "idvalue"=>$this->model_id(),
             "showdbname"=>"concat(lastname,', ',firstname)",
             "show"=>$this->text("adminmodel_Modeller_select"),
             "order"=>array("lastname","firstname"));
             
    $R["subfields"][]=array("dbname"=>"id",
                          "showname"=>$this->text("adminmodel_Modeller_identifier"),
                          "type"=>"fixed");
    $R["subfields"][]=array("dbname"=>"lastname",
                          "showname"=>$this->text("adminmodel_Modeller_lastname"),
                          "type"=>"text");
    $R["subfields"][]=array("dbname"=>"firstname",
                          "showname"=>$this->text("adminmodel_Modeller_firstname"),
                          "type"=>"text");
    $R["subfields"][]=array("dbname"=>"email",
                          "showname"=>$this->text("adminmodel_Modeller_email"),
                          "type"=>"text");
    $this->Relations1NN1[]=$R;
  }
}
/*
$M=new model();
if($M->connect())
{
  $M->display();
}else
{
  echo "error en la conexión\n";
}
*/
?>
