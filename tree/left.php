<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>LeftFrame</title><link rel="STYLESHEET" type="text/css" href="css/style.css">
</head>

<body>
<script>
function DeletePerson(id,name){
	if(confirm('Вы действительно хотите удалить этот персонаж `'+name+'` из БАЗЫ?')) document.location='left.php?DeletePerson='+id;
}
</script>
<style>td{color:#666; font:11px Tahoma}A:link{color:#666;font:bold 10px Verdana;text-decoration:none;}A:visited{color:#666;font:bold 10px Verdana;text-decoration:none;}A:hover{color:#666;text-decoration:none}#fl{color:red;text-decoration:none}#ac{color:blue;text-decoration:none}</style> 

<li> <a href=./manual.html target="_blank">мануал</a>
<li> <a href=./left.php>к списку</a>


<b>Персонажи:</b><br>

<?


include_once("inc/config.inc.php");

if($_GET["DeletePerson"]!=0){
	query("DELETE FROM tree WHERE ID=".$_GET["DeletePerson"]);
	show_log("Персонаж был удален");
}

if($_GET["FindPerson"]!=""){
	$FindPerson=$_GET["FindPerson"];
	$search="WHERE Name LIKE '$FindPerson%'";
}

echo "<form action=./left.php>";
echo "<input type=text name=FindPerson value=''><input type=Submit value='Найти'>";
echo "</form>";

$result=query("SELECT ID,Name FROM tree $search ORDER BY Name");
for($i=0;$i<@mysql_numrows($result);$i++){
	$id=mysql_result($result,$i,"ID");
	$name=mysql_result($result,$i,"Name");
	echo "\n<li type=disc><nobr><a href=\"javascript:DeletePerson($id,'$name');\">#</a> <a href=./add.php?action=view&toID=".$id." target=TopTree>".$name."</nobr></a>";
}


?>


</body>
</html>
