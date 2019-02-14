<?php
require_once("es.inc");

$strings=strings();
foreach($strings as $K=>$V)
{
  $pos=strpos($K,"_");
  $str=substr($K,$pos+1);
  $str=str_replace("_"," ",$str);
  $strings[$K]=$str;
}

$strEn="<?php\n";
$strEn.="\n";
$strEn.="function strings()\n";
$strEn.="{\n";
foreach($strings as $K=>$V)
{
  $strEn.="  \$strings[\"".$K."\"]=\"".$V."\";\n";
}
$strEn.="\n";
$strEn.="  return \$strings;\n";
$strEn.="}\n";
$strEn.="\n";
$strEn.="?>\n";
echo $strEn;

?>
