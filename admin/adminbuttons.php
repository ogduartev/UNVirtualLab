<?php
require_once("admin/adminblock.php");

class adminbuttons extends adminblock
{
  
  function display()
  {
    echo "     <form method='POST' action='admin.php'>\n";
    echo "       <input type='submit' name='reportsubmit' class='admin_button' value='".$this->text('adminlogout_Reports')."'>\n";
    echo "       <input type='submit' name='exitsubmit'   class='admin_button' value='".$this->text('adminlogout_Logout')."'>\n";
    echo "     </form>\n";
  }
  
}
?>
