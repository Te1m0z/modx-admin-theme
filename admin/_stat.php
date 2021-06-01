<?//Статистика


	include "admin.inc.php";
	$op_names=array(
	"events"=>"События|2",
	"authors"=>"Авторы|2",
	"chars"=>"Персонажи|2"
	);

	$ss=split("\|",$op_names[$go]);
	$s=$ss[0];
	$p=round($ss[1]);
	if (!$global_access || ($p && $p>$global_access && $global_access>=0)) {$s=$access_denied; $go=""; $id="";}

	if ($id && sub_access($id,"")<2 && $global_access<2) {$s=$access_denied; $go=""; $id="";}
	
	$tMain="main";
	$tAuthors="authors";
	$tPart="participants";
	$tEvents="events";
	$tCath="cathegories";

	$cImgsPath="../img";
	$cThumbImgsPath="../img/thumbs";
	$cRecsPerPage=20;
	$cEventLength=200;

	head("Российская История",$s,"$ps?code=$code");

	$pass=table_exists($tMain,$db_name);
	
	$navbar=array();	

	navigation($navbar);
	middle($s);


	echo "<b>Статистика:</b><br><br>";

	echo "<ul type=square><li><b>Картинки:</b><br>";
	$r=query("SELECT * FROM $tMain ORDER BY id DESC", $db);
	echo "Общее количество: ".mysql_numrows($r)."<br>";
	echo "Последняя запись: ".mysql_result($r,0,"id").". &laquo;".mysql_result($r,0,"title")."&raquo;<br><br>";
	
	echo "<li><b>События:</b><br>";
	$r=query("SELECT * FROM $tEvents ORDER BY id DESC", $db);
	echo "Общее количество: ".mysql_numrows($r)."<br>";
	echo "Последняя запись: ".mysql_result($r,0,"id").". &laquo;".mysql_result($r,0,"event")."&raquo;<br><br>";
	
	echo "<li><b>Персонажи:</b><br>";
	$r=query("SELECT * FROM $tPart ORDER BY id DESC", $db);
	echo "Общее количество: ".mysql_numrows($r)."<br>";
	echo "Последняя запись: ".mysql_result($r,0,"id").". &laquo;".mysql_result($r,0,"name")."&raquo;<br><br>";

	echo "<li><b>Авторы:</b><br>";
	$r=query("SELECT * FROM $tAuthors ORDER BY id DESC", $db);
	echo "Общее количество: ".mysql_numrows($r)."<br>";
	echo "Последняя запись: ".mysql_result($r,0,"id").". &laquo;".mysql_result($r,0,"name")."&raquo;<br><br>";

	echo "<li><b>Категории:</b><br>";
	$r=query("SELECT * FROM $tCath ORDER BY id DESC", $db);
	echo "Общее количество: ".mysql_numrows($r)."<br>";
	echo "Последняя запись: ".mysql_result($r,0,"id").". &laquo;".mysql_result($r,0,"name")."&raquo;<br><br>";

	echo "</ul>";

	foot();
?>