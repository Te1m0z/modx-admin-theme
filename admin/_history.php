<?//Российская История


	include "admin.inc.php";
	$op_names=array(
	"events"=>"События|2",
	"authors"=>"Авторы|2",
	"chars"=>"Персонажи|2"
	);

	$ss=split("\|",$op_names[$go]);
	$s=$ss[0];
	$p=round($ss[1]);
	if (!$global_access || ($p && $p>$global_access && $global_access>=0)) {$s=$access_denied; $go=""; $id="";}

	if ($id && sub_access($id,"")<2 && $global_access<2) {$s=$access_denied; $go=""; $id="";}
	
	$tMain="main";
	$tAuthors="authors";
	$tPart="participants";
	$tEvents="events";
	$tCath="cathegories";

	$cImgsPath="../img";
	$cThumbImgsPath="../img/thumbs";
	$cRecsPerPage=20;
	$cEventLength=200;

	head("Российская История",$s,"$ps?code=$code");

	$pass=table_exists($tMain,$db_name);
	
	$navbar=array(
	"2::\$pass::$ps?go=event&code=$code::Новая запись",
	"2::\$pass::$ps?go=authors&code=$code::Авторы",
	"2::\$pass::$ps?go=chars&code=$code::Персонажи",
	"2::\$pass::$ps?go=events&code=$code::События",
	"2::\$pass::$ps?go=caths&code=$code::Категории",
	"2::\$pass::$ps?go=links&code=$code::Ссылки"
	);	

	navigation($navbar);
	middle($s);

//	if (!$pass && $go!="uninstall" && $go!="install") {foot(); exit;}


	function quotes($x) {
		return str_replace("'","\'",$x);
	}


	function PartReplace($a) {
	  global $flag, $xxId, $damn, $xId;

	    error("Совпадение: &laquo;".$a."&raquo;");
	    
	    if ($flag) return $a;
	    $flag=1;
		if (!ereg("^<pid=[0-9]+>",$a)) {
		    $damn=1;
			return "<pid=".$xxId.">".$a."</pid>";
		}
		else return $a;
	}
	
	switch ($go) {
	    case "links": {
	    	$dNames=$dbMain->Query("SELECT * FROM $tPart ORDER BY id");
	    	$xPeople=array();
	    	$xxPeople=$dNames->NumRows();
	    	for ($i=0;$i<$xxPeople;$i++) {
	    	    $dNames->NextResult();
	    		$xPeople[$dNames->Get("name")]=$dNames->Get("id");
	    	}

	    	error("Персонажей: ".$xxPeople);
	    	$dEvents=$dbMain->Query("SELECT * FROM $tEvents ORDER BY id");

/*	    	$xEvents=array();
	    	for ($i=0;$i<$dEvents->NumRows();$i++) {
	    		$dEvents->NextResult();
	    		$xEvents[$dEvents->Get("")]
	    	}


	    	$dEvents->Seek(0);*/

	    	for ($i=0;$i<$dEvents->NumRows();$i++) {
	    		$dEvents->NextResult();
	    		$xId=$dEvents->Get("id");
	    		$xEvent=$dEvents->Get("event");

	    		reset($xPeople);
	    		while (list($xName,$xxId)=each($xPeople)) {
	    			$flag=0;
		    		$damn=0;
	    		    $xName=preg_replace("/ *\([^(]*$/","",$xName);
	    			$xEvent=preg_replace("/([\n\r\t., ])((<pid=[0-9]+>)?".$xName."(<\/pid>)?)([\n\r\t., ])/e","\"\\1\".PartReplace(\"\\2\").\"\\5\"",$xEvent);
	    			if ($damn) {

	    				$dStore=$dbMain->Query("UPDATE $tEvents SET event='".str_replace("'","\'",$xEvent)."' WHERE id=$xId");
	    			}
	    		}
	    	}

	    	break;
	    }
	    case "cath_edit": {
	    	if ($id) {
	    		$dEditChar=$dbMain->Query("SELECT * FROM $tCath WHERE id='$id'");
	    		if ($dEditChar->NumRows()) {
	    			$dEditChar->NextResult();
	    			
					$f=new Form(array("formname"=>"History","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
					$f->SetStyle("elements","id=sform");

					$f->Hidden(array("name"=>"go","value"=>"caths"));
					$f->Hidden(array("name"=>"sub","value"=>"store"));
					$f->Hidden(array("name"=>"code","value"=>"$code"));
					$f->Hidden(array("name"=>"id","value"=>"$id"));
					$f->Hidden(array("name"=>"fPage","value"=>"$fPage"));

					$f->Text(array("name"=>"fName","value"=>$dEditChar->Get("name"),"title"=>"Название:<br>","after"=>"<br>"));
					$f->SubmitReset(array("submittitle"=>" готово ","resettitle"=>" отмена "));
					$f->PrintForm();
	    		
	    		} else error("Нет такой категории!");
	    	
	    	}
	    	break;
	    }
		case "caths": {
			echo "<b>Категории:</b><br>";

			if ($sub=="del" && $id) {
				$dCharDel=$dbMain->Query("DELETE FROM $tCath WHERE id='$id'");
				$dCharDel->Query("DELETE FROM $tMain WHERE cath_id='$id'");
				error("Записи в категории уничтожены!");
			}
	    	if ($sub=="store" && $id) {
	    		$dStoreChar=$dbMain->Query("UPDATE $tCath SET name='".quotes($fName)."' WHERE id=$id");
	    		error("Информация о категории изменена!");
	    	}

			echo "<table width=100% cellpadding=2 cellspacing=1>";
			echo "<colgroup><col valign=top><col id=smallest>";

			echo "<tr><th>%/#</th><th width=100%>Категория</th></tr>";

			$xSearch=quotes($fSearch);
			if ($fSearch) $ad="WHERE name LIKE '%$xSearch%'";
			else $ad="";
			
			$dChars=$dbMain->Query("SELECT * FROM $tCath $ad ORDER BY name");
			$xChars=$dChars->NumRows();

			if ($fPage) $dChars->Seek($fPage);
			for ($i=0;$i<$cRecsPerPage;$i++) {
				if ($fPage+$i>=$xChars) break;
				$dChars->NextResult();

				$xId=$dChars->Get("id");
				echo "<tr id=trr><td><nobr><a href=$ps?go=cath_edit&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=ed>Edit</a> <a href=$ps?go=caths&sub=del&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=del>Del</a></nobr></td>";
				echo "<td id=smallest><b>".$dChars->Get("name")."</b></td></tr>\n";
			}
			echo "</table>";

			$p=new Pages($xChars,$cRecsPerPage,$fPage);
			$p->SetScript($ps);
			$p->SetParameterName("fPage");
			$p->SetAddParams("code=$code&go=events&fSearch=$fSearch");
	
			$p->SetDivider(" | ");
			$p->SetText("Категории");
			$p->SetPrevNext("&laquo; назад","дальше &raquo;");


			$p->SetStyle("current","color:red; font-weight:bold");
			$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");

			echo "<center>";
			$p->PrintPages();
			echo "</center>";


			$f=new Form(array("formname"=>"CathSearch","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
			$f->SetStyle("elements","id=sform");
			$f->Hidden(array("name"=>"go","value"=>"caths"));
			$f->Hidden(array("name"=>"code","value"=>$code));

			$f->Text(array("name"=>"fSearch","value"=>$fSearch,"title"=>"Поиск категории:<br>","after"=>""));
			$f->SubmitReset(array("submittitle"=>" найти "));
			$f->PrintForm();

			break;
        }
	    case "event_edit": {
	    	if ($id) {
	    		$dEditChar=$dbMain->Query("SELECT * FROM $tEvents WHERE id='$id'");
	    		if ($dEditChar->NumRows()) {
	    			$dEditChar->NextResult();
	    			
					$f=new Form(array("formname"=>"History","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
					$f->SetStyle("elements","id=sform");

					$f->Hidden(array("name"=>"go","value"=>"events"));
					$f->Hidden(array("name"=>"sub","value"=>"store"));
					$f->Hidden(array("name"=>"code","value"=>"$code"));
					$f->Hidden(array("name"=>"id","value"=>"$id"));
					$f->Hidden(array("name"=>"fPage","value"=>"$fPage"));

					$f->Text(array("name"=>"fDate","value"=>$dEditChar->Get("date"),"title"=>"Дата (старая):<br>","after"=>"<br>","events"=>" readonly"));

					$f->AddHTML("<fieldset><legend>Даты:</legend>");

					list($xFromCentury,$xFromYear,$xFromMonth,$xFromDay)=split("\.",$dEditChar->Get("from_date"));
					$f->Select($cCenturies,array("name"=>"fFromCentury","choosed"=>$xFromCentury,"title"=>"<span id=small>Начало события (век, год, месяц, день):</span><br>","after"=>" "));
					$f->Text(array("name"=>"fFromYear","size"=>"8","value"=>$xFromYear,"title"=>"","after"=>" "));
					$f->Select($cMonths,array("name"=>"fFromMonth","choosed"=>$xFromMonth,"title"=>"","after"=>" "));
					$f->Select($cDays,array("name"=>"fFromDay","choosed"=>$xFromDay,"title"=>"","after"=>"<br>"));

					list($xTillCentury,$xTillYear,$xTillMonth,$xTillDay)=split("\.",$dEditChar->Get("till_date"));
					$f->Select($cCenturies,array("name"=>"fTillCentury","choosed"=>$xTillCentury,"title"=>"<span id=small>Окончание события (если есть) (век, год, месяц, день):</span><br>","after"=>" "));
					$f->Text(array("name"=>"fTillYear","size"=>"8","value"=>$xTillYear,"title"=>"","after"=>" "));
					$f->Select($cMonths,array("name"=>"fTillMonth","choosed"=>$xTillMonth,"title"=>"","after"=>" "));
					$f->Select($cDays,array("name"=>"fTillDay","choosed"=>$xTillDay,"title"=>"","after"=>"<br><br>"));


					$f->AddHTML("</fieldset>");


					$f->TextArea(array("name"=>"fEvent","value"=>$dEditChar->Get("event"),"title"=>"Описание:<br>","after"=>"<br>"));
					$f->SubmitReset(array("submittitle"=>" готово ","resettitle"=>" отмена "));
					$f->PrintForm();
	    		
	    		} else error("Событие не найдено!");
	    	
	    	}
	    	break;
	    }
		case "events": {
			echo "<b>События:</b><br>";

			if ($sub=="del" && $id) {
				$dCharDel=$dbMain->Query("DELETE FROM $tEvents WHERE id='$id'");
				$dCharDel->Query("DELETE FROM $tMain WHERE event_id='$id'");
				error("Записи о событии уничтожены!");
			}
	    	if ($sub=="store" && $id) {
		        $xFromDate=sprintf("%02d",$fFromCentury).".".sprintf("%04d",preg_replace("/[^0-9]/","",$fFromYear)).".".sprintf("%02d",$fFromMonth).".".sprintf("%02d",$fFromDay);
		        $xTillDate=sprintf("%02d",$fTillCentury).".".sprintf("%04d",preg_replace("/[^0-9]/","",$fTillYear)).".".sprintf("%02d",$fTillMonth).".".sprintf("%02d",$fTillDay);

	    		$dStoreChar=$dbMain->Query("UPDATE $tEvents SET event='".quotes($fEvent)."', from_date='".$xFromDate."', till_date='".$xTillDate."' WHERE id=$id");
	    		error("Информация о событии изменена!");
	    	}

			echo "<table width=100% cellpadding=2 cellspacing=1>";
			echo "<colgroup><col valign=top><col id=smallest>";

			echo "<tr><th>%/#</th><th width=100%>Дата/событие</th></tr>";

			$xSearch=quotes($fSearch);
			if ($fSearch) $ad="WHERE date LIKE '%$xSearch%' OR event LIKE '%$xSearch%'";
			else $ad="";
			
			$dChars=$dbMain->Query("SELECT * FROM $tEvents $ad ORDER BY from_date");
			$xChars=$dChars->NumRows();

			if ($fPage) $dChars->Seek($fPage);
			for ($i=0;$i<$cRecsPerPage;$i++) {
				if ($fPage+$i>=$xChars) break;
				$dChars->NextResult();

				$xId=$dChars->Get("id");
				echo "<tr id=trr><td><nobr><a href=$ps?go=event_edit&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=ed>Edit</a> <a href=$ps?go=events&sub=del&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=del>Del</a></nobr></td>";
				echo "<td id=smallest><b>".$dChars->Get("date")."</b> (".MakeDate($dChars->Get("from_date"),$dChars->Get("till_date")).")<br>".nl2br(substr($dChars->Get("event"), 0, $cEventLength))."</td></tr>\n";
			}
			echo "</table>";

			$p=new Pages($xChars,$cRecsPerPage,$fPage);
			$p->SetScript($ps);
			$p->SetParameterName("fPage");
			$p->SetAddParams("code=$code&go=events&fSearch=$fSearch");
	
			$p->SetDivider(" | ");
			$p->SetText("События");
			$p->SetPrevNext("&laquo; назад","дальше &raquo;");


			$p->SetStyle("current","color:red; font-weight:bold");
			$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");

			echo "<center>";
			$p->PrintPages();
			echo "</center>";


			$f=new Form(array("formname"=>"EventSearch","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
			$f->SetStyle("elements","id=sform");
			$f->Hidden(array("name"=>"go","value"=>"events"));
			$f->Hidden(array("name"=>"code","value"=>$code));

			$f->Text(array("name"=>"fSearch","value"=>$fSearch,"title"=>"Поиск события (дата, ключевые слова):<br>","after"=>""));
			$f->SubmitReset(array("submittitle"=>" найти "));
			$f->PrintForm();

			break;
        }
	    case "author_edit": {
	    	if ($id) {
	    		$dEditChar=$dbMain->Query("SELECT * FROM $tAuthors WHERE id='$id'");
	    		if ($dEditChar->NumRows()) {
	    			$dEditChar->NextResult();
	    			
					$f=new Form(array("formname"=>"History","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
					$f->SetStyle("elements","id=sform");

					$f->Hidden(array("name"=>"go","value"=>"authors"));
					$f->Hidden(array("name"=>"sub","value"=>"store"));
					$f->Hidden(array("name"=>"code","value"=>"$code"));
					$f->Hidden(array("name"=>"id","value"=>"$id"));
					$f->Hidden(array("name"=>"fPage","value"=>"$fPage"));

					$f->Text(array("name"=>"fName","value"=>$dEditChar->Get("name"),"title"=>"Автор:<br>","after"=>"<br>"));
					$f->TextArea(array("name"=>"fDescr","value"=>$dEditChar->Get("description"),"title"=>"Описание:<br>","after"=>"<br>"));
					$f->SubmitReset(array("submittitle"=>" готово ","resettitle"=>" отмена "));
					$f->PrintForm();
	    		
	    		} else error("Автор не найден!");
	    	
	    	}
	    	break;
	    }
		case "authors": {
			echo "<b>Авторы:</b><br>";

			if ($sub=="del" && $id) {
				$dCharDel=$dbMain->Query("DELETE FROM $tAuthors WHERE id='$id'");
				$dCharDel->Query("DELETE FROM $tMain WHERE author_id='$id'");
				error("Записи об авторе и его работах уничтожены!");
			}
	    	if ($sub=="store" && $id) {
	    		$dStoreChar=$dbMain->Query("UPDATE $tAuthors SET name='".quotes($fName)."', description='".quotes($fDescr)."' WHERE id=$id");
	    		error("Информация об авторе изменена!");
	    	}

			$f=new Form(array("formname"=>"AuthorSearch","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
			$f->SetStyle("elements","id=sform");
			$f->Hidden(array("name"=>"go","value"=>"authors"));
			$f->Hidden(array("name"=>"code","value"=>$code));

			$f->Text(array("name"=>"fSearch","value"=>$fSearch,"title"=>"Поиск автора:<br>","after"=>""));
			$f->SubmitReset(array("submittitle"=>" найти "));
			$f->PrintForm();

			echo "<table width=100% cellpadding=2 cellspacing=1>";
			echo "<colgroup><col valign=top><col id=smallest>";

			echo "<tr><th>%/#</th><th width=100%>Автор/справка</th></tr>";

			$xSearch=quotes($fSearch);
			if ($fSearch) $ad="WHERE name LIKE '%$xSearch%' OR description LIKE '%$xSearch%'";
			else $ad="";
			
			$dChars=$dbMain->Query("SELECT * FROM $tAuthors $ad ORDER BY name");
			$xChars=$dChars->NumRows();

			if ($fPage) $dChars->Seek($fPage);
			for ($i=0;$i<$cRecsPerPage;$i++) {
				if ($fPage+$i>=$xChars) break;
				$dChars->NextResult();

				$xId=$dChars->Get("id");
				echo "<tr id=trr><td><nobr><a href=$ps?go=author_edit&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=ed>Edit</a> <a href=$ps?go=authors&sub=del&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=del>Del</a></nobr></td>";
				echo "<td id=smallest><b>".$dChars->Get("name")."</b><br>".nl2br($dChars->Get("description"))."</td></tr>\n";
			}
			echo "</table>";

			$p=new Pages($xChars,$cRecsPerPage,$fPage);
			$p->SetScript($ps);
			$p->SetParameterName("fPage");
			$p->SetAddParams("code=$code&go=authors&fSearch=$fSearch");
			$p->SetShownPages(40);
	
			$p->SetDivider(" | ");
			$p->SetText("Авторы");
			$p->SetPrevNext("&laquo; назад","дальше &raquo;");

			$p->SetStyle("current","color:red; font-weight:bold");
			$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");

			echo "<center>";
			$p->PrintPages();
			echo "</center>";



			break;
        }
	    case "char_edit": {
	    	if ($id) {
	    		$dEditChar=$dbMain->Query("SELECT * FROM $tPart WHERE id='$id'");
	    		if ($dEditChar->NumRows()) {
	    			$dEditChar->NextResult();
	    			
					$f=new Form(array("formname"=>"history","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
					$f->SetStyle("elements","id=sform");

					$f->Hidden(array("name"=>"go","value"=>"chars"));
					$f->Hidden(array("name"=>"sub","value"=>"store"));
					$f->Hidden(array("name"=>"code","value"=>"$code"));
					$f->Hidden(array("name"=>"id","value"=>"$id"));
					$f->Hidden(array("name"=>"fPage","value"=>"$fPage"));

					$f->Text(array("name"=>"fName","value"=>$dEditChar->Get("name"),"title"=>"Имя персонажа:<br>","after"=>"<br>"));
					$f->TextArea(array("name"=>"fDescr","value"=>$dEditChar->Get("description"),"title"=>"Описание:<br>","after"=>"<br>"));
					$f->SubmitReset(array("submittitle"=>" готово ","resettitle"=>" отмена "));
					$f->PrintForm();
	    		
	    		} else error("Персонаж не найден!");
	    	
	    	}
	    	break;
	    }
		case "chars": {
			echo "<b>Персонажи:</b><br>";

			if ($sub=="del" && $id) {
				$dCharDel=$dbMain->Query("DELETE FROM $tPart WHERE id='$id'");
				$dCharDel->Query("UPDATE $tMain SET participants_ids=REPLACE(participants_ids, ' $id ', ' ') WHERE participants_ids LIKE '% $id %'");
				error("Персонаж уничтожен!");
			}
	    	if ($sub=="store" && $id) {
	    		$dStoreChar=$dbMain->Query("UPDATE $tPart SET name='".quotes($fName)."', description='".quotes($fDescr)."' WHERE id=$id");
	    		error("Информация о персонаже изменена!");
	    	}


			echo "<br>- <a href=# onClick=window.open('treechars.php?code=$code','chars','width=500,height=700,location=no,menubar=no,resizable=yes,status=yes')>Указать соответствия между персонажами и родословной</a><br><br>";
			
			echo "<table width=100% cellpadding=2 cellspacing=1>";
			echo "<colgroup><col valign=top><col><col align=center>";

			echo "<tr><th>%/#</th><th width=100%>Персонаж/описание</th><th>Родословная</th></tr>";

			$xSearch=quotes($fSearch);
			if ($fSearch) $ad="WHERE name LIKE '%$xSearch%' OR description LIKE '%$xSearch%'";
			else $ad="";
			
			$dChars=$dbMain->Query("SELECT * FROM $tPart $ad ORDER BY name");
			$xChars=$dChars->NumRows();

			if ($fPage) $dChars->Seek($fPage);
			for ($i=0;$i<$cRecsPerPage;$i++) {
				if ($fPage+$i>=$xChars) break;
				$dChars->NextResult();

				$xId=$dChars->Get("id");
				$xTreeId=$dChars->Get("intree");
				echo "<tr id=trr><td><nobr><a href=$ps?go=char_edit&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=ed>Edit</a> <a href=$ps?go=chars&sub=del&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=del>Del</a></nobr></td>";
				echo "<td id=smallest><b>".$dChars->Get("name")."</b><br>".nl2br($dChars->Get("description"))."</td>";
				echo "<td>".($xTreeId?"<a href=../tree/?toID=$xTreeId target=_tree>есть</a>":"&nbsp;")."</td></tr>\n";
			}
			echo "</table>";

			$p=new Pages($xChars,$cRecsPerPage,$fPage);
			$p->SetScript($ps);
			$p->SetParameterName("fPage");
			$p->SetAddParams("code=$code&go=chars&fSearch=$fSearch");
	
			$p->SetShownPages(20);
			$p->SetDivider(" | ");
			$p->SetText("Персонажи");
			$p->SetPrevNext("&laquo; назад","дальше &raquo;");


			$p->SetStyle("current","color:red; font-weight:bold");
			$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");

			echo "<center>";
			$p->PrintPages();
			echo "</center>";


			$f=new Form(array("formname"=>"CharSearch","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
			$f->SetStyle("elements","id=sform");
			$f->Hidden(array("name"=>"go","value"=>"chars"));
			$f->Hidden(array("name"=>"code","value"=>$code));

			$f->Text(array("name"=>"fSearch","value"=>$fSearch,"title"=>"Поиск персонажа:<br>","after"=>""));
			$f->SubmitReset(array("submittitle"=>" найти "));
			$f->PrintForm();

			break;
        }
		case "store": {
			$xE="";
			if (!$fTitle) {
				error("Не задано название!");
				$xE=1;
			} else $fTitle=quotes($fTitle);

			//--------------- Event 
			if ($fEventExists!="yes") {
			    if (!$fFromCentury && !$fFromYear) {
					error("Неверно указана дата!");
					$xE=1;
			    } else {
			        $xFromDate=sprintf("%02d",$fFromCentury).".".sprintf("%04d",preg_replace("/[^0-9]/","",$fFromYear)).".".sprintf("%02d",$fFromMonth).".".sprintf("%02d",$fFromDay);
			        $xTillDate=sprintf("%02d",$fTillCentury).".".sprintf("%04d",preg_replace("/[^0-9]/","",$fTillYear)).".".sprintf("%02d",$fTillMonth).".".sprintf("%02d",$fTillDay);

			        $fDate=quotes($fDate);
			        $fDescr=quotes($fDescr);

			    	$dNewEvent=$dbMain->Query("INSERT INTO $tEvents (event, from_date, till_date) VALUES ('$fDescr','$xFromDate','$xTillDate')");

			    	$dNewEvent->Query("SELECT LAST_INSERT_ID() FROM $tEvents");
			    	$dNewEvent->NextResult();
			    	$fEventId=$dNewEvent->Get("id");
			    }
			} else {
				if (!$fEventId) {
					error("Не указан идентификатор события!");
					$xE=1;
				} else {
					$dCheckEvent=$dbMain->Query("SELECT * FROM $tEvents WHERE id='$fEventId'");
					if (!$dCheckEvent->NumRows()) {
						error("Cобытие не найдено!!");
						$xE=1;
					}
				}
		   	}

			//--------------- Cathegory 
			if ($fCathExists!="yes") {
			    if (!$fNewCath) {
					error("Не указана категория!");
					$xE=1;
			    } else {
			        $fNewCath=quotes($fNewCath);
			    	$dNewCath=$dbMain->Query("SELECT * FROM $tCath WHERE name='$fNewCath'");
			    	if (!$dNewCath->NumRows()) {
				    	$dNewCath->Query("INSERT INTO $tCath (name) VALUES ('$fNewCath')");
				    	$dNewCath->Query("SELECT * FROM $tCath WHERE name='$fNewCath'");
				    }
				    $dNewCath->NextResult();
				    $fCathId=$dNewCath->Get("id");
			    }
			} else {
				if (!$fCath) {
					error("Не указана категория!");
					$xE=1;
				} else $fCathId=$fCath;
			}

			//--------------- Author 
			if ($fAuthorExists!="yes") {
			    if (!$fNewAuthor) {
					error("Не указан автор!");
					$xE=1;
			    } else {
			        $fNewAuthor=quotes($fNewAuthor);
			    	$dNewAuthor=$dbMain->Query("INSERT INTO $tAuthors (name, description) VALUES ('$fNewAuthor', '')");
			    	$dNewAuthor->Query("SELECT * FROM $tAuthors WHERE name='$fNewAuthor'");
			    	$dNewAuthor->NextResult();
			    	$fAuthorId=$dNewAuthor->Get("id");
			    }
			} else $fAuthorId=$fAuthor;

			//--------------- Participants
			if ($fParticipants) {
			    $xPartTemp=$fParticipants;
			    $xAParts=array();
				$dParts=$dbMain->Query("SELECT * FROM $tPart");
				for ($i=0;$i<$dParts->NumRows();$i++) {
					$dParts->NextResult();
					$xAParts[$dParts->Get("name")]=$dParts->Get("id");
				}

				$xFParts=array();
				$index=0;
				$xParts=split("\n", $fParticipants);
				for ($i=0;$i<sizeof($xParts);$i++) {

				    $xCurrentChar=trim($xParts[$i]);
					if ($xCurrentChar) {
						$xPart=$xAParts[$xCurrentChar];
						if (!$xPart) {
							error("Новый персонаж: ",$xCurrentChar);
							$dParts->Query("INSERT INTO $tPart (name, description) VALUES ('".quotes($xCurrentChar)."', '')");
							$dParts->Query("SELECT * FROM $tPart WHERE name='".quotes($xCurrentChar)."'");
							$dParts->NextResult();

							$xFParts[$index]=$dParts->Get("id");
							$index++;
						} else {
							$xFParts[$index]=$xPart;
							$index++;
						}
					}
				}
				$fParticipants=" ".join(" ", $xFParts)." ";
			} else $fParticipants="";

//			error("Персонажи: ".$fParticipants);

			
			//--------------- Image Uploaded
//			error($fImg['tmp_name'].", ".$fImg['name'].", ".$fImg_name);

            $xUploaded=0;
			if (!is_uploaded_file($fImg)) {
				if (!$id) {
					error("Не загружен файл!");
					$xE=1;
				}
			} else {
				$xImageSize=getimagesize($fImg);
				if (!$xImageSize[2]) {
					error("Загруженный файл не является картинкой!");
					$xE=1;
				} else {
					error("Загружен файл: ".$xImageSize[0]."x".$xImageSize[1]);
					$xUploaded=1;
				}
			}

			//--------------- Thumbnail Image Uploaded
            $xThumbUploaded=0;
			if (!is_uploaded_file($fThumbImg)) {
				if (!$id) {
					error("Не загружен файл предпросмотра!");
					$xE=1;
				}
			} else {
				$xImageSize=getimagesize($fThumbImg);
				if (!$xImageSize[2]) {
					error("Загруженный файл не является картинкой!");
					$xE=1;
				} else {
					error("Загружен файл предпросмотра: ".$xImageSize[0]."x".$xImageSize[1]);
					$xThumbUploaded=1;
				}
			}

			if (!$xE) {
			    $fFileName=$fOldFile;
			    if ($xUploaded) {
			    	if ($fFileRename && $fFileRename!=$fImg_name) $fFileName=$fFileRename; else $fFileName=$fImg_name;
					move_uploaded_file($fImg, $cImgsPath."/".$fFileName);
				} else {
					if ($fFileRename!=$fOldFile && $fFileRename) {
						rename($cImgsPath."/".$fOldFile, $cImgsPath."/".$fFileRename);
						$fFileName=$fFileRename;
					}
				}

			    $fThumbFileName=$fOldThumbFile;
			    if ($xThumbUploaded) {
			    	if ($fThumbFileRename && $fThumbFileRename!=$fThumbImg_name) $fThumbFileName=$fThumbFileRename; else $fThumbFileName=$fThumbImg_name;
					move_uploaded_file($fThumbImg, $cThumbImgsPath."/".$fThumbFileName);
				} else {
					if ($fThumbFileRename!=$fOldThumbFile && $fThumbFileRename) {
						rename($cThumbImgsPath."/".$fOldThumbFile, $cThumbImgsPath."/".$fThumbFileRename);
						$fThumbFileName=$fThumbFileRename;
					}
				}

				$fTitle=quotes($fTitle);
				$fComments=quotes($fComments);
				if (!$id) {
					$dStore=$dbMain->Query("INSERT INTO $tMain (title, comments, cath_id, image, thumb, event_id, author_id, participants_ids) VALUES ('$fTitle', '$fComments', '$fCathId', '$fFileName', '$fThumbFileName', '$fEventId', '$fAuthorId', '$fParticipants')");	
					error("Запись добавлена!");
				} else {
					$dStore=$dbMain->Query("UPDATE $tMain SET title='$fTitle', comments='$fComments', cath_id='$fCathId', image='$fFileName', thumb='$fThumbFileName', event_id='$fEventId', author_id='$fAuthorId', participants_ids='$fParticipants' WHERE id='$id'");	
					error("Запись обновлена!");
				}
				break;
			}
		}
		case "event": {
			echo "<script>var Here = new Array();</script>\n";
			if ($id) {
				echo "<b>Редактирование записи:</b><script>\n";

				$dEvent=$dbMain->Query("SELECT * FROM $tMain WHERE id='$id'");
				if ($dEvent->NumRows()) {
					$dEvent->NextResult();

					$xTitle=$dEvent->Get("title");
					$xComments=$dEvent->Get("comments");
					$xEventId=$dEvent->Get("event_id");
					$xAuthor=$dEvent->Get("author_id");
					$xCathId=$dEvent->Get("cath_id");
					$xImage=$dEvent->Get("image");
					$xThumbImage=$dEvent->Get("thumb");

					$xAParts=split(" ", trim($dEvent->Get("participants_ids")));
					$dParts=$dbMain->Query("SELECT * FROM $tPart ORDER BY name");
					$xParts=array();
					for ($i=0;$i<$dParts->NumRows();$i++) {
						$dParts->NextResult();
						$xParts[$dParts->Get("id")]=$dParts->Get("name");
					}
					$xFParts="";
					for ($i=0;$i<sizeof($xAParts);$i++) if ($xParts[$xAParts[$i]]) {
						$xFParts.=$xParts[$xAParts[$i]]."\n";
						echo "Here[".$xAParts[$i]."]=1;\n";
					}
				} else {
					$id="";
					error("Запись не найдена!");
				}
				echo "</script>";
			} else {
				echo "<b>Новая запись:</b>";

				$xTitle=$fTitle;
				$xComments=$fComments;

				$xEventId=$fEventId;
				$xAuthor=$fAuthorId;
				$xFParts=$xPartTemp;
/*				$xCathId=$dEvent->Get("cath_id");
				$xImage=$dEvent->Get("image");
				$xThumbImage=$dEvent->Get("thumb");*/
			}

			
			$f=new Form(array("formname"=>"History","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
			$f->SetStyle("elements","id=sform");

			$f->Hidden(array("name"=>"go","value"=>"store"));
			$f->Hidden(array("name"=>"code","value"=>"$code"));
			$f->Hidden(array("name"=>"id","value"=>"$id"));


//-------- Picture
			$f->Text(array("name"=>"fTitle","value"=>$xTitle,"title"=>"Название:<br>","after"=>"<br>"));
			$f->TextArea(array("name"=>"fComments","value"=>$xComments,"title"=>"Комментарий:<br>","after"=>"<br>"));

//-------- Cathegory
			$xCaths=array();
			$dCaths=$dbMain->Query("SELECT * FROM $tCath ORDER BY name");
			for ($i=0;$i<$dCaths->NumRows();$i++) {
				$dCaths->NextResult();

				$xCN=$dCaths->Get("name");
				$xCaths[$xCN]=$dCaths->Get("id");

				if (!$xCathId && $fNewCath && $fNewCath==$xCN) $xCathId=$xCaths[$xCN];
			}
			$f->AddHTML("<fieldset><legend>Категория:</legend>");
			$xA="checked";
			$xB="";
			$f->Radio(array("state"=>"checked","title"=>"","name"=>"fCathExists","value"=>"yes","after"=>""));
			$f->Select($xCaths, array("title"=>"","name"=>"fCath","choosed"=>"$xCathId","after"=>" (существующая)<br>"));
			$f->Radio(array("state"=>"","title"=>"","name"=>"fCathExists","value"=>"no","after"=>""));
			$f->Text(array("name"=>"fNewCath","value"=>"","title"=>"","after"=>" (новая)<br><br>","size"=>40));
			$f->AddHTML("</fieldset>");
			
//-------- Event
			$f->AddHTML("<fieldset><legend>Событие:</legend>");
			$f->Radio(array("state"=>"checked","title"=>" <a name=event href=#event onClick=window.open('events.php?code=$code','events','width=500,height=400,location=no,resizable=yes,status=yes,scrollbars=yes')>Выбрать</a> существующее событие","name"=>"fEventExists","value"=>"yes","after"=>""));
			$f->Text(array("name"=>"fEventId","value"=>$xEventId,"title"=>"(идентификатор ","after"=>")<br>", size=>"4", "events"=>" style='text-align:center;' READONLY"));

			$f->Radio(array("state"=>"","title"=>" Новое событие","name"=>"fEventExists","value"=>"no","after"=>"<div style='padding: 0 0 10 25'>"));

//			$f->Text(array("name"=>"fDate","value"=>$xDate,"title"=>"Дата:<br>","after"=>"<br>","events"=>" readonly"));

					$f->AddHTML("<fieldset><legend>Даты:</legend>");

//					list($xFromCentury,$xFromYear,$xFromMonth,$xFromDay)=split(".",$dEditChar->Get("from_date"));
					$f->Select($cCenturies,array("name"=>"fFromCentury","value"=>$xFromCentury,"title"=>"<span id=small>Начало события (век, год, месяц, день):</span><br>","after"=>" "));
					$f->Text(array("name"=>"fFromYear","size"=>"8","value"=>$xFromYear,"title"=>"","after"=>" "));
					$f->Select($cMonths,array("name"=>"fFromMonth","value"=>$xFromMonths,"title"=>"","after"=>" "));
					$f->Select($cDays,array("name"=>"fFromDay","value"=>$xFromDay,"title"=>"","after"=>"<br>"));

//					list($xTillCentury,$xTillYear,$xTillMonth,$xTillDay)=split(".",$dEditChar->Get("till_date"));
					$f->Select($cCenturies,array("name"=>"fTillCentury","value"=>$xTillCentury,"title"=>"<span id=small>Окончание события (если есть) (век, год, месяц, день):</span><br>","after"=>" "));
					$f->Text(array("name"=>"fTillYear","size"=>"8","value"=>$xTillYear,"title"=>"","after"=>" "));
					$f->Select($cMonths,array("name"=>"fTillMonth","value"=>$xTillMonths,"title"=>"","after"=>" "));
					$f->Select($cDays,array("name"=>"fTillDay","value"=>$xTillDay,"title"=>"","after"=>"<br><br>"));

					$f->AddHTML("</fieldset>");

			$f->TextArea(array("name"=>"fDescr","value"=>$xDescr,"title"=>"Описание:<br>","after"=>"<br>"));
			$f->AddHTML("</div></fieldset>");

//-------- Author
			$xAuthors=array();
			$dAuthors=$dbMain->Query("SELECT * FROM $tAuthors ORDER BY name");
			for ($i=0;$i<$dAuthors->NumRows();$i++) {
				$dAuthors->NextResult();
				$xAuthors[$dAuthors->Get("name")]=$dAuthors->Get("id");
			}
			
			$f->AddHTML("<fieldset><legend>Автор:</legend>");
			$f->Radio(array("state"=>"checked","title"=>"","name"=>"fAuthorExists","value"=>"yes","after"=>""));
			$f->Select($xAuthors, array("title"=>"","name"=>"fAuthor","choosed"=>$xAuthor,"after"=>" (существующий)<br>"));
			$f->Radio(array("state"=>"","title"=>"","name"=>"fAuthorExists","value"=>"no","after"=>""));
			$f->Text(array("name"=>"fNewAuthor","value"=>"","title"=>"","after"=>" (новый)<br><br>","size"=>40));
			$f->AddHTML("</fieldset>");

//-------- Characters
			$f->AddHTML("<fieldset><legend>Персонажи: <span id=small>(<a name=chars href=#chars onClick=window.open('participants.php?code=$code','participants','width=500,height=400,location=no,resizable=yes,status=yes,scrollbars=yes')>выбрать</a>)</span></legend>");
			$f->TextArea(array("name"=>"fParticipants","value"=>$xFParts,"title"=>"","after"=>"<br><br>"));
			$f->AddHTML("</fieldset>");

//-------- Images
			$f->AddHTML("<fieldset><legend>Картинки:");

			if ($id) {
				$f->AddHTML(" <span id=small>(<a href='$cImgsPath/$xImage' target=_blank>просмотр</a>, <a href='$cThumbImgsPath/$xThumbImage' target=_blank>превью</a>)</span>");
			}

			$f->AddHTML("</legend>");

			if ($id) $xImgRen=""; else $xImgRen="onChange=\"n=''; a=document.History.fImg.value; for (i=a.length;i>0;i--) {if (a.charAt(i)!='\\\\') {n=a.charAt(i)+n;} else {i=0;}}; document.History.fFileRename.value=n; document.History.fThumbFileRename.value='t_'+n;\"";
			$f->File(array("name"=>"fImg","value"=>"","title"=>"","after"=>"<br>", "events"=>$xImgRen));

			$f->Text(array("name"=>"fFileRename","value"=>$xImage,"title"=>"Переименовать:<br>","after"=>" <input type=button value=' Проверить... ' id=sform onClick='window.open(\"photos.php?code=$code&field=fFileRename&filename=\"+document.History.fFileRename.value,\"ch\",\"width=200,height=150\")'><br><br>", "events"=>"onChange=\"if (document.History.fFileRename.value) document.History.fThumbFileRename.value='t_'+document.History.fFileRename.value;\""));
			
			$f->File(array("name"=>"fThumbImg","value"=>"","title"=>"Файл превью (предпросмотр)<br>","after"=>"<br>"));

			$f->Text(array("name"=>"fThumbFileRename","value"=>$xThumbImage,"title"=>"Переименовать превью:<br>","after"=>" <input type=button value=' Проверить... ' id=sform onClick='window.open(\"photos.php?code=$code&field=fThumbFileRename&filename=thumbs/\"+document.History.fThumbFileRename.value,\"ch\",\"width=200,height=150\")'><br><br>"));
			
			$f->Hidden(array("name"=>"fOldFile","value"=>$xImage));
			$f->Hidden(array("name"=>"fOldThumbFile","value"=>$xThumbImage));
			$f->AddHTML("</fieldset>");

			$f->SubmitReset(array("submittitle"=>" готово ","resettitle"=>" отмена "));
			$f->PrintForm();
			break;
		}
		default: {
			if ($sub=="del" && $id) {
				$dDelEvent=$dbMain->Query("SELECT * FROM $tMain WHERE id='$id'");
				if ($dDelEvent->NumRows()) {
					$dDelEvent->NextResult();

					$xFileName=$dDelEvent->Get("image");
					$xThumbFileName=$dDelEvent->Get("thumb");
					@unlink($cImgsPath."/".$xFileName);
					@unlink($cThumbImgsPath."/".$xThumbFileName);
					error("Файл удалён!");

					$dDelEvent->Query("DELETE FROM $tMain WHERE id='$id'");
					error("Запись уничтожена!");
				} else error("Запись не найдена!");
			}
		
		    $xAuthors=array();
		    $dAuthors=$dbMain->Query("SELECT * FROM $tAuthors");
		    for ($i=0;$i<$dAuthors->NumRows();$i++) {
		    	$dAuthors->NextResult();
		    	$xId=$dAuthors->Get("id");
		    	$xName=$dAuthors->Get("name");
		    	$xAuthors[$xId]=$xName;
		    }
			
//			$dEvents=$dbMain->Query("SELECT * FROM $tMain ORDER BY id");

            if ($fL=="%" || !$fL) {$xCond="ORDER BY title ASC LIMIT 30";}
            else {$xCond="WHERE UPPER(title) LIKE '$fL%' ORDER BY title ASC";}
			$dEvents=$dbMain->Query("SELECT * FROM $tMain $xCond");

			$xRows=$dEvents->NumRows();
			echo "<table width=100% cellpadding=2 cellspacing=1>";
			echo "<tr><th>%/#</th><!--<th>дата</th>--><!--<th>автор</th>--><th width=100%>название/автор</th><!--<th>персонажи</th>--><th>изображение</th></tr>";
			echo "<colgroup><col valign=top><!--<col id=small valign=top>--><!--<col valign=top id=small>--><col valign=top id=smallest><!--<col valign=top id=smallest>--><col valign=top align=center id=small>";

			if ($fPage) $dEvents->Seek($fPage);

			for ($i=0;$i<$xRows;$i++) {
//				if ($fPage+$i>=$xRows) break;

				$dEvents->NextResult();

				$xId=$dEvents->Get("id");
				$xTitle=$dEvents->Get("title");
				$xAuthor=$dEvents->Get("author_id");
				$xImage=$dEvents->Get("image");
				$xThumbImage=$dEvents->Get("thumb");
				$xParts=$dEvents->Get("participants_ids");

				echo "<tr id=trr>";
				echo "<td><nobr><a href=$ps?go=event&id=$xId&code=$code id=ed>Edit</a> <a href=$ps?sub=del&id=$xId&code=$code&fPage=$fPage id=del>Del</a></nobr></td>";
//				echo "<td>".$xDate."</td>";
//				echo "<td>".$xAuthor."</td>";
				echo "<td id=smallest><div id=small><b>".$xTitle."</b> / ".$xAuthors[$xAuthor]."</div>".nl2br($xDescr)."</td>";
//				echo "<td>".$xParts."</td>";
				echo "<td><nobr><a href=".$cThumbImgsPath."/".$xThumbImage." target=_blank id=small>$xThumbImage</a><br><a href=".$cImgsPath."/".$xImage." target=_blank id=small>$xImage</a></nobr></td>";
				echo "</tr>";
			}
			echo "</table>";

/*			$p=new Pages($xRows,$cRecsPerPage,$fPage);
			$p->SetScript($ps);
			$p->SetParameterName("fPage");
			$p->SetAddParams("code=$code");
			$p->SetShownPages(20);
	
			$p->SetDivider(" | ");
			$p->SetText("События");
			$p->SetPrevNext("&laquo; назад","дальше &raquo;");


			$p->SetStyle("current","color:red; font-weight:bold");
			$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");*/

			echo "<center id=smallest>";
			LetterPages("code=$code&fL",$fL);
			echo "</center>";

		}
	}

	
	foot();
?>