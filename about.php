<?php
require_once('block.php');

class abouttable extends block
{
  function display()
  {
    $this->displayModel();
    $this->displayModellers();
    $this->displayFiles();
    $this->displayEmbedLinks();
    $this->displayPractices();
  }

  function displayModel()
  {
    $model_data=array("about_Model_name" => "name",
                      "about_Model_description" => "description",
                      "about_Model_theory" => "bibliography");
    
    $sql="SELECT * FROM models WHERE id='".$this->model_id()."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      echo " <tr>\n";
      echo "  <th colspan=2 class='about_title'>".$this->text('about_Model_information')."</th>\n";
      echo " </tr>\n";
      foreach($model_data as $K=>$V)
      {
        echo " <tr>\n";
        echo "  <td class='about_name'>".$this->text($K)."</td>\n";
        echo "  <td class='about_value'>".$linea[$V]."</td>\n";
        echo " </tr>\n";
      }
    }
  }

  function displayModellers()
  {
    
    $sql="SELECT modellers.* FROM modellers 
                             INNER JOIN modellers_models ON modellers.id=modellers_models.modeller_id 
                             INNER JOIN models ON modellers_models.model_id=models.id
                             WHERE models.id='".$this->model_id()."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      echo " <tr>\n";
      echo "  <td class='about_name'>".$this->text('about_Modellers')."</td>\n";
      echo "  <td class='about_value'>\n";
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        echo $linea['firstname']." ".$linea['lastname']." (".$linea['email'].")<br>\n";
      }
      echo "  </td>\n";
      echo " </tr>\n";
    }
  }
  
  function displayFiles()
  {
    $files_data=array("/modelica/source.tar.gz"=>"about_Modelica_files",
                      "/doc/documentation.pdf"=>"about_Documentation",
                      "/graphics/graphics.zip"=>"about_Graphic_files");
    echo " <tr>\n";
    echo "  <td class='about_name'>".$this->text('about_Files')."</td>\n";
    echo "  <td class='about_value'>\n";
    foreach($files_data as $K=>$V)
    {
      if(file_exists("./files/".$this->model_id().$K))
      {
        echo "<a href=\"./files/".$this->model_id().$K."\" target='_blank'>".$this->text($V)."<br>\n";
      }
    }
    echo "  </td>\n";    
    echo " </tr>\n";
  }
  
  function displayEmbedLinks()
  {
    echo " <tr>\n";
    echo "  <th colspan=2 class='about_title'>".$this->text('about_Links_for_embedding')."</th>\n";
    echo " </tr>\n";
    $links_data=array();
    $links_data[$this->text("about_Model")]="&lt;iframe src=\"http://".$this->configurationSettings['URLbase'].
                                                   "/embed.php?modelid=".$this->model_id()."\" width=940 height=400&gt;&lt;/iframe&gt;";
    if(file_exists("./files/".$this->model_id()."/doc/documentation.pdf"))
    {
      $links_data[$this->text("about_Documentation")]="&lt;iframe src=\"http://".$this->configurationSettings['URLbase'].
                                                          "/doc.php?modelid=".$this->model_id()."\" width=940 height=400&gt;&lt;/iframe&gt;";
      $links_data[$this->text("about_Pdf")]="http://".$this->configurationSettings['URLbase'].
                                                 "/files/".$this->model_id()."/doc/documentation.pdf";
    }
    foreach($links_data as $K=>$V)
    {
      echo " <tr>\n";
      echo "  <td class='about_name'>\n";
      echo $K;
      echo "  </td>\n";    
      echo "  <td class='about_value'>\n";
      echo $V;
      echo "  </td>\n";    
      echo " </tr>\n";
    }
  }
  
  function displayPractices()
  {
    echo " <tr>\n";
    echo "  <th colspan=2 class='about_title'>".$this->text('about_Practices')."</th>\n";
    echo " </tr>\n";
    $sql="SELECT practices.* FROM practices 
                             INNER JOIN models ON practices.model_id=models.id
                             WHERE models.id='".$this->model_id()."'";
    $sql.=$this->enabled("practices");
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        echo " <tr>\n";
        echo "  <td class='about_name'>".$linea['name']."</td>\n";
        echo "  <td class='about_name'><i>".$linea['header']."</i>".$linea['description']."</td>\n";
        echo " </tr>\n";
      }
    }
  }
}

$B=new block();
if($B->connect())
{
  $xmlFN="page_structure/about.xml";
  $B->html($xmlFN);
}

?>
