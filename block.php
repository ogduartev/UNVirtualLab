<?php
require_once('xmlthing.class.php');
require_once('config/config.class.php');

class block
{
  var $Xml;
  var $strings;
  var $configurationSettings;
  var $link;

  function block()
  {
    session_start();
    $conf = new configuration();
    $this->configurationSettings=$conf->readconfig('unvlconfig.txt');
    require_once("locale/".$this->configurationSettings['locale'].".inc");
    $this->strings=strings();
  }

  function connect()
  {
    $this->link=NULL;
    $username="";
    $userpass="";
    if(isset($_SESSION) and isset($_SESSION['UNVL_SESSION_UNVL']))
    {
      $username=$this->configurationSettings['DBadmin'];
      $userpass=$this->configurationSettings['DBadminpass'];
    }else
    {
      $username=$this->configurationSettings['DBuser'];
      $userpass=$this->configurationSettings['DBuserpass'];
    }
    $this->link=mysqli_connect($this->configurationSettings['DBserver'],$username,$userpass);
    if(!$this->link)
    {
      echo "A".$this->text('about_No_Database_connection');
      return FALSE;
    }else
    {
      $sql="USE ".$this->configurationSettings['DBname'];
      mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
      return $this->link;
    }
    return FALSE;
  }

  function disconnect()
  {
    if($this->link)
    {
      mysqli_close($this->link);
    }
  }
  
  function text($str)
  {
    if(isset($this->strings[$str]))
    {
      return $this->strings[$str];
    }else
    {
      return $str;
    }
  }

  function validateValue($type,$par_id,$new_value,$default_value)
  {
    if(isset($_POST['reset'])){return $default_value;}
    switch($type)
    {
      case 'Integer':
                    if(is_numeric($new_value) and strpos($new_value,".")===false and strpos($new_value,",")===false )
                    {
                      return $new_value;
                    }else
                    {
                      return $default_value;
                    }
                    break;
      case 'Real'   :
                    if(is_numeric($new_value))
                    {
                      return $new_value;
                    }else
                    {
                      return $default_value;
                    }
                    break;
      case 'Boolean':
                    if($new_value=='true' or $new_value=='false')
                    {
                      return $new_value;
                    }else
                    {
                      return $default_value;
                    }
                    break;
      default: return $new_value;
    }
  }
  
  function opener()
  {
    echo "<html>\n";
    echo " <head>\n";
    echo "  <title>\n";
    echo "  </title>\n";
    echo "  <meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\" />\n";
    echo "  <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/".$this->configurationSettings['theme']."/style.css\" />\n";
    echo "  <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/".$this->configurationSettings['theme']."/styleSVG.css\" />\n";
    echo "  <link rel=\"shortcut icon\" href=\"themes/".$this->configurationSettings['theme']."/img/favicon.gif\" />\n";
    echo "  <script type=\"text/javascript\" src=\"jscolor/jscolor.js\"></script>\n";
    $fHtml="themes/".$this->configurationSettings['theme']."/head.html";
    if(file_exists($fHtml))
    {
       $f=file($fHtml);
       foreach($f as $linea)
       {
         $linea=str_replace("href=\"","href=\"themes/".$this->configurationSettings['theme']."/",$linea);
         $linea=str_replace("src=\"js","src=\"themes/".$this->configurationSettings['theme']."/js",$linea);
         echo $linea;
       }
    }
    echo " </head>\n";
  }

  function closer()
  {
    echo "</html>\n";
  }

  function htmlSimpleOpen($Xml,$nivel)
  {
    for($i=0;$i<$nivel;$i++){echo "  ";}
    echo "<".$Xml['type']." id='".$Xml['id']."' class='".$Xml['class']."'>\n";
    if(isset($Xml['action']))
    {
      $X=new $Xml['action']();
      if($this->connect())
      {
        $X->connect();
        if($X->link)
        {
          $X->display();
          $X->disconnect();
        }
        $this->disconnect();
      }
    }
    return;    
  }

  function htmlSimpleClose($Xml,$nivel)
  {
    for($i=0;$i<$nivel;$i++){echo "  ";}
    echo "</".$Xml['type'].">\n";
    return;    
  }

  function htmlBlock($Xml,$nivel)
  {
    if(!is_array($Xml)){return;}
    if(isset($Xml['attributes'])){$this->htmlSimpleOpen($Xml['attributes'],$nivel);}
    foreach($Xml as $K=>$bl)
    {
      $this->htmlBlock($bl,$nivel+1);
    }
    if(isset($Xml['attributes'])){$this->htmlSimpleClose($Xml['attributes'],$nivel);}
  }

  function html($xmlFN)
  {
    $this->readXml($xmlFN);
    $this->opener();
    $this->htmlBlock($this->Xml['block'],0);
    $this->closer();
  }
  
  function readXml($fn)
  {
    $xmlstr=file_get_contents($fn,FILE_TEXT);
    $xml = new XMLThing($xmlstr);
    $this->Xml = $xml->parse(); 
  }
  
  function enabled($table)
  {
    $str="";
    session_start();
    if(!isset($_SESSION['UNVL_SESSION_UNVL']))
    {
      $str.=" AND ".$table.".enabled=1";
    }
    return $str;
  }
  
  function x_id($key)
  {
    if(isset($_GET[$key]))
    {
      return $_GET[$key];
    }elseif(isset($_POST[$key]))
    {
      return $_POST[$key];
    }
    return 0;
  }
  
  function modeller_id()
  {
    return $this->x_id('modellerid');
  }
  
  function c2deffect_id()
  {
    return $this->x_id('2deffectid');
  }
  
  function c2danimation_id()
  {
    return $this->x_id('2danimationid');
  }
  
  function control_id()
  {
    return $this->x_id('controlid');
  }
  
  function practice_id()
  {
    return $this->x_id('practiceid');
  }
  
  function control_group_id()
  {
    return $this->x_id('controlgroupid');
  }
  
  function curve_id()
  {
    return $this->x_id('curveid');
  }
  
  function plot_id()
  {
    return $this->x_id('plotid');
  }
  
  function model_id()
  {
    return $this->x_id('modelid');
  }
  
  function section_id()
  {
    return $this->x_id('sectionid');
  }
  
  function display()
  {
    $model_id=$this->model_id();
    echo $model_id;
  }

  function logMessage($flag)
  {
    $logEnabled=$this->configurationSettings["logEnabled"];
    if(!(strtoupper($logEnabled)=="TRUE")){return;}
    
    $dirLog=$this->configurationSettings["unvlDir"]."/logs/";

    $strCase="Event: ";
    switch($flag)
    {
      case 0  : $strCase.=$this->text("loggin_Simulation_Starts");break;
      case 1  : $strCase.=$this->text("loggin_Simulation_Ends");break;
      default : $strCase.="NN";break;
    }
    
    $strDate = date("Y-m-d h:m:s.ms");
    $strModel = "Model id: ".$_POST['modelid'];
    $strRemote= "Remote IP: ".$_SERVER['REMOTE_ADDR'];
    
    $sep="; ";
    $logString=$strDate.$sep.$strCase.$sep.$strModel.$sep.$strRemote."\n";
  
    file_put_contents($dirLog."unvl.log",$logString,FILE_APPEND);  
    
    $maxSize=0+$this->configurationSettings["logMaxSize"];
    if(filesize($dirLog."unvl.log")>$maxSize)
    {
      $g=glob($dirLog."unvl.log.*");
      $n=count($g)+1;
      $fnDest=$dirLog."unvl.log.".sprintf("%04d",$n);
      copy($dirLog."unvl.log",$fnDest);
      unlink($dirLog."unvl.log");
    }
  }
}
?>
