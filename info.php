<?php
require_once('block.php');
require_once('header.php');
require_once('footer.php');

class info extends block
{  
  function display()
  {
    echo "  <div class='wellcome_box'>\n";
    echo "   <div class='wellcome_unvl'><a class='logosmall'></a></div>\n";
    echo "   <div class='wellcome_sub'>Laboratorio virtual de sistemas dinámicos</div>\n";
    echo "   <div class='wellcome_explain'>Ambiente Web de simulación de sistemas dinámicos basado en el lenguaje <a href='http://www.modelica.org' target='_new' class='info'>modelica</a><br><a href='http://www.unal.edu.co' target='_new' class='info'>Universidad Nacional de Colombia</a></div>\n";
    echo "  </div>\n";
    echo "<div class='wellcome_buttons'>\n";
    echo "    <table>\n";
    echo "     <tr>\n";
    echo "      <td class='login_label' ><a href=\"https://isbn.camlibro.com.co/catalogo.php?mode=detalle&nt=311416\" target='_blank' class='info'>Documentación principal</a></td>\n";
    echo "     </tr>\n";
    echo "     <tr>\n";
    echo "      <td class='login_label' ><a href=\"https://github.com/ogduartev/UNVirtualLab\" target='_blank' class='info'>Instaladores</a></td>\n";
    echo "     </tr>\n";
    echo "     <tr>\n";
    echo "      <td class='login_label' ><a href=\"faqs.php\" target='_blank' class='info'>Manual del experimentador</a></td>\n";
    echo "     </tr>\n";
    echo "     <tr>\n";
    echo "      <td class='login_label' ><a href=\"info/html/index.html\" target='_blank' class='info'>Documentación de clases en html</a></td>\n";
    echo "     </tr>\n";
    echo "     <tr>\n";
    echo "      <td class='login_label' ><a href=\"info/latex/refman.pdf\" target='_blank' class='info'>Documentación de clases en pdf</a></td>\n";
    echo "     </tr>\n";
    echo "    </table>\n";
    echo "  </div>\n";
    echo "</div>\n";
  }
}

$B=new block();
$B->connect();
if($B->link)
{
  $pageStructure = trim($B->configurationSettings['PageStructure']);
  $xmlFN="page_structure/".$pageStructure."/info.xml";

  $B->html($xmlFN);
  $B->display();
}
?>
