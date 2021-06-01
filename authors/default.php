<?
	require "../inc/_config.inc.php";

	
	
	$tMain="main";
	$tAuthors="authors";
	$tPart="participants";
	$tEvents="events";
	$tCath="cathegories";

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
			
	$aid=round($aid);
	$is=0;
	if ($aid) {
		$dAuthor=$dbMain->Query("SELECT * FROM $tAuthors WHERE id=$aid");
		if ($dAuthor->NumRows()) {
			$dAuthor->NextResult();


			$xName=$dAuthor->Get("name");

			Head("Авторы | $xName","authors");
			echo "<table width=100%>";
			$is=1;

			echo "<td width=100% valign=top><h1>$xName</h1>";

			$xDescr=$dAuthor->Get("description");
			if ($xDescr) echo "<div align=justify>".str_replace("\n", "<p>", $xDescr)."</div>";
			else echo "Информация отстутствует";

			$dEvents=$dbMain->Query("SELECT * FROM $tMain WHERE author_id=$aid");
			$xNumRows=$dEvents->NumRows();
			if ($xNumRows) {
				echo "<h1>Произведения автора:</h1>";
				echo "<ul type=square>";
				for($i=0;$i<$xNumRows;$i++) {
					$dEvents->NextResult();

					$xId=$dEvents->Get("id");
					$xTitle=$dEvents->Get("title");
					echo "	<li> <a href='/?wid=$xId'>&laquo;<b>".$xTitle."</b>&raquo;</a>\n";
				}
				echo "</ul>";
			}

			echo "</td>";
		}
	}
	if (!$is) {
		$fL = substr($fL, 0, 3);
		Head("Авторы | $fL","authors");
		echo "<table width=100%>";
	
	
	echo "<td valign=top>";
	echo "<h1>Авторы</h1>";

	
	if ($fL=="%" || !$fL) {$xCond="ORDER BY name LIMIT 30";}
    else {$xCond="WHERE UPPER(name) LIKE '$fL%' ORDER BY name";}

	$dAuthors=$dbMain->Query("SELECT * FROM $tAuthors $xCond");
	$xRows=$dAuthors->NumRows();

	if ($fPage) $dAuthors->Seek($fPage);

	echo "<ul type=square>";
	for ($i=0;$i<$xRows;$i++) {
		$dAuthors->NextResult();

		$xId=$dAuthors->Get("id");
		$xName=$dAuthors->Get("name");

		echo "	<li> <a href='$ps?aid=$xId&fL=$fL'>".$xName."</a>\n";
	}
	echo "</ul>";

/*	$p=new Pages($xRows,$cRecsPerPage,$fPage);
	$p->SetScript($ps);
	$p->SetParameterName("fPage");
	$p->SetAddParams("code=$code");
	$p->SetShownPages(10);

	$p->SetDivider(" | ");
	$p->SetText("Авторы");
	$p->SetPrevNext("&laquo; назад","дальше &raquo;");

	$p->SetStyle("current","color:red; font-weight:bold");
	$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");*/

	echo "<center>Авторы:<br>";
//	$p->PrintPages();
	LetterPages("fL",$fL,$tAuthors);
	echo "</center>";
	}//-------------------------------!!!!!!!!!!!!

	echo "<br><img src=/i/spacer.gif width=250 height=1 border=0></td></tr></table>";


	Foot();
?>