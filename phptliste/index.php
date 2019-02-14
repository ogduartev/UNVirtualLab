<?php include("tliste.php"); ?>
<?php include("tlistemysql.php"); ?>
<HTML>
<HEAD><TITLE>PhptListe</TITLE>
<LINK REL=STYLESHEET TYPE='text/css' HREF='./tli.css'>
</HEAD>
<BODY bgcolor=#D0F0FF link=#000000 vlink=#000000 alink=#000000>


<?php 
	$tree1 = new tliste("_1") ;
	$tree2 = new tliste("_2", "txt/tliste2.txt") ;
	$tree3 = new tlistemysql("_3") ;
?>
<table width="80%" border=2>
<tr valign="top">
<td width="33%"><?php  $tree1->display() ; ?></td>
<td width="33%"><?php  $tree2->display() ; ?></td>
<td width="33%"><?php  $tree3->display() ; ?></td>
</tr>
</body>
</HTML>
