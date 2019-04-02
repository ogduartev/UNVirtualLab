<?php
require_once("admin/adminblock.php");

class adminmodelfiles extends block
{  
  var $filedata;
  
  function createdata()
  {
    $this->filedata=array();
    $fd=array("name"=>$this->text("adminmodelfiles_Executable"),
              "uploadtype"=>"exe",
              "path"=>"files/".$this->model_id()."/bin/",
              "process"=>"db_executable");
    $this->filedata[$fd['uploadtype']]=$fd;
    $fd=array("name"=>$this->text("adminmodelfiles_Documentation"),
              "uploadtype"=>"doc",
              "path"=>"files/".$this->model_id()."/doc/",
              "process"=>"db_documentation");
    $this->filedata[$fd['uploadtype']]=$fd;
    $fd=array("name"=>$this->text("adminmodelfiles_Modelica_files"),
              "uploadtype"=>"mo",
              "path"=>"files/".$this->model_id()."/modelica/",
              "process"=>"db_modelica");
    $this->filedata[$fd['uploadtype']]=$fd;
  }
  
  function copyfile($FILE,$FD)
  {
    $fn=$FD['path'].$FILE['name'];
    move_uploaded_file($FILE['tmp_name'],$fn);
  }
  
  function db_modelica($FILE,$FD)
  {
    $this->copyfile($FILE,$FD);
    $orden= "mv ".$FD['path'].$FILE['name']." ".$FD['path']."source.tar.gz";
    passthru($orden);
  }
  
  function db_documentation($FILE,$FD)
  {
    $this->copyfile($FILE,$FD);
    $orden= "mv ".$FD['path'].$FILE['name']." ".$FD['path']."documentation.pdf";
    passthru($orden);
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
        if($path_parts['extension']=="mos")
        {
          return $fd;
        }
      }
    }
    return $mosfile;
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
    
    $f=glob($this->configurationSettings['unvlDir']."/".$path_parts['dirname']."/*_init.xml");
    if(count($f)!=1){return;}
    $fn=basename($f[0]);
    $fn=substr($fn,0,strlen($fn)-9);
    $exename="";

    if(file_exists($this->configurationSettings['unvlDir']."/".$path_parts['dirname']."/".$fn))
    {
      $exename=$fn;
    }elseif(file_exists($this->configurationSettings['unvlDir']."/".$path_parts['dirname']."/".$fn.".exe"))
    {
      $exename=$fn.".exe";
    }
    if(!($exename==""))
    {
      $orden="cp \"".$this->configurationSettings['unvlDir']."/".$path_parts['dirname']."/".$fn."_init.xml\" ".$this->configurationSettings['unvlDir']."/".$FD['path'].".";
      passthru($orden);
      $orden="cp \"".$this->configurationSettings['unvlDir']."/".$path_parts['dirname']."/".$fn."_res.csv\" ".$this->configurationSettings['unvlDir']."/".$FD['path'].".";
      passthru($orden);
      $orden="cp \"".$this->configurationSettings['unvlDir']."/".$path_parts['dirname']."/".$fn."_info.json\" ".$this->configurationSettings['unvlDir']."/".$FD['path'].".";
      passthru($orden);
      $orden="cp \"".$this->configurationSettings['unvlDir']."/".$path_parts['dirname']."/".$exename."\" ".$this->configurationSettings['unvlDir']."/".$FD['path'].".";
      passthru($orden);
    }
    
    chdir($this->configurationSettings['unvlDir']);
  }
  
  function db_executable($FILE,$FD)
  {
    $fdir=$this->configurationSettings['unvlDir']."/";

    $orden= "rm -r ".$fdir.$FD['path']."*";
    passthru($orden);
    $orden= "rm -r ".$fdir.$FD['path']."*.*";
    passthru($orden);
    $this->copyfile($FILE,$FD);
    chmod($fdir.$FD['path'].$FILE['name'],0777);
    $orden= "gunzip -c ".$fdir.$FD['path'].$FILE['name']." | tar -C ".$fdir.$FD['path']." -x";
    passthru($orden);
    $orden= "rm ".$fdir.$FD['path'].$FILE['name'];
    passthru($orden);
    if($this->configurationSettings['Compilation']=='TRUE')
    {
      $this->compile($FD);
    }
    $f=glob($fdir.$FD['path']."*_init.xml");
    if(count($f)!=1){return;}
    $fn=basename($f[0]);
    $fn=substr($fn,0,strlen($fn)-9);
    $exename="";

    if(file_exists($fdir.$FD['path']."/".$fn))
    {
      $exename=$fn;
    }elseif(file_exists($fdir.$FD['path']."/".$fn.".exe"))
    {
      $exename=$fn.".exe";
    }

   if(file_exists($fdir.$FD['path']."/".$exename) and 
       file_exists($fdir.$FD['path']."/".$fn."_init.xml") and
       file_exists($fdir.$FD['path']."/".$fn."_res.csv"))
    {
      chmod($fdir.$FD['path'].$exename,0777);
      chmod($fdir.$FD['path'].$fn."_init.xml",0777);
      chmod($fdir.$FD['path'].$fn."_res.csv",0777);
      chmod($fdir.$FD['path'].$fn."_info.json",0777);
      $sql="UPDATE models SET exename='".$exename."' WHERE id='".$this->model_id()."'";
      mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
      $this->updateDBvarsAndPars($fdir.$FD['path'].$fn);
    }else
    {
      return;
    }

  }
  
  function readInitXml($fn)
  {
    $xmlstr=file_get_contents($fn,FILE_TEXT);
    $xml = new XMLThing($xmlstr);
    $this->XML = $xml->parse();
  }  
  
  function varsInInitXml($fn)
  {
    $xmlstr=file_get_contents($fn,FILE_TEXT);
    $xml = new XMLThing($xmlstr);
    $XML = $xml->parse();
    $varXML=array();
    foreach($XML['fmiModelDescription']['ModelVariables']['ScalarVariable'] as $V)
    {
      $varXML[$V['attributes']['name']]=array(
                                       "modelicaname"=>$V['attributes']['name'],
                                       "alias"       =>$V['attributes']['alias'],
                                       "description" =>$V['attributes']['description'],
                                       "units"       =>$V['attributes']['unit']);
      $type="";
      if(isset($V['Real'])){$type="Real";}
      if(isset($V['Integer'])){$type="Integer";}
      if(isset($V['Boolean'])){$type="Boolean";}
      $varXML[$V['attributes']['name']]['type']=$type;
      $varXML[$V['attributes']['name']]['value']=$V[$type]['attributes']['start'];
      $varXML[$V['attributes']['name']]['units']=$V[$type]['attributes']['unit'];
    }
    $expPars=array("startTime"=>"Real","stopTime"=>"Real","stepSize"=>"Real","tolerance"=>"Real","solver"=>"Text");
    foreach($expPars as $par=>$type)
    {
      $varXML[$par]=array("modelicaname"=>$par,"alias"=>$par,"description"=>"Experiment parameter","units"=>"");
      $varXML[$par]['value']=$type;
      $varXML[$par]['value']=$XML['fmiModelDescription']['DefaultExperiment']['attributes'][$par];
      $varXML[$par]['units']="";
    }
//    print_r($XML['fmiModelDescription']['DefaultExperiment']);
    return $varXML;
  }
  
  function varsInResCsv($fn)
  {
    $f=file($fn);
    $f=str_replace(",\n","",$f);
    $dataName=explode(',',str_replace('"','',$f[0]));
    return $dataName;  
  }
  
  function inDB($Table)
  {
    $varDB=array();
    $sql="SELECT * FROM ".$Table." WHERE model_id='".$this->model_id()."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $varDB[$linea['modelicaname']]=$linea;
      }
    }
    return $varDB;
  }
    
  function updateDBvarsAndPars($fn)
  {
    $varParXml=$this->varsInInitXml($fn."_init.xml");
    $varCsv=$this->varsInResCsv($fn."_res.csv");
    $varXml=array();
    $parXml=array();
    foreach($varParXml as $mn=>$var)
    {
      if(in_array($mn,$varCsv))
      {
        $varXml[$mn]=$var;
      }else
      {
        $parXml[$mn]=$var;
      }
    }
    $varXml["time"]=array("modelicaname"=>"time","alias"=>"time","description"=>"time","units"=>"s","type"=>"Real","value"=>"0");
    $this->compareUpdateXMLDB($varXml,"variables");
    $this->compareUpdateXMLDB($parXml,"parameters");
  }
  
  function compareUpdateXMLDB($Xml,$Table)
  {    
    // estrategia: 
    // 1) recorrer DB. para cada var, buscar en Xml.
    //                      Si se encuentra el mismo modelicaname actualizar y borrar de Xml. 
    //                      Si no se encuentra borrarlo de la BD.     
    // 2) recorrer los que quedaron en Xml. insertarlos en la BD.
    
    //1)
    $DB =$this->inDB($Table);
    foreach($DB as $mn=>$var)
    {
      if(array_key_exists($mn,$Xml))
      {
        $strSET="";
        foreach($Xml[$mn] as $K=>$V)
        {
          $strSET.=$K."='".$V."', ";
        }
        $strSET=substr($strSET,0,strlen($strSET)-2);
        
        $sql="UPDATE ".$Table." SET ".$strSET." WHERE id='".$var['id']."'";
        mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
        unset($Xml[$mn]);
      }else
      {
        $sql="DELETE FROM ".$Table." WHERE id='".$var['id']."'";
        mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
      }
    }
    //2)
    foreach($Xml as $mn=>$var)
    {
      $strCOL="model_id";
      $strVAL="'".$this->model_id()."'";
      foreach($Xml[$mn] as $K=>$V)
      {
        $strCOL.=", ".$K;
        $strVAL.=", '".$V."'";
      }
      $sql="INSERT INTO ".$Table."(".$strCOL.") VALUES(".$strVAL.")";
      mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    }
  }
    
  function upload() 
  {
    $FD=$this->filedata[$_POST['uploadfiletype']];
    if ($_FILES["file".$FD['uploadtype']]["error"] == 0 and strlen($FD['process'])>0)
    {
//      $this->$FD['process']($_FILES['file'.$FD['uploadtype']],$FD);
      switch($FD['process'])
      {
        case 'db_documentation': $this->db_documentation($_FILES['file'.$FD['uploadtype']],$FD);break;
        case 'db_executable': $this->db_executable($_FILES['file'.$FD['uploadtype']],$FD);break;
        case 'db_modelica': $this->db_modelica($_FILES['file'.$FD['uploadtype']],$FD);break;
        default: brak;
      }
    }
  }
  
  function displayFD($FD) 
  {
    echo " <tr>\n";
    echo "<form method=\"POST\" enctype=\"multipart/form-data\">\n";
    echo "  <td class=\"updatefile_name\">".$FD['name']."</td>\n";
    echo "  <td class=\"updatefile_file\">\n";
    echo "   <input type=\"hidden\" name=\"uploadfiletype\" value=\"".$FD['uploadtype']."\" />\n";
    echo "   <label for=\"file".$FD['uploadtype']."\" class=\"cabinet\">\n";
    echo "    <input type=\"file\" name=\"file".$FD['uploadtype']."\" id=\"file".$FD['uploadtype']."\" class=\"file\"/>\n";
    echo "   </label>\n";
    echo "  </td>\n";
    echo "  <td class=\"updatefile_button\">\n";
    echo "   <input type=\"submit\" name=\"upload_file\" value=\"Enviar\" class=\"controls_button_file\"/>\n";
    echo "  </td>\n";
    echo "</form>\n";    
    echo "</tr>\n";
  }
  
  function display()
  {
    $this->createdata();
    if(isset($_POST['upload_file']))
    {
      $this->upload();
    }
    echo "<script type=\"text/javascript\" src=\"admin/si.files.js\"></script>\n";
    echo "<table class=\"updatefile\">\n";
    foreach($this->filedata as $FD)
    {
      $this->displayFD($FD);
    }
    echo "</table>\n";
    echo "<script type=\"text/javascript\">SI.Files.stylizeAll();</script>\n";
  }
  
}

?>
