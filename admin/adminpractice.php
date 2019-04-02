<?php
require_once("admin/adminblock.php");

class adminpractice extends adminblock
{  

  function createFields()
  {
    $this->blockname=$this->text("adminpractice_Practice");
    $this->table="practices";
    $this->sectionidname="practiceid";
    $this->idname="id";
    $this->idvalue=$this->practice_id();
    $this->fields=array();
    $this->fields[]=array("dbname"=>"id",
                          "showname"=>$this->text("adminpractice_Identifier"),
                          "type"=>"fixed");
    $this->fields[]=array("dbname"=>"name",
                          "showname"=>$this->text("adminpractice_Practice_name"),
                          "type"=>"text");
    $this->fields[]=array("dbname"=>"header",
                          "showname"=>$this->text("adminpractice_Header"),
                          "type"=>"longtext");
    $this->fields[]=array("dbname"=>"description",
                          "showname"=>$this->text("adminpractice_Description"),
                          "type"=>"longtext");
    $this->fields[]=array("dbname"=>"enabled",
                          "showname"=>$this->text("adminpractice_Enabled"),
                          "type"=>"bool");
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
