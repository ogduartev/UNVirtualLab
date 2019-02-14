<?php
require_once("admin/adminblock.php");

class adminlogin extends adminblock
{

  function showMessage()
  {
    echo "  <div class='wellcome_box' OnClick='window.location=\"unvl.php\"'>\n";
    echo "   <div class='wellcome'>".$this->text('adminlogin_Wellcome_title')."</div>\n";
    echo "   <div class='wellcome_sub'>".$this->text('adminlogin_Wellcome_subtitle')."</div>\n";
    echo "   <div class='wellcome_explain'>".$this->text('adminlogin_Wellcome_explanation')."</div>\n";
    echo "  </div>\n";
  }
  
  function showButtons()
  {
    echo "  <div class='wellcome_buttons'>\n";
    echo "     <form method='POST' action='admin.php'>\n";
    echo "      <table>\n";
    echo "       <tr>\n";
    echo "        <td class='login_label' >".$this->text('adminlogin_User')."</td>\n";
    echo "        <td class='login_data' ><input class='login' name='loginname' type='text'></td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class='login_label' >".$this->text('adminlogin_Password')."</td>\n";
    echo "        <td class='login_data' ><input class='login' name='loginpass' type='password'></td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td></td><td class='login_button' >\n";
    echo "         <input type='submit' name='loginsubmit' class='wellcome_button' value='".$this->text('adminlogin_Configuration')."'>\n";
    echo "        </td>\n";
    echo "       </tr>\n";
    echo "      </table>\n";
    echo "     </form>\n";
    echo "  </div>\n";
  }

  function display()
  {
    $this->showMessage();
    $this->showButtons();
  }
}
?>
