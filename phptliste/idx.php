<?php include("tliste.php"); ?>
<HTML>
<HEAD><TITLE>Index ASPSide</TITLE>
<LINK REL=STYLESHEET TYPE='text/css' HREF='./tli.css'>
<script src="jquery.js" type="text/javascript"></script>
<script type="text/javascript"><!--
function tliste_clicks(id, frag)
{
		  jQuery(id +' a.jq').bind("click",
			function()
			{
				jQuery(id + ' a.jq').unbind("click");
				var	lien=this.href.replace("idx.php", frag); 

				jQuery(id).load(lien ,
											  function()
		       							{ 
													tliste_clicks(id);
												}
           				 );
        return false ;
      }
     );
}

jQuery(document).ready(
	function()
	{
	tliste_clicks('#dtli2', 'j2x.php');
	tliste_clicks('#dtli1', 'j1x.php');
	} 
); 
// --></script>
</HEAD>
<BODY bgcolor=#D0F0FF link=#000000 vlink=#000000 alink=#000000>


<?php 
	$tree1 = new tliste("_1") ;
	$tree2 = new tliste("_2", "txt/tliste2.txt") ;
?>
<table width="80%">
	<tr valign="top">
		<td width="50%" id="dtli1"><?php $tree1->display() ; ?></td>
		<td width="50%" id="dtli2"><?php $tree2->display() ; ?></td>
	</tr>
</table>
</body>
</HTML>

