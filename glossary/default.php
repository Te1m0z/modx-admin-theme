<?
	require "../inc/_config.inc.php";


	$tid=(int)$tid;
	
	$tMain="main";
	$tAuthors="authors";
	$tPart="participants";
	$tEvents="events";
	$tCath="cathegories";
	$tGloss="_glossary";

	$cCut=100;

	$cImgsPath="./img";
	$cThumbImgsPath="./img/thumbs";
	$cRecsPerPage=20;
	$cEventLength=200;

    $xAuthors=array();
    $dAuthors=$dbMain->Query("SELECT * FROM $tAuthors");
    for ($i=0;$i<$dAuthors->NumRows();$i++) {
    	$dAuthors->NextResult();
    	$xId=$dAuthors->Get("id");
    	$xName=$dAuthors->Get("name");
    	$xAuthors[$xId]=$xName;
    }
    $xCaths=array();
    $dCaths=$dbMain->Query("SELECT * FROM $tCath");
    for ($i=0;$i<$dCaths->NumRows();$i++) {
    	$dCaths->NextResult();
    	$xId=$dCaths->Get("id");
    	$xName=$dCaths->Get("name");
    	$xCaths[$xId]=$xName;
    }
    $xParts=array();
    $dParts=$dbMain->Query("SELECT * FROM $tPart");
    for ($i=0;$i<$dParts->NumRows();$i++) {
    	$dParts->NextResult();
    	$xId=$dParts->Get("id");
    	$xName=$dParts->Get("name");
    	$xParts[$xId]=$xName;
    }

    function quotes($a) {
    	return str_replace("'","\'",$a);
    }
			
	
	$tid=round($tid);
	$fL = substr($fL, 0, 3);
	
	Head("Глоссарий | $fL","glossary");
	echo "<table width=100%>";
	echo "<td width=100% valign=top>";

	
	if ($tid) {
		$dTerm=$dbMain->Query("SELECT * FROM $tGloss WHERE id=$tid");
		if ($dTerm->NumRows()) {
		 	$dTerm->NextResult();
		 	echo "<h1>".$dTerm->Get("name")."</h1>";
		 	echo "<div align=justify>".nl2br($dTerm->Get("description"))."</div>";
		}
		echo "</td><td valign=top>";
	}
	
	$xSearch=quotes($fSearch);
	$ad="";
	if ($fSearch) $ad="WHERE name LIKE '%$xSearch%'";
	else {
		if ($fL && $fL!="%") $ad="WHERE UPPER(name) LIKE '$fL%'";
		else $ad="WHERE SUBSTRING(UPPER(name),1,1)<'А' OR SUBSTRING(UPPER(name),1,1)>'Я'";
	}
		
	$dChars=$dbMain->Query("SELECT * FROM $tGloss $ad ORDER BY name");
	$xChars=$dChars->NumRows();

	echo "<h1>Термины:</h1>";
	echo "<ul type=square>";
	if ($fPage) $dChars->Seek($fPage);
	for ($i=0;$i<$cRecsPerPage;$i++) {
		if ($fPage+$i>=$xChars) break;
		$dChars->NextResult();

		$xId=$dChars->Get("id");
		echo "<li> <a href=$ps?tid=$xId&fL=$fL>".$dChars->Get("name")."</a>\n";
	}
	echo "</ul>";

	echo "<center>";
	LetterPages("code=$code&fL",$fL,$tGloss);


	$f=new Form(array("formname"=>"CathSearch","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"20","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
	$f->SetStyle("elements","id=small");
	$f->Hidden(array("name"=>"go","value"=>"caths"));
	$f->Hidden(array("name"=>"code","value"=>$code));

	$f->Text(array("name"=>"fSearch","value"=>$fSearch,"title"=>"Поиск:<br>","after"=>""));
	$f->SubmitReset(array("submittitle"=>" найти "));
//	$f->PrintForm();
	echo "</center>";

	echo "<br><img src=/i/spacer.gif width=250 height=1 border=0></td></tr></table>";

	Foot();
?>