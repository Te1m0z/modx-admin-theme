<?
	require "../inc/_config.inc.php";

	
	
	$tMain="main";
	$tAuthors="authors";
	$tPart="participants";
	$tEvents="events";
	$tCath="cathegories";

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
			
	$is=0;
	$cid = round($cid);
	$fPage = round(fPage);
	if ($cid) {
		$dClass=$dbMain->Query("SELECT * FROM $tCath WHERE id=$cid");
		if ($dClass->NumRows()) {
			$dClass->NextResult();


			$xName=$dClass->Get("name");
			
			Head("Жанр | $xName","class");
			echo "<table width=100%>";
			$is=1;
			
			echo "<td width=100% valign=top><h1>$xName</h1>";

			$dCaths=$dbMain->Query("SELECT * FROM $tMain WHERE cath_id=$cid ORDER BY title");
			$xNumRows=$dCaths->NumRows();
			if ($xNumRows) {
				echo "<ul type=square>";
				for($i=0;$i<$xNumRows;$i++) {
					$dCaths->NextResult();

					$xId=$dCaths->Get("id");
					$xTitle=$dCaths->Get("title");
					echo "	<li> <a href='/?wid=$xId'>&laquo;<b>".$xTitle."</b>&raquo;</a>\n";
				}
				echo "</ul>";
			}

			echo "</td>";
		}
	}
	if (!$is) {
		Head("Жанр","class");
		echo "<table width=100%>";
	}
	
	
	echo "<td valign=top>";
	echo "<h1>Жанры</h1>";

	$dCaths=$dbMain->Query("SELECT * FROM $tCath ORDER BY name");
	$xRows=$dCaths->NumRows();

	if ($fPage) $dCaths->Seek($fPage);

	echo "<ul type=square>";
	for ($i=0;$i<$cRecsPerPage;$i++) {
		if ($fPage+$i>=$xRows) break;

		$dCaths->NextResult();

		$xId=$dCaths->Get("id");
		$xName=$dCaths->Get("name");

		if (trim($xName) || $xDate) echo "	<li> <a href='$ps?cid=$xId&fPage=$fPage'>".$xName."</a>\n";
		else $cRecsPerPage++;
	}
	echo "</ul>";

	$p=new Pages($xRows,$cRecsPerPage,$fPage);
	$p->SetScript($ps);
	$p->SetParameterName("fPage");
	$p->SetAddParams("code=$code");

	$p->SetDivider(" | ");
	$p->SetText("Жанры");
	$p->SetPrevNext("&laquo; назад","дальше &raquo;");

	$p->SetStyle("current","color:red; font-weight:bold");
	$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");

	echo "<center>";
	$p->PrintPages();
	echo "</center>";

	echo "<br><img src=/i/spacer.gif width=250 height=1 border=0></td></tr></table>";


	Foot();
?>