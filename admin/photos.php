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
	<title>Картинки</title>
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
	d.History.$field.value=id;
}
</script>

<body bgcolor=E0E0E0 text=000000>";
	
	if ($filename) {
		$i=0;
		while (file_exists($cImgsPath."/".$filename)) {
			$filename=preg_replace("/((_[0-9]*)?(\.[^.]+))$/", "_".(++$i)."\\3", $filename);
			error($filename);
		}
	}
	echo "<script>Push('".preg_replace("/^.*\//", "", $filename)."');
	window.close();
</script>";
	
//	foot();
	echo "</body></html>";
?>