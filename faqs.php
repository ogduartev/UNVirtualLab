<?php
require_once('block.php');
require_once('header.php');
require_once('footer.php');

class faqs extends block
{  
  function display()
  {
    echo "   <script type=\"text/javascript\">\n";
    echo "   function video(fn)\n";
    echo "    {\n";
    echo "      window.open('info/'+fn+'.ogv','','scrollbars=yes,menubar=no,height=372,width=656,resizable=yes,toolbar=no,location=no,status=no');\n";
    echo "    }\n";
    echo "   </script>\n";
  
    echo " <table class='faqs' align='center'>\n";
    
    echo "  <tr><th class='faqs_header' colspan='3'>Manual del experimentador. Preguntas frecuentes</th></tr>\n";
    echo "  <tr><th class='faqs_title'>Pregunta</th> <th class='faqs_title'>Respuesta</th> <th class='faqs_title'>Video</th> </TR>\n";
    echo "  <tr>\n";
    echo "   <td class='faqs_name'>1. ¿Cómo selecciono una planta de experimentación?</td> \n";
    echo "   <td class='faqs_value'>El menú de selección muestra el listado de plantas de experimentación disponibles. Las plantas están agrupadas en secciones. Para seleccionar una planta, navegue en el menú a lo largo de las secciones y haga  </I>'<I>clic</I>' sobre la planta deseada.</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "<a onclick='video(\"menu\")'><img src=\"./themes/".$this->configurationSettings['theme']."/img/video.gif\"></a>\n";
    echo "   </td>\n";
    echo "   </tr>\n";
    
    echo "  <tr>\n";
    echo "   <td class='faqs_name'>2. ¿Cómo lanzo una simulación?</td> \n";
    echo "   <td class='faqs_value'>Haga  </I>'<I>clic</I>' sobre el botón  </I>'<I>Simular</I>' que está en el recuadro de los controles</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "<a onclick='video(\"simula\")'><img src=\"./themes/".$this->configurationSettings['theme']."/img/video.gif\"></a>\n";
    echo "   </td>\n";
    echo "   </tr>\n";

    echo "  <tr>\n";
    echo "   <td class='faqs_name'>3. ¿Cómo modifico las condiciones de la simulación?</td> \n";
    echo "   <td class='faqs_value'>Utilice los controles disponibles para ello. Tiene tres alternativas: 1) escribir el valor deseado 2) utilizar las flechas para aumentar o disminuir el valor y 3) utilizar el botón deslizante para ajustar el valor.<BR>\n";
    echo "El botón  </I>'<I>Restaurar</I>' le permite recuperar los valores por defecto de todos los parámetros.</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "<a onclick='video(\"modifica\")'><img src=\"./themes/".$this->configurationSettings['theme']."/img/video.gif\"></a>\n";
    echo "   </td>\n";
    echo "   </tr>\n";

    echo "  <tr>\n";
    echo "   <td class='faqs_name'>4. ¿Cómo descargo los resultados de la simulación?</td> \n";
    echo "   <td class='faqs_value'>Haga  </I>'<I>clic</I>' sobre el botón  </I>'<I>Descargar</I>' que está sobre la tabla de resultados. El archivo de descarga está en formato csv, separado por tabulador. El separador decimal empleado es la coma.</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "<a onclick='video(\"descarga\")'><img src=\"./themes/".$this->configurationSettings['theme']."/img/video.gif\"></a>\n";
    echo "   </td>\n";
    echo "   </tr>\n";

    echo "  <tr>\n";
    echo "   <td class='faqs_name'>5. ¿Dónde encuentro los experimentos sugeridos para realizar con la planta?</td> \n";
    echo "   <td class='faqs_value'>Haga  </I>'<I>clic</I>' sobre el logo de ayuda. Se desplegará una ventana que contiene información sobre la planta experimental, entre la que se encuentra el listado de experimentos sugeridos.</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "<a onclick='video(\"experimento\")'><img src=\"./themes/".$this->configurationSettings['theme']."/img/video.gif\"></a>\n";
    echo "   </td>\n";
    echo "   </tr>\n";

    echo "  <tr>\n";
    echo "   <td class='faqs_name'>6. ¿Cómo descargo la documentación?</td> \n";
    echo "   <td class='faqs_value'>Haga  </I>'<I>clic</I>' sobre el logo de ayuda. Se desplegará una ventana que contiene información sobre la planta experimental. En la línea marcada como  </I>'<I>Archivos</I>' encontrará el enlace a la  </I>'<I>Documentación</I>' que está en formato pdf. También puede utilizar las opciones de descarga del visualizador de pdf de su navegador, si este las tiene.</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "<a onclick='video(\"documenta\")'><img src=\"./themes/".$this->configurationSettings['theme']."/img/video.gif\"></a>\n";
    echo "   </td>\n";
    echo "   </tr>\n";

    echo "  <tr>\n";
    echo "   <td class='faqs_name'>7. ¿Cómo descargo el código fuente de la simulación?</td> \n";
    echo "   <td class='faqs_value'>Haga  </I>'<I>clic</I>' sobre el logo de ayuda. Se desplegará una ventana que contiene información sobre la planta experimental. En la línea marcada como  </I>'<I>Archivos</I>' encontrará el enlace a los  </I>'<I>Archivos Modelica</I>', que aparecen comprimidos en un único archivo en formato tar.gz.</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "<a onclick='video(\"fuente\")'><img src=\"./themes/".$this->configurationSettings['theme']."/img/video.gif\"></a>\n";
    echo "   </td>\n";
    echo "   </tr>\n";

    echo "  <tr>\n";
    echo "   <td class='faqs_name'>8. ¿Cómo inserto la planta de experimentación en un documento SCORM o HTML?</td> \n";
    echo "   <td class='faqs_value'>Haga  </I>'<I>clic</I>' sobre el logo de ayuda. Se desplegará una ventana que contiene información sobre la planta experimental. En la línea marcada como  </I>'<I>Modelo</I>', bajo la marca  </I>'<I>Enlaces para incrustar</I>' encontrará el  código en lenguaje HTML que necesita para incrustar en su documento el experimento. Este código genera un frame interno que contiene todos los instrumentos de la planta, es decir, que contiene tanto los controles como visualizadores.</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "<a onclick='video(\"scorm\")'><img src=\"./themes/".$this->configurationSettings['theme']."/img/video.gif\"></a>\n";
    echo "   </td>\n";
    echo "   </tr>\n";

    echo "  <tr>\n";
    echo "   <td class='faqs_name'>9. ¿Cómo inserto la documentación en un documento SCORM o HTML?</td> \n";
    echo "   <td class='faqs_value'>Haga  </I>'<I>clic</I>' sobre el logo de ayuda. Se desplegará una ventana que contiene información sobre la planta experimental. En la línea marcada como  </I>'<I>Documentación</I>', bajo la marca  </I>'<I>Enlaces para incrustar</I>' encontrará el  código en lenguaje HTML que necesita para incrustar en su documento la documentación de la planta. También puede emplear el enlace de la línea  </I>'<I>Pdf</I>' para enlazar directamente el archivo pdf correspondiente.</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "<a onclick='video(\"scormdoc\")'><img src=\"./themes/".$this->configurationSettings['theme']."/img/video.gif\"></a>\n";
    echo "   </td>\n";

    echo "   </tr>\n";
    echo "  <tr>\n";
    echo "   <td class='faqs_name'>10. ¿Cómo incorporo más controles, gráficas o animaciones?</td> \n";
    echo "   <td class='faqs_value'>Esta opción sólo está disponible para el administrador del sitio.</td> \n";
    echo "   <td class='faqs_video'>\n";
    echo "   </td>\n";
    echo "   </tr>\n";
    echo " </table>  \n";
  }
}

$B=new block();
$B->connect();
if($B->link)
{
  $pageStructure = trim($B->configurationSettings['PageStructure']);
  $xmlFN="page_structure/".$pageStructure."/faqs.xml";
   
  $B->html($xmlFN);
  $B->display();
}
?>
