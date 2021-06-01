<?//Российская История


	include("admin.inc.php");
	$op_names=array(
	"del"=>"Удаление опроса|2",
	"del_res"=>"Удаление результатов опроса|2",
	"del_all"=>"Удаление всех опросов|2",
	"stat"=>"Результаты опроса",
	"edit"=>"Редактирование параметров|1",
	"respondents"=>"Список респондентов",
	"user"=>"Респондент",
	"add"=>"Добавление/редактирование информации|1"
	);

	$ss=split("\|",$op_names[$go]);
	$s=$ss[0];
	$p=round($ss[1]);
	if (!$global_access || ($p && $p>$global_access && $global_access>=0)) {$s=$access_denied; $go=""; $id="";}

	if ($id && sub_access($id,"")<2 && $global_access<2) {$s=$access_denied; $go=""; $id="";}
	
	$tEvents="events";

	$tAuthors="authors";
	$tPart="participants";
	$cImgsPath="../img";
	$cRecsPerPage=30;
	$cEventLength=200;

	echo "<html>";
	echo "<head>
	<title>События</title>
	<link rel=stylesheet type=text/css href=site.css>
</head>

<style type=text/css>	
	#error {color:red;}
	#sm {font-size:10px; vertical-align:top;}
	#bigg {font-size:12px;}
	#td {font-family:verdana;font-size:10pt;background-color:c0c0c0;}
	th {font-family:verdana; text-align:center; font-size:10px; background-color:808080;}
	#thor {font-family:verdana; text-align:center; font-size:10px; background-color:orange;}
	#trr {background-color:D0D0D0}
</style>

<script>
function Push(id) {
	dd=top.opener;
	d=dd.document;
	d.History.fEventId.value=id;
}
</script>

<body bgcolor=E0E0E0 text=000000><table width=100%><tr><td>
";
	
//	head("Российская История",$s,"$ps?code=$code");

	$pass=table_exists($tMain,$db_name);
	
	$navbar=array(
	"2::\$pass::$ps?go=event&code=$code::Новое событие",
	"2::\$pass::$ps?go=chars&code=$code::Персонажи",
	"2::\$pass::$ps?go=authors&code=$code::Авторы"
	);	

//	navigation($navbar);
//	middle($s);

//	if (!$pass && $go!="uninstall" && $go!="install") {foot(); exit;}


	function quotes($x) {
		return str_replace("'","\'",$x);
	}


	echo "<b>События:</b><br>";

	echo "<table width=100% cellpadding=2 cellspacing=1>";
	echo "<colgroup><col span=2 valign=top><col id=smallest>";

	echo "<tr><th>&laquo;</th><th>id</th><th width=100%>Дата/событие</th></tr>";
//	$dChars=$dbMain->Query("SELECT * FROM $tEvents ORDER BY id, ROUND(date)");
	$dChars=$dbMain->Query("SELECT * FROM $tEvents ORDER BY from_date");
	$xChars=$dChars->NumRows();

	if ($fPage) $dChars->Seek($fPage);
	for ($i=0;$i<$cRecsPerPage;$i++) {
		if ($fPage+$i>=$xChars) break;
		$dChars->NextResult();

		$xId=$dChars->Get("id");
		echo "<tr id=trr><td><a href=#t onClick=\"Push('".$dChars->Get("id")."');\" id=ed>&laquo;</a></td>\n";
		echo "<td id=small>&nbsp;$xId&nbsp;</td>";
		echo "<td id=smallest><b>".MakeDate($dChars->Get("from_date"),$dChars->Get("till_date"))."</b><br>".nl2br(substr($dChars->Get("event"), 0, $cEventLength))."</td></tr>\n\n";
	}
	echo "</table>";

	$p=new Pages($xChars,$cRecsPerPage,$fPage);
	$p->SetScript($ps);
	$p->SetParameterName("fPage");
	$p->SetAddParams("code=$code&go=chars");
	
	$p->SetDivider(" | ");
	$p->SetText("События");
	$p->SetPrevNext("&laquo; назад","дальше &raquo;");


	$p->SetStyle("current","color:red; font-weight:bold");
	$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");

	echo "<center>";
	$p->PrintPages();
	echo "</center>";

	
//	foot();
	echo "</td></tr></table></body></html>";
?>