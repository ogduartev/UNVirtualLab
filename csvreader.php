<?php

class csvreader
{
  function readCsv($fn)
  {
    $data=array();
    $f=file($fn);
    $f=str_replace("\n",",\n",$f);
    $dataName=explode(',',str_replace('"','',$f[0]));
    foreach($dataName as $K=>$V)
    {
      $data[$V]=array();
    }
    $f=array_slice($f,1);
    foreach($f as $line)
    {
      $dataT=explode(",",$line);
      $line=str_replace("\n","",$line);
      foreach($dataT as $K=>$D)
      {
        $data[$dataName[$K]][]=$D;
      }
    }
    return $data;
   //   $data es un arreglo con los valores de cada variable
  }
  
  function readSync($fn)
  {
    $data=$this->readCsv($fn);
   //   $dataSync es un arreglo con variables vs time

    $dataSync=array();
    foreach($data as $K=>$V)
    {
      $DD=array();
      foreach($data['time'] as $KK=>$VV)
      {
        $DD[$VV]=$V[$KK];
      }
      $dataSync[$K]=$DD;
    }
    return $dataSync;
  }


}

?>
