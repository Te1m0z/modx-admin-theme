<?php
$db_name="history";
$host="localhost";
$user="history";
$pass="ih9baiPh7quivoogohxu";
$db = mysql_connect($host, $user, $pass);
mysql_select_db($db_name, $db) or die("Can't select DataBase!<br>\n"); 
mysql_query("set names 'cp1251'", $db);


$sql="SELECT * FROM tree";
$res = mysql_query($sql);

//print 0;
while($l = mysql_fetch_array($res))
{
	print "<url>
<loc>http://history.sgu.ru/tree/?toID=".$l["ID"]."</loc>
<lastmod>2011-04-26</lastmod>
</url>
";

}
print"<br>";
