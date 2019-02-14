<?php
require_once("block.php");

class databasemanager extends block
{
  function update($table,$idname,$idvalue)
  {
    $this->connect();
    foreach($_POST as $K=>$V)
    {
      if(substr($K,0,3)=="db_")
      {
        $col=substr($K,3);
        $sql="UPDATE ".$table." SET ".$col."='".str_replace("'","\'",$V)."' WHERE ".$idname."='".$idvalue."'";
        mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
      }
    }
  }
  
  function update1NN1($idvalue)
  {
    $this->connect();
    if(isset($_POST['db_'.$_POST['idLink2']]))
    {
      $sql="SELECT * FROM ".$_POST['tableLink']." WHERE ".$_POST['idLink1']."=".$idvalue." AND ".$_POST['idLink2']."=".$_POST['id2Value'];
      $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
      if($result and mysqli_num_rows($result)>0)
      {
      }else
      {
        $sql="INSERT INTO ".$_POST['tableLink']."(".$_POST['idLink1'].",".$_POST['idLink2'].") VALUES(".$idvalue.",".$_POST['id2Value'].")";
        mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
      }
      
    }else
    {
      $sql="DELETE FROM ".$_POST['tableLink']." WHERE ".$_POST['idLink1']."=".$idvalue." AND ".$_POST['idLink2']."=".$_POST['id2Value'];
      mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
    }
    foreach($_POST as $K=>$V)
    {
      if(substr($K,0,3)=="db_" and $K!='db_'.$_POST['idLink2'])
      {
        $col=substr($K,3);
        $sql="UPDATE ".$_POST['table2']." SET ".$col."='".str_replace("'","\'",$V)."' WHERE ".$_POST['id2']."='".$_POST['id2Value']."'";
        mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
      }
    }
  }
  
  function insert()
  {
    $this->connect();
    $cols="";
    $values="";
    foreach($_POST as $K=>$V)
    {
      if(substr($K,0,3)=="db_")
      {
        $cols.=substr($K,3).",";
        $values.="'".str_replace("'","\'",$V)."',";
      }
    }
    $cols=substr($cols,0,strlen($cols)-1);
    $values=substr($values,0,strlen($values)-1);
    $sql="INSERT INTO ".$_POST['insert_table']."(".$cols.") VALUES(".$values.")";
    mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
    return mysqli_insert_id($this->link);
  }
  
  function insert1NN1($idvalue)
  {
    $this->connect();
    $cols="";
    $values="";
    foreach($_POST as $K=>$V)
    {
      if(substr($K,0,3)=="db_")
      {
        $cols.=substr($K,3).",";
        $values.="'".str_replace("'","\'",$V)."',";
      }
    }
    $cols=substr($cols,0,strlen($cols)-1);
    $values=substr($values,0,strlen($values)-1);
    $sql="INSERT INTO ".$_POST['table2']."(".$cols.") VALUES(".$values.")";
    mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
    $id2=mysqli_insert_id($this->link);
    $sql="INSERT INTO ".$_POST['tableLink']."(".$_POST['idLink1'].",".$_POST['idLink2'].") VALUES(".$idvalue.",".$id2.")";
    mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
    $id2=mysqli_insert_id($this->link);
    return $id2;
  }
  
  function delete()
  {
    $this->connect();
    $sql="DELETE FROM ".$_POST['delete_table']." WHERE ".$_POST['delete_colid']."='".$_POST['delete_id']."'";
//    echo $sql;
    mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
    return;
  }

  function delete1NN1($idvalue)
  {
    $this->connect();
    $sql="DELETE FROM ".$_POST['tableLink']." WHERE ".$_POST['idLink2']."='".$_POST['id2Value']."'";
    mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
    $sql="DELETE FROM ".$_POST['table2']." WHERE ".$_POST['id2']."='".$_POST['id2Value']."'";
    mysqli_query($this->link,$sql) or die(mysqli_error($this->link)." :".$sql);
    return;
  }

  function createDirectories($id)
  {
    $oldumask = umask(0);
    mkdir($this->configurationSettings['unvlDir']."/files/".$id,0777,true);
    mkdir($this->configurationSettings['unvlDir']."/files/".$id."/bin",0777,true);
    mkdir($this->configurationSettings['unvlDir']."/files/".$id."/doc",0777,true);
    mkdir($this->configurationSettings['unvlDir']."/files/".$id."/graphics",0777,true);
    mkdir($this->configurationSettings['unvlDir']."/files/".$id."/modelica",0777,true);
    umask($oldumask);
  }
  
  function deleteDir($dir)
  {
    $iterator = new RecursiveDirectoryIterator($dir);
    foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) 
    {
      if ($file->isDir())
      {
        rmdir($file->getPathname());
      }else
      {
        unlink($file->getPathname());
      }
    }
    rmdir($dir);
  }

  function deleteDirectories($id)
  {
    $this->deleteDir("files/".$id);
  }
  
  function display()
  {
  }
}
?>
