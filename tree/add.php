<?
error_reporting(0);

$female_tree="<img src=i/female_tree.gif border=0>";
$male_tree="<img src=i/male_tree.gif border=0>";

function col($val)
{
 if($val==1) $out="f"; else $out="n";
 return $out;
}

include_once("inc/config.inc.php");
include_once("inc/tmpl.inc.php");

function get_tree($toID)
{
	GLOBAL $female_tree,$male_tree;
	$ins = new temp(array("m"=>"templates/main.html","p"=>"templates/person.html","marr"=>"templates/marriage.html","c"=>"templates/child.html"));
	$ins->delimit("<!--","-->"); 
	
	$result=query("SELECT * FROM tree WHERE ID=$toID");
	//--defining parents
	$mother_id=mysql_result($result,0,"Mother");
	$father_id=mysql_result($result,0,"Father");
	$HasChildrenWith=mysql_result($result,0,"HasChildrenWith");
	$person_name=mysql_result($result,0,"Name");
	$person_gender=mysql_result($result,0,"Gender");
	$person_id=mysql_result($result,0,"ID");
	$ins->tie("Name",$person_name);
	//---- table of parents
		if($father_id && $mother_id)
		{
			$result=query("SELECT Name,flag FROM tree WHERE ID=$mother_id");
			$mother_name=@mysql_result($result,0,"Name");
			$ins->tie(array("Marr_Name"=>$mother_name, "Marr_Col"=>col(mysql_result($result,0,"flag")), "Marr_Pict"=>$female_tree, "Marr_ID"=>$mother_id, "Marr_Stat"=>"Мать"));
			$result=query("SELECT Name,flag FROM tree WHERE ID=$father_id");
			$father_name=@mysql_result($result,0,"Name");
			$ins->tie(array("Person_Name"=>$father_name,"Person_Col"=>col(mysql_result($result,0,"flag")), "Person_Pict"=>$male_tree, "Person_ID"=>$father_id, "Person_Stat"=>"Отец"));
		
		$bsr=query("SELECT ID,Name,Gender,flag FROM tree WHERE Father=$father_id AND Mother=$mother_id AND (Father NOT LIKE '' OR Mother NOT LIKE '') AND ID not like $toID");
		for($i=0;$i<@mysql_numrows($bsr);$i++)
		{
			$bs_id=mysql_result($bsr,$i,"ID");
			$bs_name=mysql_result($bsr,$i,"Name");
			$bs_gender=mysql_result($bsr,$i,"Gender");
			if($bs_gender=="male") 	{$a = "брат";$b = $male_tree;} else{	$a = "сестра";$b = $female_tree;}
    	     $ins->tie(array("Child_Name"=>$bs_name, "Child_Col"=>col(mysql_result($bsr,$i,"flag")), "Child_Pict"=>$b, "Child_ID"=>$bs_id, "Child_Stat"=>$a));
					 $ins->parse("c","Child");
					 
		}
		$ins->parse("p","F_M");
		$ins->parse("marr","F_M");
	
		$ins->tie("Child", " ");
		}
		else {$father_id=0; $mother_id=0;				
	$ins->tie("F_M"," ");	
	$ins->parse("main","F_M");
		}
		
	// -- table of brothers and sisters

	//---- table of mariages
		$mariage_list=explode(",",$HasChildrenWith);
		if($person_gender=="male") $p_out=$male; else $p_out=$female;
		$ins->tie(array("Person_Name"=>$person_name, "Person_Col"=>"a", "Person_Pict"=>$p_out, "Person_ID"=>$person_id, "Person_Stat"=>" "));
					 $ins->parse("p","H_W");
		for($i=0;$i<count($mariage_list);$i++)
		{
				//--- building table of icons of mariage
				if($mariage_list[$i]!="")
				{
					$result=query("SELECT Name,Gender,flag FROM tree WHERE ID=".$mariage_list[$i]);
					$m_name=@mysql_result($result,0,"Name");
					$m_gender=@mysql_result($result,0,"Gender");
					$m_id=$mariage_list[$i];
					if($m_gender=="female"){$m_out=$female_tree;$m_str="жена";}else{$m_out=$male_tree;$m_str="муж";	}
					$ins->tie(array("Marr_Name"=>$m_name, "Marr_Col"=>col(mysql_result($result,$i,"flag")), "Marr_Pict"=>$m_out, "Marr_ID"=>$m_id, "Marr_Stat"=>$m_str));
				//--- getting the children of mariage or relations
					if($person_gender=="male") $p_out="Father"; else $p_out="Mother";
					if($m_gender=="male") $m_out="Father"; else $m_out="Mother";
					$result=query("SELECT ID,Name,Gender,flag FROM tree WHERE $p_out=$person_id AND $m_out=".$mariage_list[$i]);
				
					for($i1=0;$i1<@mysql_numrows($result);$i1++)
					{
						if(mysql_result($result,$i1,"Gender")=="male"){$c_out=$male_tree; $c_str="сын";}else{ $c_out=$female_tree; $c_str="дочь"; }
						$ins->tie(array("Child_Name"=>mysql_result($result,$i1,"Name"), "Child_Col"=>col(mysql_result($result,$i1,"flag")), "Child_Pict"=>$c_out, "Child_ID"=>mysql_result($result,$i1,"ID"), "Child_Stat"=>$c_str));
						$ins->parse("c","Child");	
					}
					$ins->parse("marr","H_W");			
					$ins->tie("Child", " ");	
				}
				//-- end of getting childrens
			}
$ins->parse("m");
$ins->out();
}
if($_GET["toID"]){
	$toID=$_GET["toID"];
	if($_GET["action"]=="view"){
		if($_GET["AddNewPerson"]!=""){
			show_log("Добавляем персонажа с именем: ".$_GET["AddNewPerson"]);
			QUERY("INSERT INTO tree(Name,Gender) VALUES('".$_GET["AddNewPerson"]."','".$_GET["PersonGender"]."')");
			$toID=mysql_insert_id();
		}
		get_tree($toID);
	}
}else{
	$result=query("SELECT ID,Name FROM tree ORDER BY Name");
	show_log("Всего найдено персонажей:".mysql_numrows($result));
	if(@mysql_numrows($result)>0){
		$option="";
		for($i=0;$i<mysql_numrows($result);$i++){
			$option.="<option value=".mysql_result($result,$i,"ID").">".mysql_result($result,$i,"Name")."\n";
		}
	}


}
if(!$_GET["norefresh"]){
	echo "<script>document.frames.parent.DownEdit.location='./relations.php?toID=$toID';</script>";
}
?>

