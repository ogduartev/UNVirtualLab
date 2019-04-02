<?php
require_once('block.php');
require_once('csvreader.php');

class tables extends block
{

  function showTable($fn)
  {
    $units=array();
    $f=file($fn);
    $str="";
    $cnt=0;
    foreach($f as $linea)
    {
      $linea=str_replace("\"","",$linea);
      $linea=str_replace(",","\t",$linea);
      if($cnt==0)
      {
        $units=$this->units($linea);
        $linea=str_replace(".","_",$linea);
        $cnt2=1;
        foreach($units as $u)
        {
          if(strlen($u)>0)
          {
            $linea.="(".$u.")";
          }
          if($cnt2 < count($units)){$linea.="\t";}else{$linea.="\n";}
          $cnt2++;
        }
      }else
      {
        $linea=str_replace(".",",",$linea);
      }
      $cnt++;
      $str.=$linea;
    }  
    $SS=serialize($str);
    echo "<div class='downdata'>\n";
    echo " <form method='post' action=send.php target='_new'>\n";
    echo "  <input class='downdata' type='submit' value='".$this->text('tables_Download')."'>\n";
    echo "  <input id='data' name='data' type='hidden' value='".$SS."'>\n";
    echo " </form>\n";
    echo "</div>\n";
    echo " <table class='outputs'>\n";
    $cnt=0;
    foreach($f as $linea)
    {
      echo "   <tr class='outputs'>\n";
      $linea=str_replace("\n","",$linea);
      $datos=explode(",",$linea);
      foreach($datos as $d)
      {
        if(strlen($d)<1){continue;}
        if(is_numeric($d))
        {
          echo "    <td class='outputs'>".number_format($d,3,".","")."</td>\n";
        }else
        {
          echo "    <td class='outputsTitle'>".str_replace("\"","",$d)."</td>\n";
        }
      }
      echo "   </tr>\n";
      $cnt++;
      if($cnt<2)
      {
        echo "   <tr class='outputs'>\n";
        foreach($units as $d)
        {
          if(strlen($d)>0)
          {
            echo "    <td class='outputsTitle'>(".str_replace("\"","",$d).")</td>\n";
          }else
          {
            echo "    <td class='outputsTitle'></td>\n";
          }
        }
        echo "   </tr>\n";      
      }
      $cnt++;
    }
    echo " </table>\n";
  }
  
  function units($csvLine)
  {
    $str="";
    $units=array();
    $vars=explode("\t",$csvLine);
    foreach($vars as $Var)
    {
      $sql="SELECT * FROM variables WHERE model_id='".$this->model_id()."' AND modelicaname='".$Var."'";
      $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
      if($result and mysqli_num_rows($result)>0)
      {
        while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
        {
          $units[$linea['modelicaname']]=$linea['units'];
        }
      }
    }
    return $units;
  }

  function display()
  {
    $model_id=$this->model_id();
    if($model_id==0 or !isset($_POST['res_file']))
    {
      return;
    }
    $this->showTable($_POST['res_file']);
  }
}

?>


