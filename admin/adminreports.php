<?php
require_once("admin/adminblock.php");

class adminreports extends adminblock
{

	function getDataByLine($line,$flagFin=false)
	{
		$d=explode(";",str_replace("\n","",$line));
		if(count($d)<4){return false;}
		$stamp     = strtotime($d[0]);
		$date      = getdate($stamp);
		$month     =  $date["year"]."-";if($date["mon"]<10){$month.="0";}$month.=$date["mon"];
		$event     = str_replace(" Event: "    ,"",$d[1]);
		$model_id  = str_replace(" Model id: " ,"",$d[2]);
		$ip        = str_replace(" Remote IP: ","",$d[3]);
		$pais      = $this->getPais($ip);

		if(!$flagFin and $event=="Final de simulación "){return false;}
		
		$data=array("time"      => $month,
		            "model_id"  => $model_id,
		            "país"      => $pais);
		
		return $data;
	}

	function getPais($ip)
	{
		$orden="geoiplookup ".$ip;
		$l=exec($orden);
		$p=str_replace("GeoIP Country Edition: ","",$l);
		$p=str_replace("\n","",$p);
		$pos=strpos($p,", ")+2;
		$pais=trim(substr($p,$pos));
		return $pais;
	}

	function arrayMonth($y1,$m1,$y2,$m2)
	{
		$A=array("Total"=>0);
		
		for($y=$y1;$y<=$y2;$y++)
		{
		  $mi=1;
		  $mf=12;
			if($y==$y1){$mi=$m1;}
			if($y==$y2){$mf=$m2;}
			for($m=$mi;$m<=$mf;$m++)
			{
			  $ms="";if($m<10){$ms.="0";}$ms.=$m;
				$A[$y."-".$ms]=0;
			}
		}
		
		return $A;
	}

	function csvByModelMonth($stats,$fn)
	{
		$sep="\t";
		$str="id\tNombre";
		$periodos=array();
		foreach($stats["Total"] as $periodo=>$dato)
		{
		  if(!($periodo=="Total"))
		  {
		  	$periodos[]=$periodo;
		  }
		}
		sort($periodos);
		foreach($periodos as $periodo)
		{
			$str.=$sep.$periodo;
		}
		$str.=$sep."Total\n";
		foreach($stats as $model_id=>$datos)
		{			
		  if(!($model_id=="Total") and !($model_id==""))
		  {
				$modelname="";
				$sql="SELECT name FROM models WHERE id=".$model_id;
				$result=mysqli_query($this->link,$sql) or die(mysqli_error().": ".$sql);
				if($result and mysqli_num_rows($result)>0)
				{
				  $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
				  $modelname=$linea['name'];
				}
				
		  	$str.=$model_id."\t\"".$modelname."\"";
				foreach($periodos as $periodo)
				{
					$str.=$sep.$datos[$periodo];
				}
			  $str.=$sep.$datos["Total"]."\n";
		  }
		}
		
		$str.="Total";
		$str.=$sep;
		$str.="\"\"";
		foreach($periodos as $periodo)
		{
			$str.=$sep.$stats["Total"][$periodo];
		}
	  $str.=$sep.$stats["Total"]["Total"]."\n";
		$f=fopen($fn,"w");
		fwrite($f,$str);
		fclose($f);
	}


	function csvByCountryMonth($stats,$fn)
	{
		$sep="\t";
		$str="País";
		$periodos=array();
		foreach($stats["Total"] as $periodo=>$dato)
		{
		  if(!($periodo=="Total"))
		  {
		  	$periodos[]=$periodo;
		  }
		}
		sort($periodos);
		foreach($periodos as $periodo)
		{
			$str.=$sep.$periodo;
		}
		$str.=$sep."Total\n";
		foreach($stats as $country=>$datos)
		{			
		  if(!($country=="Total") and !($country==""))
		  {
				$str.=$country;
				foreach($periodos as $periodo)
				{
					$str.=$sep.$datos[$periodo];
				}
			  $str.=$sep.$datos["Total"]."\n";
		  }
		}
		
		$str.="Total";
		foreach($periodos as $periodo)
		{
			$str.=$sep.$stats["Total"][$periodo];
		}
	  $str.=$sep.$stats["Total"]["Total"]."\n";
		$f=fopen($fn,"w"); 
		fwrite($f,$str);
		fclose($f);
	}

	function byKeyMonth($key,$fn,$y1,$m1,$y2,$m2,$fn2)
	{
		$stats=array("Total"=>$this->arrayMonth($y1,$m1,$y2,$m2));
		$f = fopen($fn,"r");
		while(! feof($f))  
		{
			$line = fgets($f); 
			$data=$this->getDataByLine($line);
			if(is_array($data))
			{
			  if(!isset($stats[$data[$key]]))
			  {
 			  	$stats[$data[$key]]=$this->arrayMonth($y1,$m1,$y2,$m2);
			  }
			  if(isset($stats[$data[$key]][$data["time"]]))
			  {
				  $stats[$data[$key]][$data["time"]]++;
			  	$stats[$data[$key]]["Total"]++;
				  $stats["Total"][$data["time"]]++;
				  $stats["Total"]["Total"]++;
			  }
			}
		}
		ksort($stats);
		return $stats;
	}

	function byModelMonth($fn,$y1,$m1,$y2,$m2,$fn2)
	{
		$stats=$this->byKeyMonth("model_id",$fn,$y1,$m1,$y2,$m2,$fn2);
		$this->csvByModelMonth($stats,$fn2);
	}

	function byCountryMonth($fn,$y1,$m1,$y2,$m2,$fn2)
	{
		$stats=$this->byKeyMonth("país",$fn,$y1,$m1,$y2,$m2,$fn2);
		$this->csvByCountryMonth($stats,$fn2);
	}

  function showTable($fn,$sep)
  {
    $units=array();
    $f=file($fn);
    $str="";
    $cnt=0;
    foreach($f as $linea)
    {
      $linea=str_replace("\"","",$linea);
      $linea=str_replace(",","\t",$linea);
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
      $datos=explode($sep,$linea);
      foreach($datos as $d)
      {
        if(strlen($d)<1){continue;}
        if(is_numeric($d))
        {
          echo "    <td class='outputs'>".number_format($d,0,".","")."</td>\n";
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
  
  function updateCsv()
  {
    $iniM=4;
    $iniY=2019;
    $finM=10;
    $finY=2020;
    if(isset($_POST['adminreport_ini_month'])){$iniM=$_POST['adminreport_ini_month'];}
    if(isset($_POST['adminreport_ini_year']) ){$iniY=$_POST['adminreport_ini_year']; }
    if(isset($_POST['adminreport_fin_month'])){$finM=$_POST['adminreport_fin_month'];}
    if(isset($_POST['adminreport_fin_year']) ){$finY=$_POST['adminreport_fin_year']; }

		$dir=$this->configurationSettings['unvlDir']."/logs/";
		$fn=$dir."unvl.log";
		
		$this->byModelMonth  ($fn,$iniY,$iniM,$finY,$finM,$dir."byModelMonth.csv");
		$this->byCountryMonth($fn,$iniY,$iniM,$finY,$finM,$dir."byCountryMonth.csv");
  }

  function displayTables()
  {
    $fn=$this->configurationSettings["unvlDir"]."/logs/byModelMonth.csv";
    echo "        <div class='admin_reports_table_model'>\n";
    if(file_exists($fn))
    {
      echo date ("Y/m/d", filemtime($fn));
      $this->showTable($fn,"\t");
    }
    echo "        </div>\n";

    $fn=$this->configurationSettings["unvlDir"]."/logs/byCountryMonth.csv";
    echo "  <div class='admin_reports_table_country'>\n";
    if(file_exists($fn))
    {
      echo date ("Y/m/d", filemtime($fn));
      $this->showTable($fn,"\t");
    }
    echo "  </div>\n";
  }
  
  function displayControls()
  {
    echo "        <div class='admin_reports_controls'>\n";
    echo "         <form method='POST' action='admin.php'>\n";
    echo "         <span>".$this->text('adminreports_Initial_month')."</span>\n";
    echo "          <select class='admin_report_month' name='adminreport_ini_month'>\n";
    for($i=1;$i<13;$i++)
    {
      $selected="";
      if(isset($_POST['adminreport_ini_month']) and $_POST['adminreport_ini_month']==$i){$selected="selected";}
      echo "            <option ".$selected.">".$i."\n";
    }
    echo "          </select>";
    echo "         <span>".$this->text('adminreports_Initial_year')."</span>\n";
    echo "          <select class='admin_report_month' name='adminreport_ini_year'>\n";
    for($i=2019;$i<2030;$i++)
    {
      $selected="";
      if(isset($_POST['adminreport_ini_year']) and $_POST['adminreport_ini_year']==$i){$selected="selected";}
      echo "            <option ".$selected.">".$i."\n";
    }
    echo "          </select>";
   echo "         <span>".$this->text('adminreports_Final_month')."</span>\n";
    echo "          <select class='admin_report_motnh' name='adminreport_fin_month'>\n";
    for($i=1;$i<13;$i++)
    {
      $selected="";
      if(isset($_POST['adminreport_fin_month']) and $_POST['adminreport_fin_month']==$i){$selected="selected";}
      echo "            <option ".$selected.">".$i."\n";
    }
    echo "          </select>";
    echo "         <span>".$this->text('adminreports_Final_year')."</span>\n";
    echo "          <select class='admin_report_motnh' name='adminreport_fin_year'>\n";
    for($i=2019;$i<2030;$i++)
    {
      $selected="";
      if(isset($_POST['adminreport_fin_year']) and $_POST['adminreport_fin_year']==$i){$selected="selected";}
      echo "            <option ".$selected.">".$i."\n";
    }
    echo "          </select>";
    echo "         <span><input type='submit' name='reportupdate' class='admin_button' value='".$this->text('adminreports_Update')."'></span>\n";
    echo "         <span><input type='submit' name='loginsubmit' class='admin_button' value='".$this->text('adminreports_Return')."'></span>\n";
    echo "         <input type='hidden' name='reportsubmit' class='admin_button' value='reportsubmit'>\n";
    echo "         </form>\n";
    echo "        </div>\n";
  }
  
  function display()
  {
    if(isset($_POST['reportupdate']))
    {
      $this->updateCsv();
    }else
    {
      $_POST['adminreport_ini_month']=4;
      $_POST['adminreport_ini_year']=2019;
      $_POST['adminreport_fin_month']=date("n");
      $_POST['adminreport_fin_year']=date("Y");
    }
    $this->displayTables();
    $this->displayControls();
  }


}
?>
