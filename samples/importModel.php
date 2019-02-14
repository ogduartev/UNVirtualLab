<?php
require_once("../config/config.class.php");

class import
{
  var $tables;
  var $id_eq;
  var $configurationSettings;
  var $link;
                         
  function import()
  {
    $this->tables=array();
    $this->id_eq=array();
    $conf = new configuration();
    $this->configurationSettings=$conf->readconfig('unvlconfig.txt');
  }
  
  function connect()
  {
    $username="";
    $userpass="";
    $username=$this->configurationSettings['DBadmin'];
    $userpass=$this->configurationSettings['DBadminpass'];
    if($link=mysqli_connect($this->configurationSettings['DBserver'],$username,$userpass))
    {
      $sql="USE ".$this->configurationSettings['DBname'];
      if(!mysqli_query($link,$sql))
      {
        echo $this->text('about_No_Database_connection');
        return FALSE;
      }
    }else
    {
      return FALSE;
    }
    $this->link=$link;
    return $link;
  }

  function importTable($tn,$eqs)
  {
    if(!isset($this->tables[$tn])){return;}
    foreach($this->tables[$tn] as $t)
    {
      $flag=0;
      if(array_key_exists("id",$t))
      {
        $flag=true;
        $id1=$t['id'];
      }
      unset($t['id']);
      $sql="INSERT INTO ".$tn."(";
      foreach($t as $K=>$V)
      {
        if($K=="hidden"){continue;}
        if($K=="alias"){continue;}
        $sql.=$K.", ";
      }
      $sql=substr($sql,0,strlen($sql)-2);
      $sql.=") VALUES(";    
      foreach($t as $K=>$V)
      {
        if($K=="hidden"){continue;}
        if($K=="alias"){continue;}
        if(isset($this->id_eq[$K][$V]))
        {
          $V=$this->id_eq[$K][$V];
        }
        $sql.="'".$V."', ";
      }
      $sql=substr($sql,0,strlen($sql)-2);
      $sql.=")";
//      echo $sql."\n";
      mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
      $id2=mysqli_insert_id($this->link);
      if($tn=="models")
      {
        $this->createDirs($id2);
      }
      if($flag)
      {
        foreach($eqs as $eq)
        {
          $this->id_eq[$eq][$id1]=$id2;    
        }
      }
    }
  }  
  
  function findRootSection()
  {
    $sql="SELECT * FROM sections WHERE section_id IS NULL ";
    $result=mysqli_query($this->link,$sql) or die(mysql_error($this->link)." ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $line=mysqli_fetch_array($result,MYSQLI_ASSOC);
      $rootID=$line['id'];
    }
    foreach($this->tables["models"] as $T)
    {
      $this->id_eq["section_id"][$T["section_id"]]=$rootID;        
    }
  }
  
  function createDirs($id)
  {
    $oldumask = umask(0);
    mkdir("../files/".$id,0777,true);
    mkdir("../files/".$id."/bin",0777,true);
    mkdir("../files/".$id."/doc",0777,true);
    mkdir("../files/".$id."/graphics",0777,true);
    mkdir("../files/".$id."/modelica",0777,true);
    umask($oldumask);
  }

  function importTables()
  {
    $this->findRootSection();
    $this->importTable("models",array("model_id"));
    $this->importTable("practices",array());
    $this->importTable("modellers",array("modeller_id"));
    $this->importTable("modellers_models",array());
    $this->importTable("parameters",array("parameter_id"));
    $this->importTable("control_groups",array("control_group_id"));
    $this->importTable("controls",array());
    $this->importTable("variables",array("variable_id","variable_aux_id"));
    $this->importTable("plots",array("plot_id"));
    $this->importTable("curves",array());
    $this->importTable("2danimations",array("2danimation_id"));
    $this->importTable("2deffects",array());
  }
  
  function importStr($fn)
  {
    $f=fopen($fn,"r");
    $S=fread($f,filesize($fn));
    $this->tables=unserialize($S);
    print_r($this->tables);
  }
  
}

$E=new import();
if($E->connect())
{
  $E->importStr($argv[1]);
  $E->importTables();
}else
{
  echo "No DB conection\n";
}
?>
