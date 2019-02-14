<?
include("config.class.php");
 $conf = new configuration;
 
$creationarray = array(
	array("type" => "comment", "name" => "", "value" => "Class build by Thomas Schmidt"), // This is an out-marked comment
	array("type" => "empty", "name" => "", "value" => ""), // This is an emtpy line
	array("type" => "setting", "name" => "ipadress", "value" => "127.0.0.1"), // This is a new setting
	array("type" => "setting", "name" => "something", "value" => "true"),
);

$filename = "./filename.whatever";

$conf->writeconfig($filename, $creationarray); // Let's write the new file.

// Now it's time to change the config again.


$changearray = array(
	array("type" => "setting", "name" => "ipadress", "value" => "127.0.0.2"), // Let's change the ip adress
	array("type" => "empty", "name" => "", "value" => ""), // Here's an empty line again at the end of the current file
	array("type" => "comment", "name" => "", "value" => "Info: Neuer Value kommt hinzu :D"), // The next comment and ...
	array("type" => "setting", "name" => "something_else", "value" => "false"), // a new setting shall be written.
);


$conf->writeconfig($filename, $changearray); // Change the file again

$config = $conf->readconfig($filename); // Read the settings - they are set in an array

print_r($config);
?>