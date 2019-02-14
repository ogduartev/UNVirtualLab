<?php

class esqueleto
{
  var $flagBorrado=false;
  var $numOutputs;
  var $archivos;
  var $refPrefijo="";
  
  function esqueleto()
  {
    $this->archivos=array();
    $this->archivos['mainTex']       =true;
    $this->archivos['archivos']      =true;
    $this->archivos['experimentos']  =true;
    $this->archivos['implementacion']=true;
    $this->archivos['modelo']        =true;
    $this->archivos['presentacion']  =true;
    $this->archivos['referencias']   =true;
    $this->archivos['resumen']       =true;
    $this->archivos['exp']           =true;
    $this->archivos['expFig']        =true;
    $this->archivos['expTabAni']     =true;
    $this->archivos['expTabPar']     =true;
    $this->archivos['expTabPlt']     =true;
    $this->archivos['expTabTab']     =true;
    $this->archivos['expTex']        =true;
  }

  function actualizarExp()
  {
    $this->archivos=array();
    $this->archivos['mainTex']       =false;
    $this->archivos['archivos']      =false;
    $this->archivos['experimentos']  =true;
    $this->archivos['implementacion']=false;
    $this->archivos['modelo']        =false;
    $this->archivos['presentacion']  =false;
    $this->archivos['referencias']   =false;
    $this->archivos['resumen']       =false;
    $this->archivos['exp']           =true;
    $this->archivos['expFig']        =true;
    $this->archivos['expTabAni']     =true;
    $this->archivos['expTabPar']     =true;
    $this->archivos['expTabPlt']     =true;
    $this->archivos['expTabTab']     =true;
    $this->archivos['expTex']        =false;
  }

  function crearCarpeta($nuevaCarpeta)
  {
    if(file_exists($nuevaCarpeta))
    {
      if(is_dir($nuevaCarpeta))
      {
        if($this->flagBorrado===false)
        {
          echo "El directorio existe\n";
          $answer=readline("¿Desea sobreescribir el directorio ".$nuevaCarpeta." (S/N/a)?");
          if($answer==="a"){$this->flagBorrado=true;$answer="S";}
          if($answer!=="S")
          {
            echo "Saliendo...\n";
            return false;
          }
        }
        echo "Sobreescribir la carpeta ".$nuevaCarpeta."\n";
        return true;
      }else
      {
        if($this->flagBorrado===false)
        {
          echo "Hay un archivo con el nombre ".$nuevaCarpeta."\n";
          $answer=readline("¿Desea sobreescribir el archivo(S/N/a)?");
          if($answer==="a"){$this->flagBorrado=true;$answer="S";}
          if($answer!=="S")
          {
            echo "Saliendo...\n";
            return false;
          }
        }
        echo "Borrando el archivo ".$nuevaCarpeta."\n";
        unlink($nuevaCarpeta);
      }
    }
    echo "Creando carpeta ".$nuevaCarpeta."\n";
    mkdir($nuevaCarpeta);
    return true;
  }
  
  function archivar($flag,$fn,$str)
  {
    if(!($this->archivos[$flag]===true) and file_exists($fn))
    {
      echo "No se escribe el archivo ".$fn."\n";
      return;      
    }
    if(file_exists($fn))
    {
      echo "Se reescribe el archivo ".$fn."\n";
    }else
    {
      echo "Se crea el archivo ".$fn."\n";
    }
    $f=fopen($fn,"w");
    fwrite($f,$str);
    fclose($f);
  }

  function crearMainTex($texMain)
  {
    $str="";
    $str.="\\input{../preambulo}\n";
    $str.="\\label{cap:".$this->refPrefijo."}\n\n";
    $str.="\\begin{minipage}{\\textwidth}\n";
    $str.=" \\titulo{Título del modelo}{Oscar G. Duarte}\n";
    $str.=" \\end{minipage}\n";
    $str.="\n";
    $str.="\\input{resumen}\n";
    $str.="\\input{presentacion}\n";
    $str.="\n";
    $str.="\\vspace{1.5cm}\n";
    $str.="\\hrule\n";
    $str.="\\tableofcontents\n";
    $str.="\\vspace{0.5cm}\n";
    $str.="\\hrule\n";
    $str.="\n";
    $str.="\\section{El modelo}\n";
    $str.="\\input{modelo}\n";
    $str.="\n";
    $str.="\\section{Plantas de experimentación y experimentos sugeridos}\n";
    $str.="\\input{experimentos}\n";
    $str.="\n";
    $str.="\\section{La implementación}\n";
    $str.="\\input{implementacion}\n";
    $str.="\n";
    $str.="%\\section*{Referencias}\n";
    $str.="\\unvlbibliografia\n";
    $str.="\n";
    $str.="\\end{document}\n";
    $this->archivar('mainTex',$texMain,$str);
    return true;
  }
  
  function crearArchivosTex($dirmodels,$modelDir,$fnarchivos,$sectionID)
  {
    $str="";
    $str.="\\begin{tablaArchivos}{".$sectionID."}{".$this->refPrefijo."}\n";
    echo "Buscando archivos modelica en ".$dirmodels.$modelDir."/*.mo\n";
    $DD=glob($dirmodels.$modelDir."/*.mo");
    foreach($DD as $D)
    {
      $f=explode("/",$D);
      $fname=$f[count($f)-1];
      $str.="\\ref{".$this->refPrefijo."lst:".$fname."} & \\archivo{".$fname."}{".$this->refPrefijo."} \\\\ \hline\n";
    }
    $str.="\\end{tablaArchivos}\n";
    foreach($DD as $D)
    {
      $f=explode("/",$D);
      $fname=$f[count($f)-1];
      $str.="\\modelica{".$modelDir."/}{".$fname."}{".$this->refPrefijo."}\n";
    }
    $this->archivar('archivos',$fnarchivos,$str);
    return true;
  }

  function conectar($DBhost,$DBuser,$DBpass,$DBname)
  {
   if($link=mysql_connect($DBhost,$DBuser,$DBpass))
    {
      $sql="USE ".$DBname;
      if(!mysql_query($sql))
      {
        echo $this->text('about_No_Database_connection');
        return FALSE;
      }
    }else
    {
      return FALSE;
    }
    return $link;
  }
  
  function noAcentos($str)
  {
    $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
    $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
    return str_replace($search,$replace,$str);
  }
  
  function tablasParametros($nuevaCarpeta,$modelo,$cadnum,$ref)
  {
    $str="{\n\\scriptsize\n";
    $str.="\\begin{longtable}{|p{5cm}|p{3cm}|p{3cm}|p{3cm}|} \\hline\n";
    $str.="\expTabTitle{".$modelo['name']."}{".
                          $modelo['description']."}\n";
    $sql="SELECT * FROM control_groups WHERE model_id='".$modelo['id']."'";
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    $strModeller=$strEmail="";
    $sql="SELECT * FROM modellers INNER JOIN modellers_models ON modellers.id=modellers_models.modeller_id 
                                  WHERE modellers_models.model_id='".$modelo['id']."' ORDER BY lastname,firstname";
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $strModeller.=$linea['firstname']." ".$linea['lastname'].", ";
        $strEmail.=$linea['email'].", ";
      }
    }
    $strModeller=substr($strModeller,0,strlen($strModeller)-2);
    $strEmail=substr($strEmail,0,strlen($strEmail)-2);
    $str.="\expTabCredits{".$strModeller."}{".$strEmail."}\n";
    $str.="\\multicolumn{4}{|p{12cm}|}{\\centering \\textbf{Parámetros}} \\\\ \hline\n";
    $str.="\\textit{Grupo} & \\textit{nombre Modelica} & \\textit{nombre} & \\textit{descripción}\\\\ \hline\n";

    $sql="SELECT * FROM control_groups WHERE model_id='".$modelo['id']."'";
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $sql2="SELECT controls.*,parameters.modelicaname FROM controls INNER JOIN parameters ON controls.parameter_id=parameters.id 
                                      WHERE control_group_id='".$linea['id']."'";
        $result2=mysql_query($sql2) or die(mysql_error().": ".$sql2);
        if($result2 and mysql_num_rows($result2)>0)
        {
          $str.="\expTabParGr{}{".$linea['name']."}{".mysql_num_rows($result2)."}\n";
          while($linea2=mysql_fetch_array($result2,MYSQL_ASSOC))
          {
            $strTmp="\expTabPar{".$linea2['modelicaname']."}{".
                                $linea2['name']."}{".
                                $linea2['description']."}\n";
            $str.=str_replace("_","\\_",$strTmp);
          }
        }

      }
    }
    $str.="\\hline\n";
    $str.="\\caption[Parámetros del experimento \\ref{".$this->refPrefijo."exp:".$ref."}]{Parámetros del experimento \\ref{".$this->refPrefijo."exp:".$ref."}, ``".$modelo['title']."''}\n";
    $str.="\\label{".$this->refPrefijo."expTabPar:".$ref."}\n";
    $str.="\\end{longtable}\n";
    $str.="}\n";

    $this->archivar('expTabPar',"./".$nuevaCarpeta."/expTab/expTabPar".$cadnum.".tex",$str);

  }
  
  function tablasPlots($nuevaCarpeta,$modelo,$cadnum,$ref)
  {
    $str="{\n\\scriptsize\n";
    $str.="\\begin{longtable}{|m{5.5cm}m{9.0cm}|} \\hline\n";
    $sql="SELECT plots.*,variables.modelicaname as Xname FROM plots INNER JOIN variables ON plots.variable_id=variables.id 
                              WHERE plots.model_id='".$modelo['id']."'";
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $cnt=0;  
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $cadNumFig="";
        if($cnt<10){$cadNumFig="00";}
        elseif($cnt<100){$cadNumFig="0";}
        $cadNumFig.=$cnt;
        $cnt++;
        $str.="\expTabPlotFig{expFig/expPlot_".$cadnum."_".$cadNumFig."}\n";

        $str.="\\begin{tabular}{|m{1.5cm}|m{3.5cm}|m{1.0cm}|m{1.0cm}|} \\hline \n";
        $str.="\\multicolumn{4}{|m{7cm}|}\n";
        $str.="{\\expTabPlotTitle{".$linea['name']."}} \\\\ \n";
        $str.="\\multicolumn{4}{|m{7cm}|}\n";
        $str.="{\\expTabPlotDesc{".$linea['description']."}} \\\\ \hline\n";
        $str.=" \\expTabPlotLeg{Curva} & \\expTabPlotLeg{Descripción} & \\expTabPlotLeg{\$x\$} & \\expTabPlotLeg{\$y\$} \\\\ \\hline\n";
        
        $sql2="SELECT curves.*,variables.modelicaname as Yname FROM curves INNER JOIN variables ON curves.variable_id=variables.id 
                                    WHERE plot_id='".$linea['id']."'";
        $result2=mysql_query($sql2) or die(mysql_error().": ".$sql2);
        if($result2 and mysql_num_rows($result2)>0)
        {
          while($linea2=mysql_fetch_array($result2,MYSQL_ASSOC))
          {
            $str.="\expTabPlotData{".str_replace("_","\_",$linea2['legend'])."}{".
                                     str_replace("_","\_",$linea['description'])."}{".
                                     str_replace("_","\_",$linea['Xname'])."}{".
                                     str_replace("_","\_",$linea2['Yname'])."}\n";
          }
        }
        $str.="\\end{tabular}\n";
        $str.="\\\\ \hline\n";
      }
    }
    $str.="\\caption[Figuras del experimento \\ref{".$this->refPrefijo."exp:".$ref."}]{Figuras del experimento \\ref{".$this->refPrefijo."exp:".$ref."}, ``".$modelo['title']."''}\n";
    $str.="\\label{".$this->refPrefijo."expTabPlt:".$ref."}\n";
    $str.="\\end{longtable}\n";
    $str.="}\n";

    $this->archivar('expTabPlt',"./".$nuevaCarpeta."/expTab/expTabPlt".$cadnum.".tex",$str);
  }
  
  function tablasAnimaciones2D($nuevaCarpeta,$modelo,$cadnum,$ref)
  {
    $str="{\n\\scriptsize\n";
    $str.="\\begin{longtable}{|m{5.5cm}m{9.0cm}|} \\hline\n";

    $sql="SELECT * FROM 2danimations WHERE model_id='".$modelo['id']."'";
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $cnt=0;  
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $cadNumFig="";
        if($cnt<10){$cadNumFig="00";}
        elseif($cnt<100){$cadNumFig="0";}
        $cadNumFig.=$cnt;
        $cnt++;
        $str.="\expTabAniFig{expFig/expA2D_".$cadnum."_".$cadNumFig."}\n";
    
        $str.="\\begin{tabular}{|m{1.5cm}|m{2.5cm}|m{1.5cm}|m{1.5cm}|} \\hline \n";
        $str.="\\multicolumn{4}{|m{7cm}|}{\\expTabAniTitle{".$linea['name']."}} \\\\ \n";
        $str.="\\multicolumn{4}{|m{7cm}|}{\\expTabAniDesc{".$linea['description']."}} \\\\ \hline\n";
        $str.="\\multicolumn{4}{|c|}{\\textbf{Efectos}} \\\\ \hline\n";        
        $str.="\\expTabAniDataTit{Nombre}{Description}{Tipo}{Variable}\n"; 
        $sql2="SELECT 2deffects.*,modelicaname FROM 2deffects 
                         INNER JOIN variables ON 2deffects.variable_id=variables.id 
                         WHERE 2danimation_id='".$linea['id']."'";
        $result2=mysql_query($sql2) or die(mysql_error().": ".$sql2);
        if($result2 and mysql_num_rows($result2)>0)
        {
          while($linea2=mysql_fetch_array($result2,MYSQL_ASSOC))
          {
            $str.="\\expTabAniData{".$linea2['name']."}{".$linea2['description']."}{".$linea2['type']."}{".$linea2['modelicaname']."}\n"; 
          }
        }

        
        $str.="\\end{tabular} \\\\ \\hline \n";
      }
    }
    
    $str.="\\caption[Animaciones del experimento \\ref{".$this->refPrefijo."exp:".$ref."}]{Animaciones del experimento \\ref{".$this->refPrefijo."exp:".$ref."}, ``".$modelo['title']."''}\n";
    $str.="\\label{".$this->refPrefijo."expTabAni:".$ref."}\n";
    $str.="\\end{longtable}\n";
    $str.="}\n";
    $this->archivar('expTabAni',"./".$nuevaCarpeta."/expTab/expTabAni".$cadnum.".tex",$str);
  }
  
  function tablasTabla($nuevaCarpeta,$modelo,$cadnum,$ref)
  {
    $model_id=$modelo['id'];
    $outputs=array();
    
    // PLOTS AND CURVES OUTPUTS
    
    $sql="SELECT plots.id AS plotID,variables.* FROM plots INNER JOIN variables ON plots.variable_id=variables.id WHERE plots.model_id='".$model_id."'";
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $outputs[$linea['modelicaname']]=$linea;

        $sql2="SELECT variables.* FROM curves INNER JOIN variables ON curves.variable_id=variables.id WHERE curves.plot_id='".$linea['plotID']."'";
        $result2=mysql_query($sql2) or die(mysql_error().": ".$sql2);
        if($result2 and mysql_num_rows($result2)>0)
        {
          while($linea2=mysql_fetch_array($result2,MYSQL_ASSOC))
          {
            $outputs[$linea2['modelicaname']]=$linea2;        
          }
        }
      }
    }

    // 2DANIMATIONS OUTPUTS

    $sql="SELECT variables.* FROM variables INNER JOIN 2deffects ON variables.id=2deffects.variable_id
                                   INNER JOIN 2danimations ON 2deffects.2danimation_id=2danimations.id
                                   WHERE 2danimations.model_id='".$model_id."'";
                                   
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $outputs[$linea['modelicaname']]=$linea;        
      }
    }
    $this->numOutputs=count($outputs);

    $camposSale=array("modelicaname"=>"Variable","description"=>"Descripción","units"=>"Unidades");    
    $nc=count($camposSale);
    $str="{\n\\scriptsize\n";
    $str.="\\begin{longtable}{|*{".$nc."}{m{4.0cm}|}} \\hline\n";
    foreach($camposSale as $CS=>$campo)
    {
      $str.="\expTabTabTitle{".$campo."} & ";
    }
    $str=substr($str,0,strlen($str)-3);
    $str.=" \\\\ \hline\n";
    foreach($outputs as $o)
    {
      $strTmp="";
      foreach($camposSale as $CS=>$campo)
      {
        $strTmp.=$o[$CS]." & ";
      }
      $strTmp=str_replace("_","\\_",$strTmp);
      $strTmp=substr($strTmp,0,strlen($strTmp)-3);
      $str.=$strTmp." \\\\ \hline\n";
    }
    $str.="\\caption[Variables de resultados del experimento \\ref{".$this->refPrefijo."exp:".$ref."}]{Variables en la tabla de resultados del experimento \\ref{".$this->refPrefijo."exp:".$ref."}, ``".$modelo['title']."''}\n";
    $str.="\\label{".$this->refPrefijo."expTabTab:".$ref."}\n";
    $str.="\\end{longtable}\n";
    $str.="}\n";
    $this->archivar('expTabTab',"./".$nuevaCarpeta."/expTab/expTabTab".$cadnum.".tex",$str);
  }
  
  function expFiles($nuevaCarpeta,$modelo,$cadnum,$ref,$flagErase)
  {
    if($this->archivos['experimentos'])
    {
      $str="% ".$modelo['title']."\n";
      $str.="\\input{\\dirdoc exp/exp".$cadnum."}\n\n";
      $f=fopen("./".$nuevaCarpeta."/experimentos.tex","a");
      fwrite($f,$str);
      fclose($f);
    }
    
    $str="% ".$modelo['description']."\n";
    $this->archivar('expTex',"./".$nuevaCarpeta."/expTex/exp".$cadnum.".tex",$str);

    $practicas=array();

    $numCtl=$numPar=$numPlt=$numCur=$numAni=$numTab=$numPra=0;
    $sql="SELECT COUNT(*) AS C FROM control_groups WHERE model_id=".$modelo['id'];
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $linea=mysql_fetch_array($result,MYSQL_ASSOC);
      {
        $numCtl=$linea['C'];
      }
    }
    $sql="SELECT COUNT(*) AS C FROM control_groups INNER JOIN controls ON controls.control_group_id=control_groups.id WHERE model_id=".$modelo['id'];
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $linea=mysql_fetch_array($result,MYSQL_ASSOC);
      {
        $numPar=$linea['C'];
      }
    }
    $sql="SELECT COUNT(*) AS C FROM plots WHERE model_id=".$modelo['id'];
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $linea=mysql_fetch_array($result,MYSQL_ASSOC);
      {
        $numPlt=$linea['C'];
      }
    }
    $sql="SELECT COUNT(*) AS C FROM plots INNER JOIN curves ON curves.plot_id=plots.id WHERE model_id=".$modelo['id'];
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $linea=mysql_fetch_array($result,MYSQL_ASSOC);
      {
        $numCur=$linea['C'];
      }
    }
    $sql="SELECT COUNT(*) AS C FROM 2danimations WHERE model_id=".$modelo['id'];
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $linea=mysql_fetch_array($result,MYSQL_ASSOC);
      {
        $numAni=$linea['C'];
      }
    }
    $sql="SELECT * FROM practices WHERE model_id=".$modelo['id'];
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $numPra=mysql_num_rows($result);
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $practicas[]=$linea;
      }
    }
    $numTab=$this->numOutputs;
     
    $str="% ".$modelo['name']." %\n";
    $str.="\\begin{experimento}[".$modelo['title']."]\n";  
    $str.="\\label{".$this->refPrefijo."exp:".$ref."}\n";
    $str.="\\textbf{Presentación}\n";
    $str.="\\input{\dirdoc ./expTex/exp".$cadnum."} % Explicación\n\n";
    $str.="\\vspace{0.5cm}\n\\noindent\n\\textbf{Instrumentación}\n";
    if($numPar>0)
    {
      if($numPar>1)
      {
        $str.="El modelo cuenta con $numPar parámetros ajustables organizados en $numCtl grupos de controles ";
        $str.=" (Ver tabla \\ref{".$this->refPrefijo."expTabPar:".$ref."}).\n";
      }else
      {
        $str.="El modelo cuenta con $numPar parámetro ajustable ";
        $str.=" (Ver tabla \\ref{".$this->refPrefijo."expTabPar:".$ref."}).\n";        
      }
    }
    $str.="Como resultado del experimento, el programa despliega:\n";
    $str.="\\begin{itemize}\n";
    if($numCur>0)
    {
      $str.="\\item $numCur curvas organizadas en $numPlt gráficos ";
      $str.=" (Ver tabla \\ref{".$this->refPrefijo."expTabPlt:".$ref."}).\n";
    }
    if($numAni>0)
    {
      $str.="\\item $numAni animaciones en 2D ";
      $str.=" (Ver tabla \\ref{".$this->refPrefijo."expTabAni:".$ref."}).\n";
    }
    if($numTab>0)
    {
      $str.="\\item Una tabla de datos del comportamiento de $numTab variables \n";
      $str.=" (Ver tabla \\ref{".$this->refPrefijo."expTabTab:".$ref."}).\n";
    }
    $str.="\\end{itemize}\n\n";
    $str.="\\vspace{0.5cm}\n\\noindent\n\\textbf{Experimentos sugeridos}\n";
    if($numPra>0)
    {
      $str.="La siguiente es el listado de experimentos sugeridos:\n";
      foreach($practicas as $K=>$pr)
      {
        $cnt=$K+1;;
        $cadNumPra="";
        if($cnt<10){$cadNumPra="00";}
        elseif($cnt<100){$cadNumPra="0";}
        $cadNumPra.=$cnt;
        
        $str.="\\begin{practica}[".$pr['name']."]\n";
        $str.="\\label{".$this->refPrefijo."practica:".$cadnum."-".$cadNumPra."}";
        $str.="\\textbf{".$pr['header']."}\n".$pr['description']."\n";
        $str.="\\end{practica}\n";
      }
    }
    $str.="\\end{experimento}\n\n";    
    if($numPar>0)
    {
      $str.="\\input{\\dirdoc ./expTab/expTabPar".$cadnum."} % Parámetros\n";
    }
    if($numCur>0)
    {
      $str.="\\input{\\dirdoc ./expTab/expTabPlt".$cadnum."} % Figuras\n";
    }
    if($numAni>0)
    {
      $str.="\\input{\\dirdoc ./expTab/expTabAni".$cadnum."} % Animaciones\n";
    }
    if($numTab>0)
    {
      $str.="\\input{\\dirdoc ./expTab/expTabTab".$cadnum."} % Tabla de resultados\n";
    }
    $this->archivar('exp',"./".$nuevaCarpeta."/exp/exp".$cadnum.".tex",$str);
  }
  
  function tablas($nuevaCarpeta,$modelo,$cadnum,$ref,$flagErase)
  {
    $this->tablasParametros($nuevaCarpeta,$modelo,$cadnum,$ref);
    $this->tablasPlots($nuevaCarpeta,$modelo,$cadnum,$ref);
    $this->tablasAnimaciones2D($nuevaCarpeta,$modelo,$cadnum,$ref);
    $this->tablasTabla($nuevaCarpeta,$modelo,$cadnum,$ref);
    $this->expFiles($nuevaCarpeta,$modelo,$cadnum,$ref,$flagErase);
  }
  
  function figuras($nuevaCarpeta,$htmldir,$modelo,$cadnum,$flagErase)
  {
    $sql="SELECT * FROM plots WHERE model_id='".$modelo['id']."'";
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $cnt=0;  
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $cadNumFig="";
        if($cnt<10){$cadNumFig="00";}
        elseif($cnt<100){$cadNumFig="0";}
        $cadNumFig.=$cnt;

        $dir = $htmldir."files/".$modelo['id']."/graphics/";
        $fn=$dir."expPlot_".$linea['id'].".svg";
        $fsvg=file($fn);
        // opción 1: incluir el css adecuado
//        $encabezado=$fsvg[0];
//        $fsvg[0]=str_replace("href=\"","href=\"".$htmldir,$encabezado);
//        $fsvg[0]=$encabezado;
//        $strNuevo=implode("",$fsvg);
        // fin opción 1
        // opción 2: incluir en línea el css
        $f2=file($htmldir."themes/unvlbasic/styleSVG.css");
        $encabezado="<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><style type=\"text/css\" ><![CDATA[";
        $encabezado.=implode("",$f2);
        $encabezado.="]]></style>\n";
        $fsvg[0]=$encabezado;
        $strNuevo=implode("",$fsvg);
        $strNuevo.="\n</svg>\n";
        // fin opción 2
        $fnNuevo=$dir."tmp.svg";
        $ftemp=fopen($fnNuevo,"w");
        fwrite($ftemp,$strNuevo);
        fclose($ftemp);
//        $orden = "convert -background white ".$dir."tmp.svg ".$nuevaCarpeta."/expFig/expPlot_".$cadnum."_".$cadNumFig.".ps";
        $orden = "convert ".$dir."tmp.svg ".$nuevaCarpeta."/expFig/expPlot_".$cadnum."_".$cadNumFig.".ps";
        echo $orden."\n";
        passthru($orden);
        unlink($fnNuevo);
        $cnt++;
      }
    }
    
    $sql="SELECT * FROM 2danimations WHERE model_id='".$modelo['id']."'";
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $cnt=0;  
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $cadNumFig="";
        if($cnt<10){$cadNumFig="00";}
        elseif($cnt<100){$cadNumFig="0";}
        $cadNumFig.=$cnt;

        $dir = $htmldir."files/".$modelo['id']."/graphics/";
        $orden = "convert ".$dir.$linea['svg_file']." ".$nuevaCarpeta."/expFig/expA2D_".$cadnum."_".$cadNumFig.".ps";
        echo $orden."\n";
        passthru($orden);
      }
    }
    
  }

  function fecha()
  {
    $t=localtime(time(),1);
    $a=1900+$t['tm_year'];

    $s=$t['tm_mday'].'-'.($t['tm_mon']+1).'-'.$a.'-'.$t['tm_hour'].'-'.$t['tm_min'].'-'.$t['tm_sec'];
    return $s;
  }
  
  function modelos($nuevaCarpeta,$section_id,$modelsID,$flagErase,$htmldir)
  {
    $orden="rm ./".$nuevaCarpeta."/exp/*.*";
    passthru($orden);
    $orden="rm ./".$nuevaCarpeta."/expFig/*.*";
    passthru($orden);
    $orden="rm ./".$nuevaCarpeta."/expTab/*.*";
    passthru($orden);

    $str="\\label{".$this->refPrefijo."sec:experimentos}\n\n";
    $str.="\\listadoexperimentos\n\n";
    $str.="% \\listtheorems{experimento,practica}\n\n";
    $this->archivar('experimentos',$nuevaCarpeta."/experimentos.tex",$str);

    $sql="";
    if($modelsID=="")
    {
      $sql="SELECT * FROM models WHERE section_id='".$section_id."'";
    }else if($section_id=="")
    {
      $modID=explode(",",$modelsID);
      $sqlMod="";foreach($modID as $mid){$sqlMod.=" id='".$mid."' OR ";}$sqlMod=substr($sqlMod,0,strlen($sqlMod)-4);
      $sql="SELECT * FROM models WHERE ".$sqlMod;
    }else
    {
      $modID=explode(",",$modelsID);
      $sqlMod="";foreach($modID as $mid){$sqlMod.=" id='".$mid."' OR ";}$sqlMod=substr($sqlMod,0,strlen($sqlMod)-4);
      $sql="SELECT * FROM models WHERE section_id='".$section_id."' OR ".$sqlMod;
    }
    $result=mysql_query($sql) or die(mysql_error().": ".$sql);
    if($result and mysql_num_rows($result)>0)
    {
      $cnt=1;
      while($linea=mysql_fetch_array($result,MYSQL_ASSOC))
      {
        $cadnum="";
        if($cnt<10){$cadnum="00";}
        elseif($cnt<100){$cadnum="0";}
        $cadnum.=$cnt;
        $ref=$cadnum;
//        $ref=str_replace(" ","_",strtolower($this->noAcentos($linea['name'])));
        $this->tablas($nuevaCarpeta,$linea,$cadnum,$ref,$flagErase);
        $this->figuras($nuevaCarpeta,$htmldir,$linea,$cadnum,$ref,$flagErase);
        $cnt++;
      }
    }
    return true;
  }
  
  function crearOtrosTex($nuevaCarpeta)
  {
    $str="%Presentación del modelo.\n";
    $str.="%\\begin{wrapfigure}{r}{0.37\\textwidth}\n";
    $str.="%  \\vspace{-25pt}\n";
    $str.="%  \\begin{center}\n";
    $str.="%    \\includegraphics[width=6cm]{\\dirdoc figuras/foto.eps}\n";
    $str.="%  \\end{center}\n";
    $str.="%  \\vspace{-20pt}\n";
    $str.="%  \\caption{Foto.Tomada de \\MyUrl{http://XXX.}}\n";
    $str.="%  \\label{".$this->refPrefijo."fig:foto}\n";
    $str.="%  \\vspace{-10pt}\n";
    $str.="%\\end{wrapfigure}\n";

    $this->archivar('presentacion',$nuevaCarpeta."/presentacion.tex",$str);
    
    $str="%Descripción matemática detallada del modelo\n";
    $this->archivar('modelo',$nuevaCarpeta."/modelo.tex",$str);

    $str="%Descripción de la implementación modélica. Puede hacer uso de las instrucciones: \n";
    $str.="%\\input{\\dirdoc archivos} : incluye la lista de archivos modelica, y el código fuente de cada uno de ellos\n";
    $str.="%\\archivo{XXX.mo}{".$this->refPrefijo."} : referencia al archivo XXX.mo\n";
    $str.="\n\\input{\\dirdoc archivos}\n";
    $this->archivar('implementacion',$nuevaCarpeta."/implementacion.tex",$str);
    
    $str="\n";
    $this->archivar('referencias',$nuevaCarpeta."/referencias.tex",$str);
    
    $str="\\resumen{Descripción corta del modelo}\n";
    $this->archivar('resumen',$nuevaCarpeta."/resumen.tex",$str);
  }

  function crearEsqueleto($nuevaCarpeta,$texMain,$dirmodels,$modelDir,$sectionID,$modelsID,$htmldir)
  {
    $flag=$this->crearCarpeta($nuevaCarpeta);
    if($flag){$flag=$this->crearCarpeta($nuevaCarpeta."/exp");}else{return;}
    if($flag){$flag=$this->crearCarpeta($nuevaCarpeta."/expFig");}else{return;}
    if($flag){$flag=$this->crearCarpeta($nuevaCarpeta."/expTab");}else{return;}
    if($flag){$flag=$this->crearCarpeta($nuevaCarpeta."/expTex");}else{return;}
    if($flag){$flag=$this->crearCarpeta($nuevaCarpeta."/figuras");}else{return;}
    if($flag){$flag=$this->crearMainTex($nuevaCarpeta."/".$texMain);}else{return;}
    if($flag){$flag=$this->crearArchivosTex($dirmodels,$modelDir,$nuevaCarpeta."/archivos.tex",$sectionID);}else{return;}
    if($flag){$flag=$this->modelos($nuevaCarpeta,$sectionID,$modelsID,true,$htmldir);}else{return;}
    if($flag){$flag=$this->crearOtrosTex($nuevaCarpeta);}else{return;}    
    
  }
}

$dirmodels="/home/ogduartev/Proyectos/simula/";
$htmldir="/home/ogduartev/public_html/unvl/";
$E=new esqueleto();
if($E->conectar("localhost","root","rootpassword","unvlab"))
{
  echo "Uso: php esqueletoDoc.php -d nuevaCarpeta -t texMain -i directorioModelica -s sectionID -m modelsID(separados por coma) -r refPrefjo -n flagNuevo\n";
  echo "Ejemplo:\n php esqueletoDoc.php -d \"MotorDC\" -t \"docMotorDC\" -i \"DCmotor\" -s 1 -m 3,4,5 -r \"MDC\" -n \"TRUE\"
  \n";
  $options=getopt("d:t:i:s:m:r:x");

  $nuevaCarpeta=$texMain=$modelDir=$sectionID=$modelsID=$flagNuevo="";
  if(isset($options['d'])){$nuevaCarpeta     =$options['d'];}
  if(isset($options['t'])){$texMain          =$options['t'].".tex";}
  if(isset($options['i'])){$modelDir         =$options['i'];}
  if(isset($options['s'])){$sectionID        =$options['s'];}
  if(isset($options['m'])){$modelsID         =$options['m'];}
  if(isset($options['r'])){$E->refPrefijo    =$options['r'];}
  if(isset($options['n'])){$flagNuevo        =$options['n'];}
  if(!($flagNuevo)=="TRUE"){$E->actualizarExp();}
  if($nuevaCarpeta=="")               {$nuevaCarpeta="NUevaCarpeta";}
  if($texMain     ==".tex")           {$texMain     ="doc.tex";}
  if($modelDir    =="")               {echo "Error:\n  Se requiere el nombre del directorio con los archivos modelica\n";return;}
  if($sectionID=="" and $modelsID==""){echo "Error:\n  Se requiere al menos un id de sección o modelo\n";return;}
  $E->crearEsqueleto($nuevaCarpeta,$texMain,$dirmodels,$modelDir,$sectionID,$modelsID,$htmldir);
}else
{
  echo "No hay conexión con la base de datos \n";
}
?>
