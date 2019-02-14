<?php
require_once("block.php");

class MyXml extends block
{
  var $tree;
  function readXmlFile($fn)
  { 
    $xmlstr=file_get_contents($fn,FILE_TEXT);
    $this->readXmlString($xmlstr);
  }
  
  function readXmlString($xmlstr)
  {
    $xml = new XMLThing($xmlstr);
    $this->tree = $xml->parse(); 
  }

  function tab($tab)
  {
    $str="";
    for($i=0;$i<$tab;$i++)
    {
      $str.="  ";
    }
    return $str;
  }
  
  function writeXmlFile($fn)
  {
    $str=$this->writeXmlString();
    $f=fopen($fn,"w");
    fwrite($f,$str);
    fclose($f);
  }

  function writeXmlString()
  {
    $str="";
    $str.="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
    if(is_array($this->tree))
    {
      foreach($this->tree as $type=>$node)
      {
        $str.=$this->writeNodeString($type,$node,0);
        continue;
      }
    }
    return $str;
  }

  function cleanString($strin)
  {
    $str=$strin;
    $str=str_replace("\"","\\\"",$str);
    $str=str_replace("<","&lt;",$str);
    $str=str_replace(">","&gt;",$str);
    return $str;
  }

  function writeNodeAttributes($node,$tab)
  {
    $str="";
    if(isset($node['attributes']) and is_array($node['attributes']))
    {
      $str.="\n";
      foreach($node['attributes'] as $att=>$value)
      {
        $str.=$this->tab($tab+1);
        $str.=$att."=\"".$this->cleanString($value)."\"\n";
      }
      if(substr($str,strlen($str)-1)=="\n")
      {
        $str=substr($str,0,strlen($str)-1);
      }
    }
    return $str;
  }
  
  function writeNodeValue($node)
  {
    $str="";
    if(isset($node['value']) and strlen($node['value'])>0)
    {
      $str.=$this->cleanString($node['value']);
      if(substr($str,strlen($str)-1)=="\n")
      {
        $str=substr($str,0,strlen($str)-1);
      }
    }
    return $str;
  }
  
  function writeNodeString($type,$node,$tab)
  {
    $str="";
    if(is_array($node) and array_key_exists(0,$node))
    {
      foreach($node as $subnode)
      {
        $str.=$this->writeNodeString($type,$subnode,$tab);
      }
      return $str;
    }
    $str.=$this->tab($tab);
    $str.="<".$type;
    $str.=$this->writeNodeAttributes($node,$tab);
    $str.=">";
    $str.=$this->writeNodeValue($node);
    if(is_array($node))
    {
      foreach($node as $subtype=>$subnode)
      {
        if($subtype!="attributes" and $subtype!="value")
        {
          $str.=$this->writeNodeString($subtype,$subnode,$tab+1);
        }
      }
    }
    $str.=$this->tab($tab);
    $str.="</".$type.">\n";
    return $str;
  }
  
  function insertNode(&$node,$type,$subnode)
  {
    if(!array_key_exists($type,$node))
    {
      $node[$type]=$subnode;
      return;
    }
    if(array_key_exists(0,$node[$type]))
    {
      $node[$type][]=$subnode;
      return;
    }
    $sn=$node[$type];
    unset($node[$type]);
    $node[$type]=array();
    $node[$type][]=$sn;
    $node[$type][]=$subnode;
  }
  
  function removeNode(&$node,$type,$subnode)
  {
    if(!array_key_exists($type,$node))
    {
      return;
    }  
    if(array_key_exists(0,$node[$type]))
    {
      unset($node[$type][$subnode]);
      return;
    }
    unset($node[$type]);
  }
}

/*
$X=new MyXml();
$X->readXmlFile("./files/1/graphics/sensores2.svg",FILE_TEXT);
print_r($X->tree);

//$X->readXmlFile("test.xml");

$n=$X->tree['a'];
$sn=$X->tree['a']['d'];
$X->insertNode($n,'d',$sn);
print_r($n);
//print_r($X->tree);
$X->writeXmlFile("testWrite.xml");
*/
?>
