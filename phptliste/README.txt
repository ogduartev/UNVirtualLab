=== PhpTliste ===
Name: PhpTliste
Script  URI: http://www.berthou.com/us/2008/03/27/phptliste-an-opensource-treeview-in-php/
Author : rberthou
Author URI: http://www.berthou.com/us/
Tags: treeview, menu
Description: PhpTliste is a Treeview script
Version: 1.0.0

== Description ==
Treeview is the first component that I have developed (at the time in C + + for Windows 3). Since then I accommodate this development in various languages.
So I present you the last of the Tliste family PHP version. This component is a freeware under license GPL.

This component is a set of two classes PHP tliste the base class and rd_l which represents an element of the list. It is, in my opinion, relatively easy 
to use and easily adaptable. It’s still a “beta” version, which should be cleaned and I think also to produce two distinct versions for PHP-4 and PHP-5.


== sample == 
<?php include("tliste.php"); ?>
<html>
<head><title>Exemple tListe PHP</title>
<link REL=STYLESHEET TYPE='text/css' HREF='./tli.css'>
</head>
<body>
<?php 
	$tree1 = new tliste("_1") ;
	$tree2 = new tliste("_2", "txt/tliste2.txt") ;
?>
<table width="80%"><tr valign="top">
<td width="50%"><?php  $tree1->display() ; ?></td>
<td width="50%"><?php  $tree2->display() ; ?></td>
</tr></table>
</body></html>

== sample data file ==
0  §  News      § 14  §  http://www.berthou.com/us/
0  §  Langages  §  1  §  http://www.javaside.com/us/lang_1.html
1  §  JAVA      §  1  §  http://www.javaside.com/us/java_f.html	§ ?applet liste
2  §  Applets   §  24  
3  §  tListe    §  34  §  http://www.javaside.com/j_tliste.html	§ ?This applet
3  §  tMbutton  §  21  §  http://www.javaside.com/j_tmbutton.html	§ ?Tabs applet
3  §  tFunction §  20  §  http://www.javaside.com/j_tfunction.html	§ ?draw y=f(x) 
3  §  tChart	§  27  §  http://www.javaside.com/j_tchart.html	§ ?Draw a chart	
3  §  tScroll	§  3  §  http://www.javaside.com/j_tscrol.html	§ ?Texte Scrolling
3  §  tButton   §  3  §  http://www.javaside.com/j_butto.html	§ ?3D Button
2  §  Jeux    §  24  
3  §  Goban     §  31  §  http://www.javaside.com/j_goban.html	§ ?Multiplayer Go Game	
3  §  JVMine	§  31  §  http://www.javaside.com/j_jvmine.html	§ ?MineSweeper game
1  §  C / C++   §  0  §  http://www.javaside.com/us/cpp_f.html
2  §  BC++5 Faq §  3  §  http://www.mdex.net/~kentr/bc50faq.htm  §  _top
2  §  Sources   §  3  §  http://www.pfdpf.state.oh.us/msawczyn/files/owlfiles.htm  §  _top
2  §  FAQ WinDev § 3  §  http://www.r2m.com/win-developer-FAQ/  §  _top
0  §  Documentation § 0  §  http://www.javaside.com/us/net_1.html
1  §  Presse     § 0
2  §  01 Info    § 3  §  http://www.01-informatique.com/  §  _top
2  §  LMI        § 3  §  http://www.lmi.fr/  		  §  _top
2  §  Informatique § 3  §  http://techweb.cmp.com/ifm     §  _top
1  §  Manuels     § 0
2  §  UNGI        § 3  §  http://www.imaginet.fr/ime      §  _top
2  §  Man HTML    § 3  §  http://www.imag.fr/Multimedia/miroirs/manuelhtml/manuelhtml.html  §  _top
2  §  HTML Goodies § 3  §  http://www.htmlgoodies.com	  §  _top