<?php
require_once('block.php');
require_once('csvreader.php');

class SVGplot extends block
{
  var $svgStr="";

  var $totalWidth=300;
  var $totalHeight=200;

// dims (%)
  var $titleHeight=14;
  var $plotHeight=76;
  var $legendHeight=5;
  var $framePositionX=20;
  var $framePositionY=5;
  var $frameWidth=75;
  var $frameHeight=85;
  var $tagHeight=10;
  var $tagWidth=20;
  var $legendSeparation=5;
  var $legendXseparation=15;
  
  var $legendSeparationYFactor=0.75;
  

// data
  var $title="";
  var $description="";
  var $gridX=5;
  var $gridY=3;
  var $autoscaleX='1';
  var $autoscaleY='1';
  var $minX=0.0;
  var $maxX=1.0;
  var $minY=0.0;
  var $maxY=1.0;
  var $data;
  var $colors;
  var $legends;
  var $units;
  var $descriptions;
  var $legendH="";
  var $unitsH="";

  function SVGplot()
  {
    $this->block();
    $this->totalWidth=$this->configurationSettings['plotWidth'];
    $this->totalHeight=$this->configurationSettings['plotHeight'];
    $this->data=array();
    $this->colors=array();
  }
  
  function settings($settings)
  {
    $this->title=$settings['name'];
    $this->description=$settings['description'];
    $this->minX=$settings['minX'];
    $this->maxX=$settings['maxX'];
    $this->minY=$settings['minY'];
    $this->maxY=$settings['maxY'];
    $this->gridX=$settings['gridX'];
    $this->gridY=$settings['gridY'];
    $this->autoscaleX=$settings['autoscaleX'];
    $this->autoscaleY=$settings['autoscaleY'];
  }
  
  function title()
  {
    $this->svgStr.="  <svg width='".$this->frameWidth."%' height='".$this->titleHeight."%' x='".$this->framePositionX."%'>\n";
    $strTmp;
    $this->svgStr.="    <text class='title' x='50%' y='85%' text-anchor='middle'>".$this->title."</text>\n";
    $this->svgStr.="  </svg>\n";
  }
  
  function grid()
  {
    for($i=1;$i<$this->gridX;$i++)
    {
      $X=$i*100/($this->gridX);
      $this->svgStr.="    <line class='grid' x1='".$X."%' y1='0%' x2='".$X."%' y2='100%'/>\n";
    }
    for($i=1;$i<$this->gridY;$i++)
    {
      $Y=$i*100/($this->gridY);
      $this->svgStr.="    <line class='grid' y1='".$Y."%' x1='0%' y2='".$Y."%' x2='100%'/>\n";
    }  
  }
  
  function curves()
  {
    foreach($this->data as $K=>$curve)
    {
      $sx=$this->totalWidth*($this->frameWidth/100)/100;
      $sy=$this->totalHeight*($this->frameHeight/100)*($this->plotHeight/100)/100;
      $this->svgStr.="    <g transform='scale(".$sx.",".$sy.")'>\n";
      $RGB=$this->colors[$K];
      $this->svgStr.="     <path class='curve' style='stroke:".$this->colors[$K].";' d=\"";
      $flag=0;
      foreach($curve["x"] as $KK=>$x)
      {
        $y=$curve["y"][$KK];
        if($flag==0)
        {
          $flag=1;
          $this->svgStr.="M";
        }else
        {
          $this->svgStr.="L";
        }
        $this->svgStr.=number_format(($x-$this->minX)/($this->maxX-$this->minX)*100,2);
        $this->svgStr.=" ";
        $this->svgStr.=number_format(100 - ($y-$this->minY)/($this->maxY-$this->minY)*100,2);
        $this->svgStr.=" ";
      }
      $this->svgStr.="\"/>\n";
      $this->svgStr.="    </g>\n";
    }
  }
  
  function frame()
  {
    $this->svgStr.="   <svg  x='".$this->framePositionX."%' y='".$this->framePositionY."%' width='".$this->frameWidth."%' height='".$this->frameHeight."%'>\n";
    $this->svgStr.="     <rect class='frame' x='0' y='0' width='100%' height='100%' />\n";
    $this->grid();
    $this->curves();
    $this->svgStr.="   </svg>\n";
  }
  
  function strNum($x)
  {
    return number_format($x,2);
  }
  
  function tagH()
  {
    $this->svgStr.="   <svg  x='0%' y='".(100-1*$this->tagHeight)."%' width='100%' height='".$this->tagHeight."%'>\n";
    for($i=0;$i<=$this->gridX;$i++)
    {
      $X=$this->framePositionX+$i*($this->frameWidth)/($this->gridX);
      $value=$this->strNum($this->minX+($this->maxX-$this->minX)*$i/($this->gridX));
      $this->svgStr.="    <text class='tagH' x='".$X."%' y='100%' text-anchor='middle' >".$value."</text>\n";
    }
    $this->svgStr.="   </svg>\n";
  }

  function tagV()
  {
    $this->svgStr.="   <svg  x='0%' y='0%' width='".$this->tagWidth."%' height='100%'>\n";
    for($i=0;$i<=$this->gridY;$i++)
    {
      $Y=($this->framePositionY)+$i*($this->frameHeight)/($this->gridY);
      $value=$this->strNum($this->minY+($this->maxY-$this->minY)*($this->gridY-$i)/($this->gridY));
      $this->svgStr.="    <text class='tagV' x='80%' y='".$Y."%' text-anchor='end' >".$value."</text>\n";
    }
    $this->svgStr.="   </svg>\n";
  }

  function plot()
  {
    $this->svgStr.="  <svg x='0%' y='".$this->titleHeight."%' width='100%' height='".$this->plotHeight."%'>\n";
    $this->frame();
    $this->tagH();
    $this->tagV();
    $this->svgStr.="  </svg>\n";
  }
  
  function legend()
  {
    $this->svgStr.="  <svg x='0%' y='".($this->plotHeight+$this->titleHeight+$this->legendSeparation)."%' width='100%' height='".(100.0-$this->plotHeight-$this->titleHeight-+$this->legendSeparation)."%'>\n";
//    $this->svgStr.="   <rect fill='red' x='0' y='0' width='100%' height='100%' />\n";
    $cnt=0;
    foreach($this->colors as $str=>$rgb)
    {
      $strTmp=$this->legends[$str];
      $strTmp.=" : (".$this->units[$str].") vs (".$this->unitsH.")";
      $this->svgStr.="   <text class='legend' title='".$this->descriptions[$str]."' y='".(($cnt+0.5)*100/(count($this->legends)/$this->legendSeparationYFactor+0))."%' x='".$this->legendXseparation."%' style='fill:".$rgb.";text-anchor: top;;'>".$strTmp."</text>";
      $cnt++;
    }
    $this->svgStr.="  </svg>\n";
  }
  
  function display()
  {
    $this->plotHeight-=count($this->legends)*$this->legendHeight;
    $this->svgStr.="<svg class='plot' title='".$this->description."' id='MyPlot' width='".$this->totalWidth."px' height='".$this->totalHeight."px'>\n";
    $this->svgStr.="  <rect class='plot' x='0' y='0' width='100%' height='100%' rx='5%' ry='5%' />\n";
    $this->title();
    $this->plot();
    $this->legend();
    $this->svgStr.="</svg>\n";
    echo $this->svgStr;
  }
  
  function data($plot,$resfile)
  {
    $this->data=array();
    $this->colors=array();
    $this->legends=array();
    $this->units=array();
    $this->legendH="";
    $this->unitH="";
    $sql="SELECT variables.modelicaname AS MN,variables.units AS UN, plots.description AS DC
                 FROM plots INNER JOIN variables ON plots.variable_id=variables.id WHERE plots.id='".$plot['id']."'";
    $result=mysqli_query($this->link,$sql) or die(mysql_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      $this->legendH=$linea['MN'];
      $this->unitsH=$linea['UN'];
      $this->description=$linea['DC'];
    }
    $sql="SELECT curves.*,variables.modelicaname AS MN,variables.units AS UN,curves.description AS DC
                 FROM curves INNER JOIN variables ON curves.variable_id=variables.id WHERE plot_id='".$plot['id']."'";
    $sql.=$this->enabled("curves");
    $result=mysqli_query($this->link,$sql) or die(mysql_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $csv=new csvreader();
      $data=$csv->readCsv($resfile);
      $x=$data[$plot['MN']];
      $x=array_slice($x,$plot['firstdata']);
      $mnY=array();
      $mxY=array();
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $y=$data[$linea['MN']];
        $y=array_slice($y,$plot['firstdata']);
        
        $xy=array();
        $xy["x"]=$x;
        $xy["y"]=$y;
        
        $this->data[$linea['MN']]=$xy;
        $this->colors[$linea['MN']]="#".$linea['colorRGB'];
        $this->legends[$linea['MN']]=$linea['legend'];
        $this->units[$linea['MN']]=$linea['UN'];
        $this->descriptions[$linea['MN']]=$linea['DC'];
        if($this->autoscaleY==1)
        {
          $mnY[]=min($y);
          $mxY[]=max($y);
        }
      }
      //autoscale
      if($this->autoscaleX==1)
      {
        $this->minX=min($x);
        $this->maxX=max($x);
      }
      if($this->autoscaleY==1)
      {
        $this->minY=min($mnY);
        $this->maxY=max($mxY);
      }
    }
  }
}

?>
