<?
	require "../inc/_config.inc.php";

	
	
	$tMain="main";
	$tAuthors="authors";
	$tPart="participants";
	$tEvents="events";
	$tCath="cathegories";

	$cImgsPath="../img";
	$cThumbImgsPath="../img/thumbs";
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
			

	$pid=round($pid);
	$fL = substr($fL, 0, 3);
	$is=0;
	if ($pid) {
		$dPart=$dbMain->Query("SELECT * FROM $tPart WHERE id=$pid");
		if ($dPart->NumRows()) {
			$dPart->NextResult();


			$xName=$dPart->Get("name");
			$xTree=$dPart->Get("intree");

			Head("Персонажи | $xName","people");
			echo "<table width=100%>";
			$is=1;
			echo "<td width=100% valign=top><h1>$xName<br>&nbsp;&nbsp;&nbsp;<a href=#marquee id=small>слайдшоу</a></h1>";

			$xDescr=$dPart->Get("description");
			if ($xDescr) echo "<div align=justify>".str_replace("\n", "<p>", $xDescr)."</div>";
			else echo "Информация отстутствует";

			if ($xTree) echo "<br><br><a href=/tree/?toID=$xTree>Посмотреть</a> родословную.";

			$dEvents=$dbMain->Query("SELECT * FROM $tMain WHERE participants_ids LIKE '% $pid %' ORDER BY title");
			$xNumRows=$dEvents->NumRows();
			if ($xNumRows) {
				echo "<h1>Произведения:</h1>";
				echo "<ul type=square>";
				$xMarquee="<table border=0 cellpadding=3 cellsapcing=2><tr>";
				for($i=0;$i<$xNumRows;$i++) {
					$dEvents->NextResult();

					$xId=$dEvents->Get("id");
					$xThumb=$dEvents->Get("thumb");
					$xTitle=$dEvents->Get("title");
					$xAuthor=$dEvents->Get("author_id");
					echo "	<li> <a href='/?wid=$xId'><b>".$xTitle."</b></a> <span id=small>/ <a href='/authors/?aid=$xAuthor'>".$xAuthors[$xAuthor]."</a></span>\n";
					$xMarquee.="<td align=center width=100><a href=../?wid=$xId title='$xTitle'><img src=$cThumbImgsPath/$xThumb border=1 id=img></a></td>";
				}
				$xMarquee.="</tr></table>";
				$xMovie=$i;
				echo "</ul>";
			}

			echo "</td>";
		}
	}
	if (!$is) {
	
		Head("Персонажи | $fL","people");
		echo "<table width=100%>";
	
	
	echo "<td valign=top>";
	echo "<h1>Персонажи</h1>";

	if ($fL=="%" || !$fL) {$xCond="ORDER BY name LIMIT 30";}
    else {$xCond="WHERE UPPER(name) LIKE '$fL%' ORDER BY name";}

	$dPeople=$dbMain->Query("SELECT * FROM $tPart $xCond");
	$xRows=$dPeople->NumRows();

	if ($fPage) $dPeople->Seek($fPage);

	echo "<ul type=square>";
	for ($i=0;$i<$cRecsPerPage;$i++) {
		if ($fPage+$i>=$xRows) break;

		$dPeople->NextResult();

		$xId=$dPeople->Get("id");
		$xName=$dPeople->Get("name");
		$xDescr=$dPeople->Get("description");

		echo "	<li> <a href='$ps?pid=$xId&fL=$fL'>".$xName."</a>\n";
	}
	echo "</ul>";

/*	$p=new Pages($xRows,$cRecsPerPage,$fPage);
	$p->SetScript($ps);
	$p->SetParameterName("fPage");
	$p->SetAddParams("code=$code");

	$p->SetDivider(" | ");
	$p->SetText("Персонажи");
	$p->SetPrevNext("&laquo; назад","дальше &raquo;");

	$p->SetStyle("current","color:red; font-weight:bold");
	$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");*/

	echo "<center>Персонажи:<br>";
//	$p->PrintPages();
	LetterPages("fL",$fL,$tPart);
	echo "</center>";
	}//----------------!!!!!!!!!

	echo "<br><img src=/i/spacer.gif width=250 height=1 border=0></td></tr></table>";
	echo "<a name=marquee></a>";
	if ($xMovie) {
		echo "<center><img src=/i/index_08.gif><br><marquee width=600 scrollamount=10 onMouseOver='this.stop()' onMouseOut='this.start()'>".$xMarquee."</marquee></center>";
	}

	Foot();
?>