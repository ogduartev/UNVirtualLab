<?php
require_once('admin/adminblock.php');

class sessionManager extends adminblock 
{
  function verify()
  {
    session_start();
    if($this->connect())
    {
      $sql="SELECT * FROM users WHERE user_name=\"".$_POST['loginname']."\" AND password=SHA1(\"".$_POST['loginpass']."\")";
      $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
      if($result and mysqli_num_rows($result)>0)
      {
        $_SESSION['UNVL_SESSION_UNVL']=true;
        return true;
      }
    }
    return false;
  }
}

?>
