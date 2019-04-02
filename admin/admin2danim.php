<?php
require_once("admin/adminblock.php");
require_once('svganim2d.php');

class admin2danim extends adminblock
{  

  function createFields()
  {
    $this->varpar="variable";
    $this->blockname=$this->text("admin2danim_2D_animation");
    $this->table="2danimations";
    $this->sectionidname="2danimationid";
    $this->idname="id";
    $this->idvalue=$this->c2danimation_id();
    $this->fields=array();
    $this->fields[]=array("dbname"=>"id",
                          "showname"=>$this->text("admin2danim_Identifier"),
                          "type"=>"fixed");
    $this->fields[]=array("dbname"=>"model_id",
                          "showname"=>$this->text("admin2danim_Model_identifier"),
                          "type"=>"model link");
    $this->fields[]=array("dbname"=>"name",
                          "showname"=>$this->text("admin2danim_Animation_name"),
                          "type"=>"text");
    $this->fields[]=array("dbname"=>"description",
                          "showname"=>$this->text("admin2danim_Animation_description"),
                          "type"=>"longtext");
    $this->fields[]=array("dbname"=>"enabled",
                          "showname"=>$this->text("admin2danim_Enabled"),
                          "type"=>"bool");
    $this->fields[]=array("dbname"=>"duration",
                          "showname"=>$this->text("admin2danim_Duration"),
                          "type"=>"float");
    $this->fields[]=array("dbname"=>"sample_time",
                          "showname"=>$this->text("admin2danim_Sample_time"),
                          "type"=>"float");
  }  
  
  function createRelations1N()
  {
    $this->Relations1N=array();
    $R=array(
             "title"=>$this->text("admin2danim_Animation"),
             "table"=>"2deffects",
             "linkname"=>"2deffectid",
             "id1"=>"id",
             "id2"=>"2danimation_id",
             "idvalue"=>$this->c2danimation_id(),
             "showdbname"=>"name",
             "show"=>$this->text("admin2danim_Object_name"));
    $this->Relations1N[]=$R;
  }  
  
  function createRelations1N1N()
  {
    $this->Relations1N1N=array();
  }  

  function findModelId()
  {
    $sql="SELECT model_id FROM 2danimations WHERE id=".$this->c2danimation_id();
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      return($linea['model_id']);
    }
    return 0;
  }

  function upload() 
  {
    $FILE=$_FILES["filesvg"];
    if ($_FILE["error"] == 0)
    {
      $model_id=$this->findModelId();
      $path="files/".$model_id."/graphics/";
      $fn=$path.$FILE['name'];
      move_uploaded_file($FILE['tmp_name'],$fn);
      $sql="UPDATE 2danimations SET svg_file='".$FILE['name']."' WHERE id=".$this->c2danimation_id();
      mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
      // delete old files
      $svgnames=array();
      $sql="SELECT id as FN  FROM plots WHERE model_id=".$model_id;
      $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
      if($result and mysqli_num_rows($result)>0)
      {
        while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
        {
          $svgnames[]=$path."expPlot_".$linea['FN'].".svg";
        }
      }
      $sql="SELECT svg_file as FN FROM 2danimations WHERE model_id=".$model_id;
      $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
      if($result and mysqli_num_rows($result)>0)
      {
        while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
        {
          $svgnames[]=$path.$linea['FN'];
        }
      }
      $files=glob($path."*.*");
      foreach($files as $fn)
      {
        if(!in_array($fn,$svgnames))
        {
          unlink($fn);
        }
      }
      // create tar files
      $orden= "cd ".$path."; tar -czf graphics.tar.gz *.svg ";
      passthru($orden);
    }
  }
  function displayPostOwn()
  {
    if(isset($_POST['upload_file']))
    {
      $this->upload();
    }
    $filename="";
    $sql="SELECT 2danimations.svg_file as F FROM 2danimations 
                      WHERE 2danimations.id='".$_POST['2danimationid']."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      $filename=$linea["F"];
    }
    echo "<script type=\"text/javascript\" src=\"admin/si.files.js\"></script>\n";
    echo "<table class=\"updatefile\">\n";
    echo "<th class=\"update_title\" colspan=3>".$this->text("admin2danim_SVG_file")."</th>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo "<form method=\"POST\" enctype=\"multipart/form-data\">\n";
    echo "  <td class=\"update_name\">".$filename."</td>\n";
    echo "  <td class=\"update_name\">\n";
    echo "   <input type=\"hidden\" name=\"uploadfiletype\" value=\"svg\" />\n";
    echo "   <label for=\"filesvg\" class=\"cabinet\">\n";
    echo "    <input type=\"file\" name=\"filesvg\" id=\"filesvg\" class=\"file\"/>\n";
    echo "   </label>\n";
    echo "  </td>\n";
    echo "  <td class=\"update_name\">\n";
    echo "   <input type=\"hidden\" name=\"".$this->sectionidname."\" value=\"".$this->idvalue."\">\n";
    echo "   <input type=\"submit\" name=\"upload_file\" value=\"Enviar\" class=\"controls_button_file\"/>\n";
    echo "  </td>\n";
    echo "</form>\n";    
    echo "</tr>\n";
    echo " <tr>\n";
    echo "<td  class=\"updatefile_file\" colspan=3>\n";
    $this->displayAnim();
    echo "</td>\n";
    echo " </tr>\n";
    echo "</table>";
    echo "<script type=\"text/javascript\">SI.Files.stylizeAll();</script>\n";
  }
  
  function displayAnim()
  {
  
    $sql="SELECT 2danimations.*,models.exename FROM 2danimations 
                      INNER JOIN models ON 2danimations.model_id=models.id 
                      WHERE 2danimations.id='".$_POST['2danimationid']."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      
      $graph = new animation2dSVG();
      $graph->connect();
      if($graph->link)
      {
        $graph->animateObject($linea,"files/".$linea['model_id']."/bin/".$linea['exename']."_res.csv");
        echo "<div style=\"text-align:center;\">ss\n";
        echo $graph->writeXmlString();
        echo "</div>\n";
        $graph->disconnect();
      }
    }
  }  
    
}

?>
