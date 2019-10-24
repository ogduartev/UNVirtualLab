<?php

class recompile
{
  var $configurationSettings;
  
  function recompile()
  {
    $this->configurationSettings=array();
	  $this->configurationSettings['Compilation']='TRUE';  
	  $this->configurationSettings['CompilationOrder']='omc';
//	  $this->configurationSettings['CompilationPostOrder']='';
	  $this->configurationSettings['CompilationPostOrder']='> /dev/null';
	  $this->configurationSettings['unvlDir']='/home/ogduartev/public_html/unvl';
  }

  function findMos($dir)
  {
    if(!(substr($dir,-1)=="/")){$dir.="/";}
    $mosfile="";
    $fn=scandir($dir);
    foreach($fn as $f)
    {
      $fd=$dir.$f;
      if(is_dir($fd) and !($f==".")  and !($f=="..") )
      {
        $mosfile=$this->findMos($fd);
      }else
      {
        $path_parts = pathinfo($fd);
        if(array_key_exists('extension',$path_parts) and $path_parts['extension']=="mos")
        {
          return $fd;
        }
      }
    }
    return $mosfile;
  }

	function ciclo($model_id)
	{
    $fdir=$this->configurationSettings['unvlDir']."/";

    $FD=array();
    $FD['path']=$fdir."files/".$model_id."/bin/";

/*
    $orden= "rm -r ".$fdir.$FD['path']."*";
    passthru($orden);
    $orden= "rm -r ".$fdir.$FD['path']."*.*";
    passthru($orden);
    $this->copyfile($FILE,$FD);
    chmod($fdir.$FD['path'].$FILE['name'],0777);
    $orden= "gunzip -c ".$fdir.$FD['path'].$FILE['name']." | tar -C ".$fdir.$FD['path']." -x";
    passthru($orden);
    $orden= "rm ".$fdir.$FD['path'].$FILE['name'];
    passthru($orden);*/

    if($this->configurationSettings['Compilation']=='TRUE')
    {
      $this->compile($FD);
    }
    
    $f=glob($FD['path']."*_init.xml");
    if(count($f)!=1){echo "NO:\n";return;}
    $fn=basename($f[0]);
    $fn=substr($fn,0,strlen($fn)-9);
    $exename="";

    if(file_exists($FD['path']."/".$fn))
    {
      $exename=$fn;
    }elseif(file_exists($FD['path']."/".$fn.".exe"))
    {
      $exename=$fn.".exe";
    }

   if(file_exists($FD['path']."/".$exename) and 
       file_exists($FD['path']."/".$fn."_init.xml") and
       file_exists($FD['path']."/".$fn."_res.csv"))
    {
      chmod($FD['path'].$exename,0777);
      chmod($FD['path'].$fn."_init.xml",0777);
      chmod($FD['path'].$fn."_res.csv",0777);
      chmod($FD['path'].$fn."_info.json",0777);
      $sql="UPDATE models SET exename='".$exename."' WHERE id='".$model_id."'";
//      mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
//      $this->updateDBvarsAndPars($FD['path'].$fn);
    }else
    {
      return;
    }
  }
        
  function compile($FD)
  {
    $mosfile=$this->findMos($FD['path']);
    if($mosfile==""){return;}
    $path_parts = pathinfo($mosfile);
    chdir($path_parts['dirname']);
    $orden=$this->configurationSettings['CompilationOrder']." ";
    $orden.=$path_parts['basename'];
    $orden.=" ".$this->configurationSettings['CompilationPostOrder'];
    passthru($orden);
    
    $f=glob($path_parts['dirname']."/*_init.xml");
    if(count($f)!=1){return;}
    $fn=basename($f[0]);
    $fn=substr($fn,0,strlen($fn)-9);
    $exename="";

    if(file_exists($path_parts['dirname']."/".$fn))
    {
      $exename=$fn;
    }elseif(file_exists($path_parts['dirname']."/".$fn.".exe"))
    {
      $exename=$fn.".exe";
    }
    if(!($exename==""))
    {
      $orden="cp \"".$path_parts['dirname']."/".$fn."_init.xml\" ".$FD['path'].".";
      passthru($orden);
      $orden="cp \"".$path_parts['dirname']."/".$fn."_res.csv\" ".$FD['path'].".";
      passthru($orden);
      $orden="cp \"".$path_parts['dirname']."/".$fn."_info.json\" ".$FD['path'].".";
      passthru($orden);
      $orden="cp \"".$path_parts['dirname']."/".$exename."\" ".$FD['path'].".";
      passthru($orden);
    }
    
    chdir($this->configurationSettings['unvlDir']);
  }
    
}

$R=new recompile();
for($model_id=0;$model_id<19;$model_id++)
{
echo "***************\n";
echo "       model_id: ".$model_id."\n";
echo "***************\n";
	$R->ciclo($model_id);
}
?>
