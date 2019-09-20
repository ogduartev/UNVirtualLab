<?php
require_once("../config/config.class.php");

class export
{
  var $tables;
  var $configurationSettings;
  var $link;
                         
  function export()
  {
    $this->tables=array();
    $conf = new configuration();
    $this->configurationSettings=$conf->readconfig('unvlconfig.txt');
  }
  
  function connect()
  {
    $username="";
    $userpass="";
    $username=$this->configurationSettings['DBuser'];
    $userpass=$this->configurationSettings['DBuserpass'];
    if($this->link=mysqli_connect($this->configurationSettings['DBserver'],$username,$userpass))
    {
      $sql="USE ".$this->configurationSettings['DBname'];
      if(!mysqli_query($this->link,$sql))
      {
        echo $this->text('about_No_Database_connection');
        return FALSE;
      }
    }else
    {
      return FALSE;
    }
    return $this->link;
  }
  
  function exportLine($table,$line)
  {
    $this->tables[$table][]=$line;
//    echo $table."\n";
//    print_r($line);
  }
  
  function exportModellersModels($id)
  {
    $modellers_id=array();
    $sql="SELECT * FROM modellers_models WHERE model_id='".$id."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($line=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $modellers_id[]=$line["modeller_id"];
        $this->exportLine("modellers_models",$line);
      }
    }
    return $modellers_id;
  }
    
  function exportTableArray($tn,$idn,$id)
  {
    $array_id=array();
    $sql="SELECT * FROM ".$tn." WHERE ".$idn."='".$id."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($line=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $array_id[]=$line["id"];
        $this->exportLine($tn,$line);
      }
    }
    return $array_id;
  }

  function exportTable($tn,$idn,$id)
  {
    $sql="SELECT * FROM ".$tn." WHERE ".$idn."='".$id."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($line=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $this->exportLine($tn,$line);
      }
    }
  }
  
  function exportTable2($tn,$idn,$mid)
  {
    $modellers_id=array();
    $sql="SELECT * FROM ".$tn." WHERE ";
    foreach($mid as $id)
    {
      $sql.=$idn."='".$id."' OR ";
    }
    $sql=substr($sql,0,strlen($sql)-4);
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($line=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $this->exportLine($tn,$line);
      }
    }
  }
  
  function exportTables($modelID)
  {
    $this->exportTable("models","id",$modelID);
    $this->exportTable("practices","model_id",$modelID);
    $modellers_ID=$this->exportModellersModels($modelID);
    $this->exportTable2("modellers","id",$modellers_ID);
    $this->exportTable("parameters","model_id",$modelID);
    $control_groups_ID=$this->exportTableArray("control_groups","model_id",$modelID);
    $this->exportTable2("controls","control_group_id",$control_groups_ID);
    $this->exportTable("variables","model_id",$modelID);
    $plots_ID=$this->exportTableArray("plots","model_id",$modelID);
    $this->exportTable2("curves","plot_id",$plots_ID);
    $animations_ID=$this->exportTableArray("2danimations","model_id",$modelID);
    $this->exportTable2("2deffects","2danimation_id",$animations_ID);
  }
  
  function exportStr()
  {
    $S=serialize($this->tables);
    echo $S."\n";
  }
  
}

$E=new export();
if($E->connect())
{
  $E->exportTables($argv[1]);
  $E->exportStr();
}else
{
  echo "No DB conection\n";
}
?>
