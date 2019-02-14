<?php
class configuration {
 public function readconfig($configfile) {
  $realfile = $this->getfiledir($configfile);
  $fp = fopen($realfile, "r");
  if($fp == false) {
   return false;
  }
  else {
   $config = array();
   while(!feof($fp)) {
    $row = trim(fgets($fp));
	if(substr($row, 0, 1) == ":") {
	 $newrow = substr($row, 1);
	 $parted = explode("=", $newrow, 2);
//	 $settingname = strtolower(trim($parted[0])); // DUARTE: respetar caps
	 $settingname = trim($parted[0]);
	 $settingself = trim($parted[1]);
	 $config[$settingname] = $settingself;
	}
   }
   return $config;
  }
 }
 
 public function writeconfig($configfile, $changes) {
  $realfile = $this->getfiledir($configfile);
  
  if(file_exists($realfile)) {

   $fp = fopen($realfile, "r");
   if($fp == false) {
    return false;
   }
   else {
    $config = array();
    while(!feof($fp)) {
     $row = trim(fgets($fp));
	 if($row == "") {
	  $entry = array("type" => "empty", "value" => "");
	 }
	 elseif(substr($row, 0, 1) == "#") {
	  $comment = substr($row, 1);
	  $entry = array("type" => "comment", "value" => $comment);
	 }
	 elseif(substr($row, 0, 1) == ":") {
	  $newrow = substr($row, 1);
	  $parted = explode("=", $newrow, 2);
//	  $settingname = strtolower(trim($parted[0])); // DUARTE : respetar caps
	  $settingname = trim($parted[0]);
	  $settingself = trim($parted[1]); 
	  $entry = array("type" => "setting", "name" => $settingname, "value" => $settingself);
 	 }
	 $config[] = $entry;
	 unset($entry, $comment, $settingname, $settingself);
    }
	print_r($config);
	
	$added = array();
	$ii = 0;
	foreach($config AS $entry) {
	 if($entry['type'] == "setting") {
	  $i = 0;
	  foreach($changes AS $change) {
	   if($entry['name'] == $change['name']) {
	    $entry['value'] = $change['value'];
		$added[] = $i;
		$config[$ii] = $entry;
	   }
	   $i++;
	  }
	 }
	 $ii++;
	}
	foreach($added AS $added) {
	 unset($changes[$added]);
	}
	foreach($changes AS $change) {
	 $config[] = $change;
	}
	unlink($realfile);
	$fp = fopen($realfile, "w+");
    if($fp == false) {
     return false;
    }
    else {
	 $i = 0;
     foreach($config AS $array) {
	  if($array['type'] == "comment") {
	   $output = "#".$array['value'];
	  }
	  elseif($array['type'] == "empty") {
	   $output = "";
	  }
	  elseif($array['type'] == "setting") {
	   $output = ":".$array['name']." = ".$array['value'];
	  }
	  if($i == 0) {
	   fwrite($fp, $output);
	  }
	  else {
	   fwrite($fp, chr(10).$output);
	  }
	  $i++;
	 } 
	 fclose($fp);
 	 return true;
    }
	
   }
  
  }
  else {
   $fp = fopen($realfile, "w+");
   if($fp == false) {
    return false;
   }
   else {
	$i = 0;
    foreach($changes AS $array) {
	 if($array['type'] == "comment") {
	   $output = "#".$array['value'];
	  }
	 elseif($array['type'] == "empty") {
	  $output = "";
	 }
	 elseif($array['type'] == "setting") {
	  $output = ":".$array['name']." = ".$array['value'];
	 }
	 if($i == 0) {
	  fwrite($fp, $output);
	 }
	 else {
	  fwrite($fp, chr(10).$output);
	 }
	 $i++;
	} 
	fclose($fp);
 	return true;
   }
   
  }
  
 }
 
 private function getfiledir($configfile) {
  if(substr($configfile, 0, 1) == "/") {
  }
  elseif(substr($configfile, 0, 2) == "./") {
   $newconfig = substr($configfile, 1);
   $configfile = dirname(__FILE__).$newconfig;
  }
  else {
   $configfile = dirname(__FILE__)."/".$configfile;
  }
  return $configfile;
 }
 
}
?>
