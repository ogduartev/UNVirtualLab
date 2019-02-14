<?php
require_once("block.php");
require_once("logo.php");

class adminblock extends block
{  
  var $fields;
  var $table;
  var $sectionidname;
  var $idname;
  var $idvalue;
  var $blockname;
  
  var $relations1N;
  var $relations1N1N;
  var $relations1NN1; // p.ej. modellers
    
  function selectSection($level,$section_sql,$section_id)
  {
    $sql="SELECT id,name,description FROM sections WHERE section_id".$section_sql;
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $selected="";
        if($section_id==$linea['id']){$selected="selected=\"yes\"";}
        echo "<option value=\"".$linea['id']."\" ".$selected.">";
        for($i=0;$i<$level;$i++){echo "-";}
        echo $linea['name']."</option>\n";
        $this->selectSection($level+1,"=".$linea['id'],$section_id);
      }
    }
  }
    
  function selectFromTable($table,$model_id,$variable_id)
  {
    $sql="SELECT id,modelicaname,alias,description FROM ".$table." WHERE model_id=".$model_id." ORDER BY modelicaname";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $selected="";
        if($variable_id==$linea['id']){$selected="selected=\"yes\"";}
        echo "<option value=\"".$linea['id']."\" ".$selected.">";
        echo $linea['modelicaname']." (".$linea['alias'].")</option>\n";
      }
    }
  }
  
  function selectOptions($F,$id)
  {
    $sql="SELECT ".$F['dbname']." AS D FROM ".$F['table']." WHERE id=".$id;
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);echo $linea['D'];
      foreach($F['options'] as $O=>$V)
      {
        $selected="";
        if($V==$linea['D']){$selected="selected=\"yes\"";}
        echo "      <option value=\"".$V."\" ".$selected.">".$O."</option>\n";
      }
    }
  }
  
  function displayOneField($data,$F,$class)
  {
      switch($F['type'])
      {
        case "bool": 
                     $selected=""; 
                     echo "\n    <select name=\"db_".$F['dbname']."\" class=\"".$class."\">\n";
                     if($data[$F['dbname']]=='1'){$selected="selected=\"yes\"";}else{$selected="";}
                     echo "      <option value=\"1\" ".$selected.">".$this->text("adminblock_Yes")."</option>\n";
                     if($data[$F['dbname']]=='0'){$selected="selected=\"yes\"";}else{$selected="";}
                     echo "      <option value=\"0\" ".$selected.">".$this->text("adminblock_No")."</option>\n";
                     echo "    </select>\n";
                     break;
        case "check": 
                     $checked=""; 
                     if($F['value']=='1'){$checked="checked";}else{$checked="";}
                     echo "<input type=\"checkbox\" name=\"db_".$F['dbname']."\" class=\"".$class."\" value=\"1\" ".$checked.">";
                     break;
        case "int":
        case "float":
        case "text": 
                     echo "<input name=\"db_".$F['dbname']."\" value='".str_replace("'","\"",$data[$F['dbname']])."' class=\"".$class."\"></input>";
                     break;
        case "longtext": 
                     echo "<textarea name=\"db_".$F['dbname']."\" class=\"".$class."\">".$data[$F['dbname']]."</textarea>";
                     break;
        case "select options":
                     echo "<select name=\"db_".$F['dbname']."\" class=\"".$class."\">\n";
                     $this->selectOptions($F,$data['id']);
                     echo "</select>\n";
                     break;
        case "select section":
                     if($data['section_id']>0)
                     {
                       echo "<select name=\"db_".$F['dbname']."\" class=\"".$class."\">\n";
                       $this->selectSection(0," IS NULL",$data['section_id']);
                       echo "</select>\n";
                     }
                     break;
        case "select variable":
                     echo "<select name=\"db_".$F['dbname']."\" class=\"".$class."\">\n";
                     $this->selectFromTable("variables",$F['model_id'],$data[$F['dbname']]);
                     echo "</select>\n";
                     break;
        case "select parameter":
                     echo "<select name=\"db_".$F['dbname']."\" class=\"".$class."\">\n";
                     $this->selectFromTable("parameters",$F['model_id'],$data[$F['dbname']]);
                     echo "</select>\n";
                     break;
        case "color" :
                     echo "<input class='color' name=\"db_".$F['dbname']."\" value=\"".$data[$F['dbname']]."\">\n";
                     break;
        case "model link":
                     echo "<a href=\"admin.php?modelid=".$data[$F['dbname']]."\">".$data[$F['dbname']]."</a>\n";
                     break;
        case "fixed":
        default: 
                     echo $data[$F['dbname']];
                     break;  
      }
  }
    
  function displayFields($data)
  {
    foreach($this->fields as $F)
    {
      echo " <tr>\n";
      echo "  <td class=\"update_name\">";
      echo $F['showname'];
      echo "</td>\n";
      echo "  <td class=\"update_value\">";
      $this->displayOneField($data,$F,"normalUpdate");
      echo "</td>\n";
    }
  }
  
  function update()
  {
    if(isset($_POST['update']))
    {
      $DB=new databasemanager();
      $DB->update($this->table,$this->idname,$this->idvalue);
    }
    $this->update1NN1();
  }
  
  function update1NN1()
  {
    if(isset($_POST['update1NN1']))
    {
      $DB=new databasemanager();
      $DB->update1NN1($this->idvalue);
    }
  }
  
  function insert()
  {
    if(isset($_POST['insert']))
    {
      $DB=new databasemanager();
      $id=$DB->insert();
      if(isset($_POST['insert_table']) and $_POST['insert_table']=="models")
      {
        $DB->createDirectories($id);
      }
    }
    $this->insert1NN1();
  }
  
  function insert1NN1()
  {
    if(isset($_POST['insert1NN1']))
    {
      $DB=new databasemanager();
      $DB->insert1NN1($this->idvalue);
    }
  }
  
  function delete()
  {
    if(isset($_POST['delete']))
    {
      $DB=new databasemanager();
      $DB->delete();
      if(isset($_POST['delete_table']) and $_POST['delete_table']=="models")
      {
        $DB->deleteDirectories($_POST['delete_id']);
      }
    }
    $this->delete1NN1();
  }
  
  function delete1NN1()
  {
    if(isset($_POST['delete1NN1']))
    {
      $DB=new databasemanager();
      $DB->delete1NN1($_POST['delete_id']);
    }
  }
  
  function createRelations1N()
  {
    $this->Relations1N=array();
  }  
  
  function createRelations1N1N()
  {
    $this->Relations1N1N=array();
  }  
  
  function createRelations1NN1()
  {
    $this->Relations1NN1=array();
  }  
    
  function displayOwn()
  {
    echo "<form method=\"POST\" action=\"admin.php\">\n";
    echo "<table class=\"update\">\n";
    echo "<tr><th class=\"update_title\" colspan='2'>".$this->blockname."</th></tr>";
    $sql="SELECT * FROM ".$this->table." WHERE ".$this->idname."='".$this->idvalue."'";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      $this->displayFields($linea);
    }
    echo "</table>\n";
    echo "<input type=\"hidden\" name=\"".$this->sectionidname."\" value=\"".$this->idvalue."\">\n";
    echo "<div class=\"controls_buttons\">\n";
    echo "<input type=\"submit\" class=\"controls_button\" name=\"update\" value=\"".$this->text("adminblock_Update")."\"/>\n";
    echo "</div>\n";
    echo "</form>\n";
  }
  
  function displayOneRelation1N($rel)
  {
    echo "<table class=\"update\">\n";
    echo "<tr><th class=\"update_title\"colspan='2'>".$rel['title']."</th></tr>";
    echo "<tr><th class=\"update_title\">".$rel['show']."</th><th class=\"update_title\">".$this->text("adminblock_Actions")."</th></tr>\n";
    $sql="SELECT ".$rel['id1']." AS ID, ".$rel['showdbname']. " AS NAME FROM ".$rel['table']." WHERE ".$rel['id2']."=".$rel['idvalue'];
//    echo $sql;
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        echo " <tr>\n";
        echo "  <td class=\"update_name\">";
        echo $linea['NAME'];
        echo "  </td>\n";
        echo "  <td class=\"update_name\">\n";
        echo "   <form action=\"admin.php\" method=\"POST\">\n";
        echo "    <div class=\"controls_buttons\">\n";
        echo "     <input type=\"hidden\" name=\"".$rel['linkname']."\" value=\"".$linea['ID']."\">\n";
        echo "     <input type=\"submit\" class=\"controls_button\" name=\"edit\"   value=\"".$this->text("adminblock_Edit")."\">\n";
        echo "     <input type=\"hidden\" name=\"delete_table\" value=\"".$rel['table']."\">\n";
        echo "     <input type=\"hidden\" name=\"delete_colid\" value=\"".$rel['id1']."\">\n";
        echo "     <input type=\"hidden\" name=\"delete_id\" value=\"".$linea['ID']."\">\n";
        echo "     <input type=\"submit\" class=\"controls_button\" name=\"delete\" value=\"".$this->text("adminblock_Delete")."\" onClick=\"return confirm('".$this->text("adminblock_Delete_confirm")."')\">\n";
        echo "    </div>\n";
        echo "   </form>\n";
        echo "  </td>";
        echo " </tr>\n";
      }
    }
    echo " <tr>\n";
    echo "  <form action=\"admin.php\" method=\"post\">\n";
    echo "   <td class=\"update_name\">";
    echo "     <input type=\"text\" name=\"db_".$rel['showdbname']."\" value=\"".$this->text("adminblock_No_name")."\">\n";
    echo "   </td><td class=\"update_name\">";
    echo "    <div class=\"controls_buttons\">\n";
    echo "     <input type=\"hidden\" name=\"".$this->sectionidname."\" value=\"".$this->idvalue."\">\n";
//    echo "     <input type=\"hidden\" name=\"".$rel['linkname']."\" value=\"".$rel['idvalue']."\">\n";
    echo "     <input type=\"hidden\" name=\"insert_table\" value=\"".$rel['table']."\">\n";
    echo "     <input type=\"hidden\" name=\"db_".$rel['id2']."\" value=\"".$rel['idvalue']."\">\n";
    if(isset($rel['id3']))
    {
      echo "     <input type=\"hidden\" name=\"db_".$rel['id3']."\" value=\"".$rel['idvalue3']."\">\n";
    }
    echo "     <input type=\"submit\" class=\"controls_button\" name=\"insert\" value=\"".$this->text("adminblock_Add")."\">\n";
    echo "    </div>\n";
    echo "   </td>";
    echo "  </form>\n";
    echo " </tr>\n";
    echo "</table>\n";
  }
  
  function displayRelations1N()
  {
    foreach($this->Relations1N as $rel1N)
    {
      $this->displayOneRelation1N($rel1N);
    }
  }

  function displayOneRelation1N1N($rel)
  {
    $ncols=count($rel['subfields']);
  
    echo "<table class=\"update\">\n";
    echo "<tr><th class=\"update_title\" colspan='".($ncols+2)."'>".$rel['title']."</th></tr>";
    echo "<tr><th class=\"update_title\"></th><th class=\"update_title\" colspan='".($ncols+1)."'>Datos</th></tr>\n";
    echo "<tr><th class=\"update_title\">".$rel['show']."</th>\n";
    foreach($rel['subfields'] as $K=>$F)
    {
      echo "<th class=\"update_title\">".$F['showname']."</th>";
    }
    echo "<th class=\"update_title\">Acciones</th></tr>\n";
    $sql="SELECT ".$rel['id1']." AS ID, ".$rel['showdbname']." AS NAME, ".$rel['table'].".* FROM ".$rel['table']." WHERE ".$rel['id2']."=".$rel['idvalue'];
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        echo " <tr>\n";
        echo "   <form action=\"admin.php\" method=\"POST\">\n";
        echo "  <td class=\"update_name\">";
        echo $linea['NAME'];
        echo "  </td>\n";
        foreach($rel['subfields'] as $FF)
        {
          echo "  <td class=\"update_value\">";
          $this->displayOneField($linea,$FF,"miniUpdate");
          echo "</td>\n";
        }
        echo "  <td class=\"update_name\">\n";
        echo "    <div class=\"controls_buttons\">\n";
        echo "     <input type=\"hidden\" name=\"".$this->sectionidname."\" value=\"".$this->idvalue."\">\n";
        echo "     <input type=\"hidden\" name=\"".$rel['linkname']."\" value=\"".$linea['ID']."\">\n";
        echo "     <input type=\"submit\" class=\"controls_button\" name=\"update\"   value=\"".$this->text("adminblock_Update")."\">\n";
        echo "     <input type=\"hidden\" name=\"delete_table\" value=\"".$rel['table']."\">\n";
        echo "     <input type=\"hidden\" name=\"delete_colid\" value=\"".$rel['id1']."\">\n";
        echo "     <input type=\"hidden\" name=\"delete_id\" value=\"".$linea['ID']."\">\n";
        echo "     <input type=\"submit\" class=\"controls_button\" name=\"delete\" value=\"".$this->text("adminblock_Delete")."\" onClick=\"return confirm('".$this->text("adminblock_Delete_confirm")."')\">\n";
        echo "    </div>\n";
        echo "  </td>";
        echo "   </form>\n";
        echo " </tr>\n";
      }
    }
    // INSERT 
    
    $sql="SELECT id FROM ".$this->varpar."s WHERE model_id='".$this->findModelId()."' LIMIT 1";
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      $linea=mysqli_fetch_array($result,MYSQLI_ASSOC);
      echo " <tr>\n";
      echo "   <td class=\"update_name\" colspan=\"".($ncols+2)."\">";
      echo "    <form action=\"admin.php\" method=\"post\">\n";
      echo "     <input type=\"text\" name=\"db_".$rel['showdbname']."\" value=\"".$this->text("adminblock_No_name")."\" class=\"normalUpdate\">\n";
      echo "     <input type=\"hidden\" name=\"".$this->sectionidname."\" value=\"".$this->idvalue."\">\n";
      echo "     <input type=\"hidden\" name=\"insert_table\" value=\"".$rel['table']."\">\n";
      echo "     <input type=\"hidden\" name=\"db_".$rel['id2']."\" value=\"".$this->idvalue."\">\n";
      echo "     <input type=\"hidden\" name=\"db_".$this->varpar."_id\" value=\"".$linea['id']."\">\n";    
      echo "     <input type=\"submit\" class=\"controls_button\" name=\"insert\" value=\"".$this->text("adminblock_Add")."\">\n";
      echo "    </form>\n";
      echo "   </td>";
      echo " </tr>\n";
    }
    
    echo "</table>\n";
  }
    
  function displayRelations1N1N()
  {
    foreach($this->Relations1N1N as $rel1N1N)
    {
      $this->displayOneRelation1N1N($rel1N1N);
    }
  }
  
  function displayOneRelation1NN1($rel)
  {
    $ncols=count($rel['subfields']);

    echo "<table class=\"update\">\n";
    echo "<tr><th class=\"update_title\" colspan='".($ncols+2)."'>".$rel['title']."</th></tr>";
    echo "<tr><th class=\"update_title\">".$rel['show']."</th>\n";
    foreach($rel['subfields'] as $K=>$F)
    {
      echo "<th class=\"update_title\">".$F['showname']."</th>";
    }
    echo "<th class=\"update_title\">Acciones</th></tr>\n";
    // SELECTED
    $id_selected=array();
    $sql="SELECT ".$rel['idLink2']." AS IDSELECTED FROM ".$rel['tableLink']." WHERE ".$rel['idLink1']."=".$rel['idvalue'];
    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        $id_selected[]=$linea['IDSELECTED'];
      }
    }
    // TABLE
    
    $sql="SELECT *,id AS ID FROM ".$rel['table2'];
    $cnt=0;
    foreach($rel["order"] as $or)
    {
      if($cnt==0)
      {
        $sql.=" ORDER BY ";
      }
      $cnt++;
      $sql.=$or;
      if($cnt<count($rel["order"]))
      {
        $sql.=", ";
      }
    }

    $result=mysqli_query($this->link,$sql) or die(mysqli_error($this->link).": ".$sql);
    if($result and mysqli_num_rows($result)>0)
    {
      while($linea=mysqli_fetch_array($result,MYSQLI_ASSOC))
      {
        echo " <tr>\n";
        echo "   <form action=\"admin.php\" method=\"POST\">\n";
        $F=array("dbname"=>"modeller_id","showname"=>"","type"=>"check","value"=>0);
        if(in_array($linea['id'],$id_selected))
        {
          $F['value']=1;
        }
        $data=array();
        echo "     <td class=\"update_value\">";
        echo $this->displayOneField($data,$F,"checkUpdate");
        echo "       <input type=\"hidden\" name=\"tableLink\" value=\"".$rel['tableLink']."\">\n";
        echo "       <input type=\"hidden\" name=\"table2\" value=\"".$rel['table2']."\">\n";
        echo "       <input type=\"hidden\" name=\"id1\" value=\"".$rel['id1']."\">\n";
        echo "       <input type=\"hidden\" name=\"id2\" value=\"".$rel['id2']."\">\n";
        echo "       <input type=\"hidden\" name=\"idLink1\" value=\"".$rel['idLink1']."\">\n";
        echo "       <input type=\"hidden\" name=\"idLink2\" value=\"".$rel['idLink2']."\">\n";
        echo "</td>\n";
        foreach($rel['subfields'] AS $FF)
        {
          echo "  <td class=\"update_value\">";
          $this->displayOneField($linea,$FF,"mediumUpdate");
          echo "</td>\n";          
        }
        echo "    <td class=\"update_value\">\n";
        echo "      <div class=\"controls_buttons\">\n";
        echo "       <input type=\"hidden\" name=\"".$this->sectionidname."\" value=\"".$this->idvalue."\">\n";
        echo "       <input type=\"hidden\" name=\"".$rel['linkname']."\" value=\"".$linea['ID']."\">\n";
        echo "       <input type=\"hidden\" name=\"id2Value\" value=\"".$linea['ID']."\">\n";
        echo "       <input type=\"submit\" class=\"controls_button\" name=\"update1NN1\" value=\"".$this->text("adminblock_Update")."\">\n";
        echo "       <input type=\"hidden\" name=\"delete_id\" value=\"".$linea['ID']."\">\n";
        echo "       <input type=\"submit\" class=\"controls_button\" name=\"delete1NN1\" value=\"".$this->text("adminblock_Delete")."\" onClick=\"return confirm('".$this->text("adminblock_Delete_confirm")."')\">\n";
        echo "      </div>\n";
        echo "    </td>\n";
        echo "   </form>\n";
        echo " </tr>\n";
      }
    }
    
    // INSERT
    echo " <tr>\n";
    echo "  <form action=\"admin.php\" method=\"post\">\n";
    echo "   <td class=\"update_value\">\n";
    echo "     <input type=\"hidden\" name=\"tableLink\" value=\"".$rel['tableLink']."\">\n";
    echo "     <input type=\"hidden\" name=\"table2\" value=\"".$rel['table2']."\">\n";
    echo "     <input type=\"hidden\" name=\"idLink1\" value=\"".$rel['idLink1']."\">\n";
    echo "     <input type=\"hidden\" name=\"idLink2\" value=\"".$rel['idLink2']."\">\n";
    echo "   </td>\n";
    foreach($rel['subfields'] AS $FF)
    {
      echo "  <td class=\"update_value\">\n";
      if($FF['type']=="text")
      {
        echo "     <input type=\"text\" name=\"db_".$FF['dbname']."\" value=\"".$this->text("adminblock_No_name")."\" class=\"mediumUpdate\">\n";
      }
      echo "  </td>\n";          
    }
    echo "  <td class=\"update_value\">\n";
    echo "    <div class=\"controls_buttons\">\n";
    echo "     <input type=\"hidden\" name=\"".$this->sectionidname."\" value=\"".$this->idvalue."\">\n";
    echo "     <input type=\"hidden\" name=\"insert_table\" value=\"".$rel['table']."\">\n";
    echo "     <input type=\"submit\" class=\"controls_button\" name=\"insert1NN1\" value=\"".$this->text("adminblock_Add")."\">\n";
    echo "    </div>\n";
    echo "   </td>\n";
    echo "  </form>\n";
    echo " </tr>\n";
    
    echo "</table>\n";
  }

  function displayRelations1NN1()
  {
    foreach($this->Relations1NN1 as $rel1NN1)
    {
      $this->displayOneRelation1NN1($rel1NN1);
    }
  }
  
  function createFieldsAndRelations()
  {
    $this->createFields();
    $this->createRelations1N();
    $this->createRelations1N1N();
    $this->createRelations1NN1();
  }
  
  function displayPostOwn(){}
  
  function displayAll()
  {
    $this->displayOwn();
    $this->displayPostOwn();
    $this->displayRelations1N();
    $this->displayRelations1N1N();
    $this->displayRelations1NN1();
  }  
  
  function display()
  {
    $this->createFieldsAndRelations();
    if($this->idvalue==0)
    {
      return;
    }
    $this->update();
    $this->insert();
    $this->delete();
    
    $this->displayAll();
  }
  
}
?>

