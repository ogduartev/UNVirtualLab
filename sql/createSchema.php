<?php
require_once("../config/config.class.php");

$username="root";
$userpass="rootpassword";

$C=new configuration();
$conf=$C->readconfig("../config/unvlconfig.txt");
$link=mysqli_connect($conf['DBserver'],$username,$userpass);

if($link)
{
  $sql="DROP DATABASE IF EXISTS ".$conf['DBname'];
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="CREATE DATABASE ".$conf['DBname'];
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $orden = "mysql -u ".$username." -p".$userpass." ".$conf['DBname']." < unvl.sql";
  passthru($orden);
//
  $sql="GRANT USAGE ON *.* TO '".$conf['DBuser']."'@'".$conf['DBserver']."';";
//  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="DROP USER '".$conf['DBuser']."'@'".$conf['DBserver']."' IF EXISTS;";
//  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="FLUSH PRIVILEGES;  ";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="CREATE USER '".$conf['DBuser']."'@'".$conf['DBserver']."' IDENTIFIED BY '".$conf['DBuserpass']."'";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="GRANT SELECT ON ".$conf['DBname'].".* TO '".$conf['DBuser']."'@'".$conf['DBserver']."'";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
//
  $sql="GRANT USAGE ON *.* TO '".$conf['DBadmin']."'@'".$conf['DBserver']."';";
//  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="DROP USER '".$conf['DBadmin']."'@'".$conf['DBserver']."' IF EXISTS;";
//  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="FLUSH PRIVILEGES;  ";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="CREATE USER '".$conf['DBadmin']."'@'".$conf['DBserver']."' IDENTIFIED BY '".$conf['DBadminpass']."'";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="GRANT SELECT ON ".$conf['DBname'].".* TO '".$conf['DBadmin']."'@'".$conf['DBserver']."'";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="GRANT INSERT ON ".$conf['DBname'].".* TO '".$conf['DBadmin']."'@'".$conf['DBserver']."'";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="GRANT UPDATE ON ".$conf['DBname'].".* TO '".$conf['DBadmin']."'@'".$conf['DBserver']."'";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="GRANT DELETE ON ".$conf['DBname'].".* TO '".$conf['DBadmin']."'@'".$conf['DBserver']."'";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
//
  $sql="USE ".$conf['DBname'];
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="INSERT INTO users(user_name,password) VALUES ('".$conf['UNVLadmin']."',SHA1(\"".$conf['UNVLadminpass']."\"))";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
  $sql="INSERT INTO sections(name,description,enabled) VALUES ('unvl','unvl',1)";
  mysqli_query($link,$sql) or die(mysqli_error($link).": ".$sql);
}
?>
