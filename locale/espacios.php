<?php

function espacios($fn)
{
  echo $fn."\n";
  $lineas=file($fn);
  $posmax=0;
  foreach($lineas as $l)
  {
    $pos=strpos($l,"=");
    if($pos>$posmax){$posmax=$pos;}
  }
  $posmax+=2;
  $str="";
  foreach($lineas as $l)
  {
    $pos=strpos($l,"=");
    if($pos>0)
    {
      $str1=substr($l,0,$pos);
      $str3="= ".trim(substr($l,$pos+1,strlen($l)));
      $str2="";
      for($i=$pos;$i<=$posmax;$i++){$str2.=" ";}
      $l=$str1.$str2.$str3."\n";
    }
    $str.=$l;
  }
  $f=fopen($fn,"w");
  fwrite($f,$str);
  fclose($f);
}
$g=glob("*.inc");
foreach($g as $fn)
{
  espacios($fn);
}
?>
