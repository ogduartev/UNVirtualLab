<?php
require_once('block.php');
require_once('admin/adminlogin.php');
require_once('admin/adminlogout.php');
require_once('admin/session.php');
require_once('admin/menuadmin.php');
require_once('admin/adminmodel.php');
require_once('admin/adminplot.php');
require_once('admin/admin2danim.php');
require_once('admin/admin2deffect.php');
require_once('admin/admincontrol.php');
require_once('admin/adminpractice.php');
require_once('admin/adminmodelfiles.php');
require_once('admin/adminsection.php');
require_once('admin/databasemanager.php');


$B=new block();
$B->connect();
if($B->link)
{
  $pageStructure = trim($B->configurationSettings['PageStructure']);
  $xmlFN="page_structure/".$pageStructure."/admin.xml";
  
  $section_id=$B->section_id();
  $model_id=$B->model_id();
  $modeller_id=$B->modeller_id();
  $plot_id=$B->plot_id();
  $curve_id=$B->curve_id();
  $control_group_id=$B->control_group_id();
  $control_id=$B->control_id();
  $practice_id=$B->practice_id();
  $c2danim_id=$B->c2danimation_id();
  $c2deffect_id=$B->c2deffect_id();
  $modeller_id=$B->modeller_id();
  session_start();
  if(!isset($_SESSION['UNVL_SESSION_UNVL']))
  {
    $SM=new sessionManager();
    $SM->connect();
    if($SM->link)
    {
      if(isset($_POST['loginsubmit']) and $SM->verify())
      {
        $xmlFN="page_structure/".$pageStructure."/admin.xml";
      }else
      {
        $xmlFN="page_structure/".$pageStructure."/login.xml";
      }
      $SM->disconnect();
    }
  }elseif(isset($_POST['exitsubmit']))
  {
    $xmlFN="page_structure/".$pageStructure."/login.xml";
    session_unset();
  }elseif($section_id > 0)
  {
    $xmlFN="page_structure/".$pageStructure."/adminsection.xml";
  }elseif($model_id > 0)
  {
    $xmlFN="page_structure/".$pageStructure."/adminmodel.xml";
  }elseif($plot_id > 0 )
  {
    $xmlFN="page_structure/".$pageStructure."/adminplot.xml";
  }elseif($curve_id > 0)
  {
    $xmlFN="page_structure/".$pageStructure."/adminplot.xml";
  }elseif($control_group_id > 0)
  {
    $xmlFN="page_structure/".$pageStructure."/admincontrol.xml";
  }elseif($control_id > 0)
  {
    $xmlFN="page_structure/".$pageStructure."/admincontrol.xml";
  }elseif($practice_id > 0)
  {
    $xmlFN="page_structure/".$pageStructure."/adminpractice.xml";
  }elseif($c2danim_id > 0)
  {
    $xmlFN="page_structure/".$pageStructure."/admin2danim.xml";
  }elseif($c2deffect_id > 0)
  {
    $xmlFN="page_structure/".$pageStructure."/admin2deffect.xml";
  }
  
  $B->html($xmlFN);
  $B->disconnect();

}
?>
