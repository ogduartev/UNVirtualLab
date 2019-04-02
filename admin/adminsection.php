<?php
require_once("admin/adminblock.php");

class adminsection extends adminblock
{  

  function createFields()
  {
    $this->blockname=$this->text("adminsection_Section");
    $this->table="sections";
    $this->sectionidname="sectionid";
    $this->idname="id";
    $this->idvalue=$this->section_id();
    $this->fields=array();
    $this->fields[]=array("dbname"=>"id",
                          "showname"=>$this->text("adminsection_Identifier"),
                          "type"=>"fixed");
    $this->fields[]=array("dbname"=>"name",
                          "showname"=>$this->text("adminsection_Section_name"),
                          "type"=>"text");
    $this->fields[]=array("dbname"=>"description",
                          "showname"=>$this->text("adminsection_Description"),
                          "type"=>"longtext");
    $this->fields[]=array("dbname"=>"enabled",
                          "showname"=>$this->text("adminsection_Enabled"),
                          "type"=>"bool");
    $this->fields[]=array("dbname"=>"section_id",
                          "showname"=>$this->text("adminsection_Father_section"),
                          "type"=>"select section");
  }  
  
  function createRelations1N()
  {
    $this->Relations1N=array();
    $R=array(
             "title"=>$this->text("adminsection_Daugther_sections"),
             "table"=>"sections",
             "linkname"=>"sectionid",
             "id1"=>"id",
             "id2"=>"section_id",
             "idvalue"=>$this->section_id(),
             "showdbname"=>"name",
             "show"=>$this->text("adminsection_Section_name"));
    $this->Relations1N[]=$R;
    $R=array(
             "title"=>$this->text("adminsection_Models"),
             "table"=>"models",
             "linkname"=>"modelid",
             "id1"=>"id",
             "id2"=>"section_id",
             "idvalue"=>$this->section_id(),
             "showdbname"=>"name",
             "show"=>$this->text("adminsection_Model_name"));
    $this->Relations1N[]=$R;
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
