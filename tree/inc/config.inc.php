<?

$dbHostName="localhost";
$dbUserName="history";
$dbPassword="ih9baiPh7quivoogohxu";
$dbDataBase="history";


$link = mysql_connect($dbHostName, $dbUserName, $dbPassword) or die ("Невозможно установить соеденение c <b>$dbUserName@</b>$dbHostName");
mysql_select_db ($dbDataBase,$link) or die ("Не возможно выбрать базу данных: <b>$dbDataBase</b>");
mysql_query("set names 'cp1251'", $link);
  
function qu($str){
	$result=mysql_query($str) or die("<span class=error><br><b>[!]</b>Невозможно выполнить запрос: <b>".$str."</b><br>Сервер ответил: <b>".mysql_error()."</b></span>");
	//show_log("выполняется запрос: <b>$str</b>");
	return $result;
}
function quotes($str){
	$str=str_replace('"','\"',$str);
	$str=str_replace("<",'&lt;',$str);
	$str=str_replace(">",'&gt;',$str);
	return $str;
}
function show_log($str){
	echo "<div class=log><b>[!]</b> $str</div>";
	return;
}

?>
