<?php
require_once('block.php');

class launch extends block
{  
  function display()
  {
    $model_id=$this->model_id();
    if($model_id==0)
    {
      $this->displayVideo();
      return;
    }

    echo "<script type=\"text/javascript\">\n";

    echo "function launchDoc(modelid)\n";
    echo "{\n";
    echo "  window.open('doc.php?modelid='+modelid,'','scrollbars=yes,menubar=no,height=".$this->configurationSettings['docHeight'].",width=".$this->configurationSettings['docWidth'].",resizable=yes,toolbar=no,location=no,status=no');";
    echo "}\n";

    echo "function launchInf(modelid)\n";
    echo "{\n";
    echo "  window.open('about.php?modelid='+modelid,'','scrollbars=yes,menubar=no,height=".$this->configurationSettings['aboutHeight'].",width=".$this->configurationSettings['aboutWidth'].",resizable=yes,toolbar=no,location=no,status=no');";
    echo "}\n";
    echo "</script>\n";

    echo "  <div class='launchButtonsFrame'>\n";

    echo "  <FORM action='alone.php' method='POST' target='_blank'>\n";
    echo "   <input type='submit' class='launchSim' value='' name='submit'>\n";
    echo "   <input type='hidden' id='modelid' name='modelid' value='".$model_id."'>\n";
    echo "  </FORM>\n";

    echo "  <div class='launchDoc'>\n";
    echo "   <a onclick='launchDoc(\"".$model_id."\")' class='launch'></a>\n";
    echo "  </div>\n";

    echo "  <div class='launchInf'>\n";
    echo "   <a onclick='launchInf(\"".$model_id."\")' class='launch'></a>\n";
    echo "  </div>\n";
    echo "  </div>\n";

    $this->displayMessage();
  }

  function displayVideo()
  {
      echo "    <video controls=\"controls\" src=\"info/inicio.ogv\" type=\"video/ogg\" class=\"unvlvideo\" autoplay>\n";
      echo $this->text("description_Select_an_experimentation_plant")."\n";
      echo "    </video>\n";
  }
  
  function displayMessage()
  {
      echo "		<div onload=\"initBox()\" class=\"unvlboxmail\" id=\"closeboxdiv\">\n";
      echo "     <a class=\"launch\" id=\"closeboxdiv\" href=\"mailto:unvsoporte@unal.edu.co\"></a>\n";
      echo "    </div>\n";
  }
}

class launchsmall extends block
{  
  function display()
  {
    $model_id=$this->model_id();
    if($model_id==0)
    {
      return;
    }
    echo "<script type=\"text/javascript\">function launch(modelid)\n";
    echo "{";
    echo "  window.open('alone.php?modelid='+modelid,'_blank');";
    echo "}";
    echo "</script>";
    echo "<a onclick='launch(\"".$model_id."\")'><div class='launchsmall'></div></a>";
  }
}


class launchsmalldoc extends block
{  
  function display()
  {
    $model_id=$this->model_id();
    if($model_id==0)
    {
      return;
    }
    echo "<script type=\"text/javascript\">function launchdoc(modelid)\n";
    echo "{";
    echo "  window.open('doc.php?modelid='+modelid,'','scrollbars=yes,menubar=no,height=".$this->configurationSettings['docHeight'].",width=".$this->configurationSettings['docWidth'].",resizable=yes,toolbar=no,location=no,status=no');";
    echo "}";
    echo "</script>";
    echo "<a onclick='launchdoc(\"".$model_id."\")'><div class='launchsmalldoc'></div></a>";
  }
}


class launchsmallinf extends block
{  
  function display()
  {
    $model_id=$this->model_id();
    if($model_id==0)
    {
      return;
    }
    echo "<script type=\"text/javascript\">function launchinf(modelid)\n";
    echo "{";
    echo "  window.open('about.php?modelid='+modelid,'','scrollbars=yes,menubar=no,height=".$this->configurationSettings['aboutHeight'].",width=".$this->configurationSettings['aboutWidth'].",resizable=yes,toolbar=no,location=no,status=no');";
    echo "}";
    echo "</script>";
    echo "<a onclick='launchinf(\"".$model_id."\")'><div class='launchsmallinf'></div></a>";
  }
}


?>


