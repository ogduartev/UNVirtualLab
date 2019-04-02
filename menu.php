<?php
require_once('phptliste/tlistemysql.php');


class menu extends block
{
  
  function display()
  {
    if(!isset($_REQUEST["_1node"]))
    {
      $_REQUEST["_1node"]=1;
    }
    $data=array();
    $data['class']="section_name";
    $data['table1']="sections";
    $data['up_id1']="section_id";
    $data['name1']="name";
    $data['help1']="description";
    $data['link_id1']="";
    $data['table2']="models";
    $data['up_id2']="section_id";
    $data['name2']="name";
    $data['help2']="description";
    $data['url']="block.php";
    $data['link_id2']="modelid";
    $data['dir_img']="./themes/".$this->configurationSettings['theme']."/menuimg";
    $tree = new tlistemysql($this->link,"_1","","menumodel",$data," AND enabled=1") ;
    $tree->display();
  }
}
?>
