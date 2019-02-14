<?php
require_once('MyXml.php');
require_once('csvreader.php');

class animation2dSVG extends MyXml
{
  var $Modifications;
  var $Data;
  
  function animation2dSVG()
  {
    $this->block();
    $this->Modifications=array();
  }

  function insertAnimation(&$node,$subtype,$modification,$subnode,$number)
  {
    if($number<0)
    {
      foreach($modification as $K=>$V)
      {
        if($K!=="svg_id")
        {
          $node[$subtype]['attributes'][$K]=$V;
        }
      }
      unset($node[$subtype]['attributes']['from']);
      unset($node[$subtype]['attributes']['to']);
    }else
    {
      foreach($modification as $K=>$V)
      {
        if($K!=="svg_id")
        {
          $node[$subtype][$number]['attributes'][$K]=$V;
        }
      }
      unset($node[$subtype][$number]['attributes']['from']);
      unset($node[$subtype][$number]['attributes']['to']);
    }
  }
   
  function modifyXml(&$node,$modification)
  {
    if(!is_array($node)){return false;}
    if(isset($node['attributes']['id']) and $node['attributes']['id']==$modification['svg_id'])
    {
      return true;
    }
    foreach($node as $subtype=>$subnode)
    {
      if(is_array($subnode) and array_key_exists(0,$subnode))
      {
        foreach($subnode as $number=>$subsubnode)
        {
          if($this->modifyXml($node[$subtype][$number],$modification))
          {
            $this->insertAnimation($node,$subtype,$modification,$subsubnode,$number);
            return;
          }
        }
      }else
      {
        if($this->modifyXml($node[$subtype],$modification))
        {
          $this->insertAnimation($node,$subtype,$modification,$subnode,-100000);
          return;
        }
      }
    }
    return false;
  }
  
  function colorScale($value,$effect)
  {
    $value=$effect['offset']+$effect['scale']*$value;
    $Ro=hexdec(substr($effect['colorRGBmin'],0,2));
    $Go=hexdec(substr($effect['colorRGBmin'],2,2));
    $Bo=hexdec(substr($effect['colorRGBmin'],4,2));
    $Rf=hexdec(substr($effect['colorRGBmax'],0,2));
    $Gf=hexdec(substr($effect['colorRGBmax'],2,2));
    $Bf=hexdec(substr($effect['colorRGBmax'],4,2));
    $min=$effect['colorMin'];
    $max=$effect['colorMax'];
    $factor=($value-$min)/($max-$min);
    $R=$Ro+$factor*($Rf-$Ro);
    $G=$Go+$factor*($Gf-$Go);
    $B=$Bo+$factor*($Bf-$Bo);
    if($R>255){$R=255;}
    if($G>255){$G=255;}
    if($B>255){$B=255;}
    if($R<0){$R=0;}
    if($G<0){$G=0;}
    if($B<0){$B=0;}
    $strR=dechex($R);if(strlen($strR)<2){$strR="0".$strR;}
    $strG=dechex($G);if(strlen($strG)<2){$strG="0".$strG;}
    $strB=dechex($B);if(strlen($strB)<2){$strB="0".$strB;}
    $strR=substr($strR,0,2);
    $strG=substr($strG,0,2);
    $strB=substr($strB,0,2);
    return "#".$strR.$strG.$strB;
  }
  
  function sync($effect,$animation)
  {
    $M=array();
    $modelica_name1=$effect['modelicaname1'];
    $modelica_name2=$effect['modelicaname2'];
    $duration=$animation['duration'];
    if($duration==0)
    {
      foreach($this->Data[$modelica_name1] as $time=>$value)
      {
        $duration=$this->Data['time'][$time];
      }                
    }
    $sample_time=$animation['sample_time'];
    $offset=$effect['offset'];
    $scale=$effect['scale'];
    $maxTime=count($this->Data[$modelica_name1])+1;
    $timeStr="";
    $valueStr="";
    $timeStr.="0.0; ";
    switch($effect['type'])
    {
      case 'single' :
                      $valueStr.=str_replace(",",".",$offset+$scale*$this->Data[$modelica_name1][0])."; ";
                      $cntTime=0.0;
                      foreach($this->Data[$modelica_name1] as $time=>$value)
                      {
                        if(($this->Data['time'][$time] - $cntTime)>=$sample_time)
                        {
                          $timeStr.=str_replace(",",".",($time/$maxTime))."; ";
                          $valueStr.=str_replace(",",".",$offset+$scale*$value)."; ";
                          $cntTime=$this->Data['time'][$time];
                        }
                      }
                      $timeStr.="1.0";
                      $valueStr.=str_replace(",",".",$offset+$scale*$this->Data[$modelica_name1][$maxTime-2]);
                      break;
      case 'path' :
      case 'double' :
                      $valueStr.=str_replace(",",".",$offset+$scale*$this->Data[$modelica_name1][0]).",";
                      $valueStr.=str_replace(",",".",$offset+$scale*$this->Data[$modelica_name2][0])."; ";
                      $cntTime=0.0;
                      foreach($this->Data[$modelica_name1] as $time=>$value)
                      {
                        if(($this->Data['time'][$time] - $cntTime)>=$sample_time)
                        {
                          $timeStr.=str_replace(",",".",($time/$maxTime))."; ";
                          $valueStr.=str_replace(",",".",$offset+$scale*$value).",";
                          $valueStr.=str_replace(",",".",$offset+$scale*$this->Data[$modelica_name2][$time])."; ";
                          $cntTime=$this->Data['time'][$time];
                        }
                      }
                      $timeStr.="1.0";
                      $valueStr.=str_replace(",",".",$offset+$scale*$this->Data[$modelica_name1][$maxTime-2]).",";
                      $valueStr.=str_replace(",",".",$offset+$scale*$this->Data[$modelica_name2][$maxTime-2]);
                      break;
      case 'color' :
                      $valueStr.=str_replace(",",".",$this->colorScale($this->Data[$modelica_name1][0],$effect))."; ";
                      $cntTime=0.0;
                      foreach($this->Data[$modelica_name1] as $time=>$value)
                      {
                        if(($this->Data['time'][$time] - $cntTime)>=$sample_time)
                        {
                          $timeStr.=str_replace(",",".",($time/$maxTime))."; ";
                          $valueStr.=str_replace(",",".",$this->colorScale($value,$effect))."; ";
                          $cntTime=$this->Data['time'][$time];
                        }
                      }
                      $timeStr.="1.0";
                      $valueStr.=str_replace(",",".",$this->colorScale($this->Data[$modelica_name1][$maxTime-2],$effect));
                      break;
      case 'text' : break;
    }
    $M['svg_id']=$effect['svganimation_id'];
    $M['begin']="0";
    $M['dur']=$duration;
    $M['values']=$valueStr;
    $M['keyTimes']=$timeStr;
    return $M;
  }

  function readModifications($animation)
  {
    $animation_id=$animation['id'];
    $this->Modifications=array();
    $sql="SELECT 2deffects.* FROM 2deffects WHERE 2danimation_id='".$animation_id."' AND enabled=TRUE";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql."\n");
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $sql2="SELECT modelicaname FROM variables WHERE id='".$linea['variable_id']."'";
        $result2=mysqli_query($this->link,$sql2) or die(mysqli_error($this->link).": ".$sql2."\n");
        if($result2 and mysqli_num_rows($result2)>0)
        {
          $linea2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
          $linea['modelicaname1']=$linea2['modelicaname'];
        }
        $sql2="SELECT modelicaname FROM variables WHERE id='".$linea['variable_aux_id']."'";
        $result2=mysqli_query($this->link,$sql2) or die(mysqli_error($this->link).": ".$sql2."\n");
        if($result2 and mysqli_num_rows($result2)>0)
        {
          $linea2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
          $linea['modelicaname2']=$linea2['modelicaname'];
        }
        $M=$this->sync($linea,$animation);
        $this->Modifications[]=$M;
      }
    }    
  }

  function modify()
  {
    foreach($this->Modifications as $M)
    {
      foreach($this->tree as $type=>$node)
      {
        $this->modifyXml($this->tree[$type],$M);
        continue;
      }
    }  
  }

  function animateObject($animation,$results_file)
  {
    $resFile=$results_file;
    $C=new csvreader();
    $this->Data=$C->readCsv($resFile);
    $svgSource=$this->configurationSettings["unvlDir"]."/files/".$animation['model_id']."/graphics/".$animation['svg_file'];
    $this->tree=array();
    $this->readXmlFile($svgSource);
    $this->readModifications($animation);
    $this->modify();
//    print_r($this->tree);
  }

}

?>



