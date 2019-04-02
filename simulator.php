<?php
require_once("xmlthing.class.php");
require_once('block.php');

class simulator extends block
{
  var $XML;
  
  function exeFileName($model_id)
  {
    $sql="SELECT exename FROM models WHERE id='".$model_id."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      return $linea['exename'];
    }
  }
    
  function readInitXml($fn)
  {
    $xmlstr=file_get_contents($fn,FILE_TEXT);
    $xml = new XMLThing($xmlstr);
    $this->XML = $xml->parse();
  }
  
  function findModelicaName($var_id)
  {
    $sql="SELECT modelicaname FROM variables WHERE id='".$var_id."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      {
        return $linea['modelicaname'];
      }
    }
    return "";
  }
  
  function changeOutputFilter($model_id)
  {
    $filter="";
    
    $outputs=array();
    
    // PLOTS AND CURVES OUTPUTS
    
    $sql="SELECT plots.id,modelicaname FROM plots INNER JOIN variables ON plots.variable_id=variables.id WHERE plots.model_id='".$model_id."'";
    $sql.=$this->enabled("plots");
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $outputs[$linea['modelicaname']]=1;
        $sql2="SELECT modelicaname FROM curves INNER JOIN variables ON curves.variable_id=variables.id WHERE curves.plot_id='".$linea['id']."'";
        $sql2.=$this->enabled("curves");
        $result2=mysqli_query($this->link,$sql2) or die(mysqli_error($this->link).": ".$sql2);
        if($result2 and mysqli_num_rows($result2)>0)
        {
          while($linea2=mysqli_fetch_array($result2,MYSQLI_ASSOC))
          {
            $outputs[$linea2['modelicaname']]=1;        
          }
        }
      }
    }

    // 2DANIMATIONS OUTPUTS

    $sql="SELECT modelicaname FROM variables INNER JOIN 2deffects ON variables.id=2deffects.variable_id
                                   INNER JOIN 2danimations ON 2deffects.2danimation_id=2danimations.id
                                   WHERE 2danimations.model_id='".$model_id."'";
                                   
    $sql.=$this->enabled("2danimations");
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $outputs[$linea['modelicaname']]=1;        
      }
    }
    $sql="SELECT modelicaname FROM variables INNER JOIN 2deffects ON variables.id=2deffects.variable_aux_id
                                   INNER JOIN 2danimations ON 2deffects.2danimation_id=2danimations.id
                                   WHERE 2danimations.model_id='".$model_id."'";
                                   
    $sql.=$this->enabled("2danimations");
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $outputs[$linea['modelicaname']]=1;        
      }
    }
/*
    // 3DANIMATIONS OUTPUTS

    $sql="SELECT 3dobjectsOLD.* FROM 3dobjects 
                                     INNER JOIN 3danimations ON 3dobjects.3danimation_id=3danimations.id WHERE 3danimations.model_id='".$model_id."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
      }
    }
*/
    // THE FILTER
    $outputs["time"]=1;

    $filter=implode("|",array_keys($outputs));
    $this->XML['fmiModelDescription']['DefaultExperiment']['attributes']['variableFilter']=$filter;
    $this->XML['fmiModelDescription']['DefaultExperiment']['attributes']['outputFormat']='csv';
  }
  
  function changeValues()
  {
    foreach ($_POST as $K=>$Value)
    {
      if(substr($K,0,13)=="parameter_id_")
      {
        $parameter_id=substr($K,13);
        $parametername="";
        $sql="SELECT * FROM parameters WHERE id='".$parameter_id."'";
        $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
        if($result and mysqli_num_rows($result)>0)
        {
          $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
          $parametername=$linea['modelicaname'];
          $Value=$this->validateValue($linea['type'],$linea['parameter_id'],$Value,$linea['value']);
          foreach($this->XML['fmiModelDescription']['ModelVariables']['ScalarVariable'] as $KXml=>$VXml)
          {
            if($VXml['attributes']['name']==$parametername)
            { 
              $this->XML['fmiModelDescription']['ModelVariables']['ScalarVariable'][$KXml][$linea['type']]['attributes']['start']=$Value;
              $this->XML['fmiModelDescription']['ModelVariables']['ScalarVariable'][$KXml][$linea['type']]['attributes']['nominal']=$Value;
            }
          }
          foreach($this->XML['fmiModelDescription']['DefaultExperiment']['attributes'] as $KXml=>$VXml)
          {
            if($KXml==$parametername)
            {
              $this->XML['fmiModelDescription']['DefaultExperiment']['attributes'][$KXml]=$Value;
            }
          }
        }        
      }
    }
  }

  function tab($tab)
  {
    $str="";
    for($i=0;$i<$tab;$i++){$str.=" ";}
    return $str;
  }

  function encabezadoXml()
  {
    $str="<?xml version = \"1.0\" encoding=\"UTF-8\"?>\n";
    $str.="<!-- modelo adaptado del original con los parámetros de UNVirtualLab -->\n";
    return $str;
  }

  function atributosXml($att,$tab)
  {
    $str="";
    foreach($att as $K=>$V)
    {
      $str.=$this->tab($tab+1);
      $V=str_replace("<","&lt;",$V);
      $V=str_replace(">","&gt;",$V);
      $str.=$K." = \"".$V."\"\n";
    }
    return $str; 
  }

  function cadenaTipo($t,$xml,$tab)
  {
    $str="";
    $str.=$this->tab($tab)."<".$t."\n";
    $str.=$this->atributosXml($xml['attributes'],$tab);
    $str.=$this->tab($tab)."/>\n";
    return $str;
  }

  function cadenaScalarXml($xml,$tab)
  {
    $str="";
    $str.=$this->tab($tab)."<ScalarVariable\n";
    $str.=$this->atributosXml($xml['attributes'],$tab);
    $str.=$this->tab($tab).">\n";
    foreach($xml as $K=>$V)
    {
      if($K!="attributes")
      {
        $str.=$this->cadenaTipo($K,$V,$tab);
      }
    }
    $str.=$this->tab($tab)."</ScalarVariable>\n";
    return $str;
  }

  function scalarVarsXml($xml,$tab)
  {
    $str="";
    foreach($xml as $K=>$V)
    {
      $str.=$this->cadenaScalarXml($V,$tab);
    }
    return $str;
  }

  function cadenaModelXml($xml,$tab)
  {
    $str="";
    $str.=$this->tab($tab)."<ModelVariables>\n";
    $tab++;
    $str.=$this->scalarVarsXml($xml['ScalarVariable'],$tab);
    $tab--;
    $str.=$this->tab($tab)."</ModelVariables>\n";
    return $str;
  }

  function cadenaExpXml($xml,$tab)
  {
    $str="";
    $str.=$this->tab($tab)."<DefaultExperiment\n";
    $str.=$this->atributosXml($xml['attributes'],$tab);
    $str.=$this->tab($tab)."/>\n";
    return $str;
  }

  function cadenaFmiXml($xml,$tab)
  {
    $str="";
    $str.=$this->tab($tab)."<fmiModelDescription\n";
    $str.=$this->atributosXml($xml['attributes'],$tab);
    $str.=$this->tab($tab).">\n";
    $tab++;
    $str.=$this->cadenaExpXml($xml['DefaultExperiment'],$tab);
    $str.=$this->cadenaModelXml($xml['ModelVariables'],$tab);
    $tab--;
    $str.=$this->tab($tab)."</fmiModelDescription>\n";
    return $str;
  }

  function writeInitXml($fn,$tab)
  {
    $str=$this->encabezadoXml();
    $tab++;
    $str.=$this->cadenaFmiXml($this->XML['fmiModelDescription'],$tab);
    $tab--;
    $f=fopen($fn,"w");
    fwrite($f,$str);
    fclose($f);
  }


  function runSimulation($model_id,$exename,$tmpInitFile)
  {
    $logEnabled=true;
    chmod($tmpInitFile,0666);
    $pos=strrpos($tmpInitFile,"/work/");
    $dir=substr($tmpInitFile,0,$pos);
    $dirbin=$dir."/files/".$model_id."/bin/";
    $tmpResFile=$tmpInitFile."_res.csv";
    chdir($dirbin);
    $run=$dirbin.$exename." -f ".'"'.$tmpInitFile.'"'." -r ".'"'.$tmpResFile.'" > /dev/null';
    $this->logMessage(0);
    
    passthru($run);
        
    $_POST['res_file']=$tmpResFile;
  }

  function simulate($model_id)
  {
    $exeFileName=$this->exeFileName($model_id);
    $originalInitFile= "files/".$model_id."/bin/".$exeFileName."_init.xml";
    $this->readInitXml($originalInitFile);
    $this->changeOutputFilter($model_id);
    $this->changeValues();
    $tmpInitFile=tempnam("work","INI_");
    $this->writeInitXml($tmpInitFile,0);                           
    $this->runSimulation($model_id,$exeFileName,$tmpInitFile);
  }
}

?>
