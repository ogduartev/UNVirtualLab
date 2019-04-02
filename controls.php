<?php
require_once('block.php');

class controls extends block
{  
  var $datos;
  function displayControl($data)
  {
    if(!isset($_POST['parameter_id_'.$data['parameter_id']])){$_POST['parameter_id_'.$data['parameter_id']]=$data['value'];}
    $_POST['parameter_id_'.$data['parameter_id']]=$this->validateValue($data['type'],
                                                                       $data['parameter_id'],
                                                                       $_POST['parameter_id_'.$data['parameter_id']],
                                                                       $data['value']);
    $value=$_POST['parameter_id_'.$data['parameter_id']];
    echo "     <tr>\n";
    
    echo "      <td class='controls' title='".$data['description']."'>\n";
    echo "        <div class='control_name'>\n";
    echo "         ".$data['name']."\n";
    if(strlen($data['units'])>0)
    {
      echo" (".$data['units'].")";
    }
    echo "        </div>\n";
    
    echo "      </td>\n";
    echo "      <td>\n";
    echo "        <div class=\"controls_change\" >\n";
    echo "          <input class='controls' name='parameter_id_".$data['parameter_id']."'  onchange=\"cambia(".$data['parameter_id'].");\"  autocomplete='off'>\n";
    echo "          <svg class='controls' xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" onload='Init(evt);' onmousemove='Drag(evt);' onmouseup='Drop(evt);' width='100%'>\n";
    echo "           <rect id=\"C".$data['parameter_id']."\" x=\"0%\" y=\"37.5%\" rx=\"4\" ry=\"4\" width=\"100%\" height=\"25%\" class='control_back'/>\n";

    $d=$this->datos[$data['parameter_id']];    
    $posx=($d['value']-$d['min'])/($d['max']-$d['min'])*100;

    echo "            <circle id=\"B".$data['parameter_id']."\" onmousedown='Grab(evt,".$data['parameter_id'].")'  cx=\"".$posx."%\" cy=\"50%\" r=\"6\"  class='control_bar'/>\n";
    echo "          </svg>\n";
    echo "        </div>\n";
    echo "      </td>\n";
    echo "      <td>\n";
    echo "        <div class=\"controls_change_arrows\" >\n";
    echo "          <div onclick=\"cambio(".$data['parameter_id'].",1);\"  class=\"controls_change_plus\"></div>\n";
    echo "          <div onclick=\"cambio(".$data['parameter_id'].",-1);\" class=\"controls_change_minus\"></div>\n";
    echo "        </div>\n";
    echo "      </td>\n";
    echo "     </tr>\n";
  }

  function displaySelect($data)
  {
    if(!isset($_POST['parameter_id_'.$data['parameter_id']])){$_POST['parameter_id_'.$data['parameter_id']]=$data['value'];}
    $_POST['parameter_id_'.$data['parameter_id']]=$this->validateValue($data['type'],
                                                                       $data['parameter_id'],
                                                                       $_POST['parameter_id_'.$data['parameter_id']],
                                                                       $data['value']);
    $value=$_POST['parameter_id_'.$data['parameter_id']];//echo $value;
    echo "     <tr>\n";

    echo "      <td class='controls' title='".$data['description']."'>\n";
    echo "        <div class='control_name'>\n";
    echo "         ".$data['name']."\n";
    if(strlen($data['units'])>0)
    {
      echo" (".$data['units'].")";
    }
    echo "        </div>\n";
    echo "      </td>\n";

    echo "      <td>\n";
    echo "        <div class=\"controls_change\" >\n";
    echo "         <select class='controls' name='parameter_id_".$data['parameter_id']."'>\n";
    $opciones=explode(";",str_replace("\n","",$data['allowedvalues']));
    foreach($opciones as $opcion)
    {
      $op=explode("/",str_replace("\n","",$opcion));
      if(count($op)<2){continue;}
      $valueOp=$op[0];
      $labelOp=$op[1];
      echo "          <option";
      echo  " value=\"".$valueOp."\"";
      if($value==$valueOp)
      {
        echo " selected";
      }
      echo ">";
      echo $labelOp;
      echo "</option>\n";
    }
    echo "         </select>\n";
    echo "        </div>\n";
    echo "      </td>\n";
    echo "     </tr>\n";
  }
  
  function displayControls($control_group_id)
  {
    if($control_group_id==0)
    {
      return;
    }
    $sql="SELECT controls.*,parameters.type FROM controls INNER JOIN parameters ON controls.parameter_id=parameters.id 
                   WHERE control_group_id='".$control_group_id."'";
    $sql.=$this->enabled("controls");
    $sql.=" ORDER BY name";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error().": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $allowed=explode(";",str_replace("\n","",$linea['allowedvalues']));
        if(count($allowed)>1)
        {
          $this->displaySelect($linea);
          $this->datos[$linea["parameter_id"]]["Select"]=1;
        }else
        {
          $this->displayControl($linea);
        }
      }
    }
  }

  function datos($model_id)
  {
    $this->datos=array();
    $sql="SELECT parameters.id,controls.min,controls.max,controls.step,controls.value,parameters.type FROM controls 
                            INNER JOIN control_groups ON controls.control_group_id=control_groups.id
                            INNER JOIN parameters ON controls.parameter_id=parameters.id
                            WHERE control_groups.model_id='".$model_id."'";
    $sql.=$this->enabled("control_groups");
    $sql.=$this->enabled("controls");
    $result=mysqli_query($this->link,$sql) or die(mysqli_error().": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $this->datos[$linea['id']]=array('min'=>$linea['min'],'max'=>$linea['max'],'step'=>$linea['step'],'value'=>$linea['value'],'type'=>$linea['type']);
        
        if(isset($_POST['parameter_id_'.$linea['id']]) and !isset($_POST['reset']))
        {
          $this->datos[$linea['id']]['value']=$_POST['parameter_id_'.$linea['id']];
        }
        $this->datos[$linea['id']]['value']=$this->validateValue($linea['type'],
                                                                 $linea['id'],
                                                                 $this->datos[$linea['id']]['value'],
                                                                 $linea['value']);
      }
    }
  }

  function javascript()
  {
    echo "<script type=\"text/javascript\" language=\"JavaScript\">\n";
    echo "<!--\n";
    echo "var ids=[";
    $tam=count($this->datos);
    $cnt=0;
    foreach($this->datos as $id=>$d)
    {
      $cnt++;
      echo $id;
      if($cnt<$tam){echo ",";}
    }    
    echo "];\n";
    echo "function minimoJS(id)\n";
    echo "{\n";
    echo "  switch(id)\n";
    echo "  {\n";
    foreach($this->datos as $id=>$d)
    {
      echo "    case ".$id." : return ".$d['min'].";\n";
    }
    echo "  }\n";
    echo "  return 0;\n";
    echo "}\n";
    echo "function maximoJS(id)\n";
    echo "{\n";
    echo "  switch(id)\n";
    echo "  {\n";
    foreach($this->datos as $id=>$d)
    {
      echo "    case ".$id." : return ".$d['max'].";\n";
    }
    echo "  }\n";
    echo "  return 0;\n";
    echo "}\n";
    echo "function inicialJS(id)\n";
    echo "{\n";
    echo "  switch(id)\n";
    echo "  {\n";
    foreach($this->datos as $id=>$d)
    {
      if(!isset($d["Select"]))
      {
        echo "    case ".$id." : return ".$d['value'].";\n";
      }
    }
    echo "  }\n";
    echo "  return 0;\n";
    echo "}\n";
    echo "function step(id)\n";
    echo "{\n";
    echo "  switch(id)\n";
    echo "  {\n";
    foreach($this->datos as $id=>$d)
    {
      echo "    case ".$id." : return ".$d['step'].";\n";
    }
    echo "  }\n";
    echo "  return 0;\n";
    echo "}\n";
    echo "function inicialJJ()\n";
    echo "{\n";
    echo "  for(var i=0;i<ids.length;i++)\n";
    echo "  {\n";
    echo "    var v=document.getElementsByName(\"parameter_id_\"+ids[i]);\n";
    echo "    var type=v[0].type;\n";
    echo "    if(type=='text')\n";
    echo "    {\n";
    echo "      v[0].value=inicialJS(ids[i]);\n";
    echo "    }\n";
    echo "  }\n";
    echo "}\n";
    echo "function cambio(id,sgn)\n";
    echo "{\n";
    echo "  var mini=minimoJS(parseInt(id));\n";
    echo "  var maxi=maximoJS(parseInt(id));\n";
    echo "  var v=document.getElementsByName(\"parameter_id_\"+id);\n";
    echo "  var x=parseFloat(v[0].value);\n";
    echo "  x=x+sgn*step(parseInt(id));\n";
    echo "  if(x < mini){x=mini}\n";
    echo "  if(x > maxi){x=maxi}\n";
    echo "  v[0].value=x.toString();\n";
    echo "  moverBarra(id,(x-inicialJS(id))/(maxi-mini));\n";
    echo "}\n";
    echo "function cambia(id)\n";
    echo "{\n";
    echo "  var mini=minimoJS(parseInt(id));\n";
    echo "  var maxi=maximoJS(parseInt(id));\n";
    echo "  var v=document.getElementsByName(\"parameter_id_\"+id);\n";
    echo "  var x=parseFloat(v[0].value);\n";
    echo "  if(x < mini){x=mini;}\n";
    echo "  if(x > maxi){x=maxi;}\n";
    echo "  v[0].value=x.toString();\n";
    echo "  moverBarra(id,(x-inicialJS(id))/(maxi-mini));\n";
    echo "}\n";
    echo "function moverBarra(id,xn)\n";
    echo "{\n";
    echo "  var b=document.getElementById(\"B\"+id);\n";
    echo "  var v=document.getElementById(\"C\"+id);\n";
    echo "  var l=v.getBBox().width;\n";
    echo "  b.setAttributeNS(null, 'transform', 'translate(' + xn*l  + ',0)');\n";
    echo "}\n";
    echo "//-->\n";
    echo "</script>\n";
  }

  function ecmascript()
  {
    echo "    <script type=\"text/ecmascript\">\n";
    echo "   <![CDATA[\n";
    echo "        var SVGDocument = null;\n";
    echo "        var SVGRoot = null;\n";
    echo "        var TrueCoords = null;\n";
    echo "        var GrabPoint = null;\n";
    echo "        var DragTarget = null;\n";
    echo "        var TextTarget = null;\n";
    echo "        var DragBack = null;\n";
    echo "        var id = null;\n";
    echo "        function Init(evt)\n";
    echo "        {\n";
    echo "           SVGDocument = evt.target.ownerDocument;\n";
    echo "           SVGRoot=evt.target;\n";
    echo "           TrueCoords = SVGRoot.createSVGPoint();\n";
    echo "           GrabPoint = SVGRoot.createSVGPoint();\n";
    echo "           inicialJJ();\n";
    echo "        };\n";
    echo "        function Grab(evt,idl)\n";
    echo "        {\n";
    echo "           var targetElement = evt.target;\n";
    echo "           if ( targetElement.id == 'B'+idl )\n";
    echo "           {\n";
    echo "             id=idl;\n";
    echo "             DragTarget = targetElement;\n";
    echo "             DragTarget.setAttributeNS(null, 'pointer-events', 'none');\n";
    echo "             DragBack=SVGDocument.getElementById('C'+id);\n";
    echo "             var T=SVGDocument.getElementsByName('parameter_id_'+id);\n";
    echo "             TextTarget=T[0];\n";
    echo "             var transMatrix = DragTarget.getCTM();\n";
    echo "             GrabPoint.x = TrueCoords.x - Number(transMatrix.e);\n";
    echo "             GrabPoint.y = TrueCoords.y - Number(transMatrix.f);\n";
    echo "           }else\n";
    echo "           {\n";
    echo "             DragTarget = null;\n";
    echo "             DragBack = null;\n";
    echo "             TextTarget = null;\n";
    echo "             id = null;\n";
    echo "           }\n";
    echo "        }\n";
    echo "        function Drag(evt)\n";
    echo "        {\n";
    echo "           GetTrueCoords(evt);\n";
    echo "           if (DragTarget && TextTarget)\n";
    echo "           {\n";
    echo "             var newX = TrueCoords.x - GrabPoint.x;\n";
    echo "             var mini = minimoJS(id);\n";
    echo "             var maxi = maximoJS(id);\n";
    echo "             var prop = (maxi-mini)/Number(DragBack.getBBox().width);\n";
    echo "             var posIni= (inicialJS(id)-mini)/prop;\n";
    echo "             var newVal = mini + (posIni + newX)*prop;\n";
    echo "             if(newVal < mini){newVal=mini;}\n";
    echo "             if(newVal > maxi){newVal=maxi;}\n";
    echo "             DragTarget.setAttributeNS(null, 'transform', 'translate(' + newX + ')');\n";
    echo "             TextTarget.value=newVal;\n";
    echo "           }\n";
    echo "        }\n";
    echo "        function Drop(evt)\n";
    echo "        {\n";
    echo "           if ( DragTarget )\n";
    echo "           {\n";
    echo "             DragTarget.setAttributeNS(null, 'pointer-events', 'all');\n";
    echo "             DragTarget = null;\n";
    echo "             TextTarget = null;\n";
    echo "             DragBack = null;\n";
    echo "           }\n";
    echo "        }\n";
    echo "        function GetTrueCoords(evt)\n";
    echo "        {\n";
    echo "           var newScale = SVGRoot.currentScale;\n";
    echo "           var translation = SVGRoot.currentTranslate;\n";
    echo "           TrueCoords.x = (evt.clientX - translation.x)/newScale;\n";
    echo "           TrueCoords.y = (evt.clientY - translation.y)/newScale;\n";
    echo "        }\n";
    echo "    ]]>\n";
    echo "    </script>\n";
  }

  function display()
  {
    $model_id=$this->model_id();
    $this->datos($model_id);
    if($model_id==0)
    {
      return;
    }
    $this->javascript($model_id);
    $sql="SELECT * FROM control_groups WHERE model_id='".$model_id."'";
    $sql.=$this->enabled("control_groups");
    $sql.=" ORDER BY name";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error().": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      echo "  <form method=post>\n";
      echo "   <div class='controls_buttons'>\n";
      echo "    <input type=hidden name='modelid' value='".$model_id."'>\n";
      echo "    <input type=submit class='controls_button' name='simulate' value='".$this->text('controls_Simulate')."'>\n";
      echo "    <input type=submit class='controls_button' name='reset' value='".$this->text('controls_Reset')."'>\n";
      echo "   </div>\n";
      
      echo "   <svg width=\"0px\" height=\"0px\" xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" x=\"0\" y=\"0\">\n";
      $this->ecmascript($model_id);
      echo "   </svg>\n";
      
      echo "   <table class=\"controls\">\n";
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        echo "    <thead>\n";
        echo "     <tr>\n";
        echo "      <th colspan=3 class='controls' title='".$linea['description']."'>".$linea['name']."</th>\n";
        echo "     </tr>\n";
        echo "    </thead>\n";
        echo "    <tbody>\n";
        $this->displayControls($linea['id']);
        echo "    <tbody>\n";
      }
      echo "   </table>\n";
      echo "  </form>\n";
    }
  }
}
?>
