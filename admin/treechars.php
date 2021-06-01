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
	
	$tMain="main";
	$tAuthors="authors";
	$tPart="participants";
	$tTree="tree";
	$cImgsPath="../img";
	$cRecsPerPage=30;

	if (!$part) {
		echo "<html>";
		echo "<head>
	<title>Персонажи - Родословная</title>
</head>

<frames>
<frameset cols=50%,50%>
	<frame name=chars src='treechars.php?code=$code&part=chars'>
	<frame name=tree src='treechars.php?code=$code&part=tree'>
</frameset>
</frames>
</html>";
		exit;
	}


	echo "<html>";
	echo "<head>
	<title></title>
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
	a:active {color:red}
</style>

<script>
function Push(what, id) {
    dd=top.opener;
    d=dd.document;

	if (dd.Here[id]!=1) {
		d.History.fParticipants.value+=what+'\\n';
		dd.Here[id]=1;
   	} else alert('Персонаж уже добавлен!');
}
</script>

<body bgcolor=E0E0E0 text=000000><table width=100%><tr><td>
";

	function quotes($x) {
		return str_replace("'","\'",$x);
	}


	if ($part=="store") {

	    while (list($k,$v)=each($_POST)) {
	        if (ereg("c_[0-9]+$",$k)) {
	            $k=str_replace("c_", "", $k);
				$dChars=$dbMain->Query("UPDATE $tPart SET intree='$v' WHERE id=$k");
			}
		}
		$part="chars";
		error("Изменения сохранены!");
	}
	
	if ($part=="chars") {
	    echo "<script>
	    active = 0;
</script>";
		
		echo "<b>Персонажи:</b><br>";

		echo "<form name=store action=$ps method=POST><input type=hidden name=code value='$code'><input type=hidden name=part value=store>";
		echo "<table width=100% cellpadding=2 cellspacing=1>";
		echo "<colgroup><col valign=top><col><col align=center>";

		echo "<tr><th width=100%>Персонаж/описание</th><th>Связь</th></tr>";
		$dChars=$dbMain->Query("SELECT * FROM $tPart ORDER BY name");
		$xChars=$dChars->NumRows();

		for ($i=0;$i<$xChars;$i++) {
			$dChars->NextResult();

			$xId=$dChars->Get("id");
			$xTreeId=$dChars->Get("intree");

			if ($xTreeId==0) $xTreeId="";

			echo "<tr id=trr>";
			echo "<td id=smallest><b>".$dChars->Get("name")."</b>";
			echo "</td><td>";
			echo "<input name=c_$xId value='$xTreeId' id=sform size=3 onFocus='active=$xId;'>";
			echo "</td></tr>\n\n";
		}
		echo "</table>";

		echo "</td></tr></table>";
		echo "<input type=submit value=' Сохранить ' id=sform></form>";
		
	}

	if ($part=="tree") {
	    echo "<script>
function Link(id) {
	current = parent.frames[\"chars\"].active;
    if (current) {
		obj = eval(\"parent.frames['chars'].store.c_\"+current);
		obj.value=id;
		obj.focus();
	}
}
</script>";

		echo "<b>Персонажи родословной:</b><br><br>";
		echo "<table width=100% cellpadding=2 cellspacing=1>";
		echo "<colgroup><col valign=top><col align=center>";

		echo "<tr><th width=100%>Персонаж</th><th>Связать</th></tr>";
		$dChars=$dbMain->Query("SELECT * FROM $tTree ORDER BY Name");
		$xChars=$dChars->NumRows();

		for ($i=0;$i<$xChars;$i++) {
			$dChars->NextResult();

			$xId=$dChars->Get("ID");

			echo "<tr id=trr>";
			echo "<td id=smallest><a href=../tree/?p=0&toID=$xId target=_tree><b>".$dChars->Get("Name")."</b></a>";
			echo "</td><td id=smallest>";
			echo "<a href=javascript:Link($xId) onClick='this.blur();'>связать</a>";
			echo "</td></tr>\n\n";
		}

		echo "</table>";

	}	

	echo "</body></html>";
?>