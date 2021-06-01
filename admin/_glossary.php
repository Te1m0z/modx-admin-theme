<?//Глоссарий

    $macc=$table;
	
	include("admin.inc.php");
	$op_names=array(
	"installform"=>"Создание таблицы|2",
	"install"=>"Создание таблицы|2",
	"uninstall"=>"Уничтожение таблицы|2",
	"del"=>"Удаление записи",
	"edit"=>"Редактирование информации",
	"add"=>"Добавление/редактирование информации"
	);

	$tGloss="_glossary";

	$ss=split("\|",$op_names[$go]);
	$s=$ss[0];
	$p=round($ss[1]);
	if (!$global_access || ($p && $p>$global_access && $global_access>=0)) {$s=$access_denied; $go=""; $cath="";}


	$show_mess=10;
	head("Глоссарий",$s,"$ps?code=$code");

	$pass=table_exists($tGloss,$db_name);

	$navbar=array(
	"2::!\$pass::$ps?go=install&code=$code::Инсталляция",
	"2::\$pass::$ps?go=uninstall&code=$code::Деинсталляция::1",
	"1::\$pass::$ps?go=edit&id=&code=$code&fL=$fL::Новая запись"

	);	

	navigation($navbar);
	middle($s);

// -------- Functionz ----------------------	
	function quotes($x) {
		return str_replace("'","\'",$x);
	}


	switch ($go) {
	    case "install": {
	    	$dCreate=$dbMain->Query("CREATE TABLE $tGloss (
	id int(11) NOT NULL AUTO_INCREMENT,
	name TEXT NOT NULL,
	description TEXT NOT NULL,
	PRIMARY KEY(id))");
			error("Таблица создана!");
			break;
	    }
	    case "uninstall": {
	    	$dDrop=$dbMain->Query("DROP TABLE $tGloss");
	    	error("Таблица уничтожена!");
			break;
	    }
	    case "edit": {
	        $xName="";
	        $xDescription="";
	    	if ($id) {
	    		$dEditChar=$dbMain->Query("SELECT * FROM $tGloss WHERE id='$id'");
	    		if ($dEditChar->NumRows()) {
	    			$dEditChar->NextResult();
	    			$xName=$dEditChar->Get("name");
	    			$xDescr=$dEditChar->Get("description");
	    		} else $id="";
	    	} else $id="";
	    			
			$f=new Form(array("formname"=>"Glossary","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
			$f->SetStyle("elements","id=sform");

			$f->Hidden(array("name"=>"sub","value"=>"store"));
			$f->Hidden(array("name"=>"code","value"=>"$code"));
			$f->Hidden(array("name"=>"id","value"=>"$id"));
			$f->Hidden(array("name"=>"fL","value"=>"$fL"));

			$f->Text(array("name"=>"fName","value"=>$xName,"title"=>"Термин:<br>","after"=>"<br>"));
			$f->TextArea(array("name"=>"fDescr","value"=>$xDescr,"title"=>"Описание:<br>","after"=>"<br>"));

			$f->SubmitReset(array("submittitle"=>" готово ","resettitle"=>" отмена "));
			$f->PrintForm();

	    	break;
	    }
	}

		    
    if (table_exists($tGloss,$db_name)) {
		if ($sub=="del" && $id) {
			$dCharDel=$dbMain->Query("DELETE FROM $tGloss WHERE id='$id'");
			error("Запись уничтожена!");
		}
    	if ($sub=="store") {
    		if ($id) $dStoreChar=$dbMain->Query("UPDATE $tGloss SET name='".quotes($fName)."',description='".quotes($fDescr)."' WHERE id=$id");
    		else $dStoreChar=$dbMain->Query("INSERT INTO $tGloss(name,description) VALUES('".quotes($fName)."', '".quotes($fDescr)."')");
    		error("Информация изменена!");
    	}

		echo "<table width=100% cellpadding=2 cellspacing=1>";
		echo "<colgroup><col valign=top><col id=smallest>";

		echo "<tr><th>%/#</th><th width=100%>Термины</th></tr>";

		$xSearch=quotes($fSearch);
		if ($fSearch) $ad="WHERE name LIKE '%$xSearch%'";
		else {
			if ($fL && $fL!="%") $ad="WHERE UPPER(name) LIKE '$fL%'";
			else $ad="WHERE SUBSTRING(UPPER(name),1,1)<'А' OR SUBSTRING(UPPER(name),1,1)>'Я'";
		}
		
		$dChars=$dbMain->Query("SELECT * FROM $tGloss $ad ORDER BY name");
		$xChars=$dChars->NumRows();

		if ($fPage) $dChars->Seek($fPage);
		for ($i=0;$i<$cRecsPerPage;$i++) {
			if ($fPage+$i>=$xChars) break;
			$dChars->NextResult();

			$xId=$dChars->Get("id");
			echo "<tr id=trr><td><nobr><a href=$ps?go=edit&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=ed>Edit</a> <a href=$ps?sub=del&id=$xId&code=$code&fPage=$fPage&fSearch=$fSearch id=del>Del</a></nobr></td>";
			echo "<td id=smallest><b>".$dChars->Get("name")."</b></td></tr>\n";
		}
		echo "</table>";

		$p=new Pages($xChars,$cRecsPerPage,$fPage);
		$p->SetScript($ps);
		$p->SetParameterName("fPage");
		$p->SetAddParams("code=$code&go=events&fSearch=$fSearch");
	
		$p->SetDivider(" | ");
		$p->SetText("Термины");
		$p->SetPrevNext("&laquo; назад","дальше &raquo;");


		$p->SetStyle("current","color:red; font-weight:bold");
		$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");

		echo "<center>";
		LetterPages("code=$code&fL",$fL);
		echo "</center>";


		$f=new Form(array("formname"=>"CathSearch","action"=>"$ps","method"=>"POST","enctype"=>"","size"=>"50","cols"=>"50","rows"=>"10",""=>"",""=>"",""=>""));
		$f->SetStyle("elements","id=sform");
		$f->Hidden(array("name"=>"go","value"=>"caths"));
		$f->Hidden(array("name"=>"code","value"=>$code));

		$f->Text(array("name"=>"fSearch","value"=>$fSearch,"title"=>"Поиск:<br>","after"=>""));
		$f->SubmitReset(array("submittitle"=>" найти "));
		$f->PrintForm();
	}


	foot();
?>