<?php
require_once("admin/adminblock.php");

class adminlogout extends adminblock
{
  
  function display()
  {
    echo "     <form method='POST' action='admin.php'>\n";
    echo "       <input type='submit' name='exitsubmit' class='logout_button' value='".$this->text('adminlogout_Logout')."'>\n";
    echo "     </form>\n";
  }
  
}
?>
