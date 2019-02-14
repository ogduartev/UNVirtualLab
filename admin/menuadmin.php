<?php
require_once('phptliste/tlistemysql.php');


class menuadmin extends block
{
  
  function display()
  {
    $data=array();
    $data['class']="section_name";
    $data['table1']="sections";
    $data['up_id1']="section_id";
    $data['name1']="name";
    $data['help1']="description";
    $data['link_id1']="sectionid";
    $data['table2']="models";
    $data['up_id2']="section_id";
    $data['name2']="name";
    $data['help2']="description";
    $data['url']="block.php";
    $data['link_id2']="modelid";
    $tree = new tlistemysql($this->link,"_1","","menumodel",$data,"") ;
    $tree->display();
  }
}
/*
$M=new menuadmin();
if($M->connect())
{
  $M->display();
}else
{
  echo "error en la conexión\n";
}*/
?>
