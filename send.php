<?php
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="data.csv"');

$data=$_POST['data'];

$pos=strpos($data,"\"")+1;
$data=substr($data,$pos);
$data=str_replace('";','',$data);
echo $data;
?>
