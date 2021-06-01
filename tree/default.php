<?
	require "../inc/_config.inc.php";

error_reporting(0);


$female_tree="<img src=i/female_tree.gif border=0>";
$male_tree="<img src=i/male_tree.gif border=0>";

include_once("inc/config.inc.php");
include_once("inc/tmpl.inc.php");
function col($val)
{
 if($val==1) $out="f"; else $out="n";
 return $out;
}

function get_tree($toID) {
  global $person_name;
  GLOBAL $female_tree,$male_tree;

	$ins = new temp(array("m"=>"templates/main.html","p"=>"templates/person.html","marr"=>"templates/marriage.html","c"=>"templates/child.html"));
	$ins->delimit("<!--","-->"); 
	$result=qu("SELECT * FROM tree WHERE ID=$toID");
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
			$result=qu("SELECT Name,flag FROM tree WHERE ID=$mother_id");
			$mother_name=@mysql_result($result,0,"Name");
			$ins->tie(array("Marr_Hr"=>"<a href=./?toID=$mother_id id=".col(mysql_result($result,0,"flag")).">".$mother_name."</a>","Marr_Pict"=>$female_tree,"Marr_Stat"=>"Мать"));
			$result=qu("SELECT Name,flag FROM tree WHERE ID=$father_id");
			$father_name=@mysql_result($result,0,"Name");
			$ins->tie(array("Person_Hr"=>"<a href=./?toID=$father_id id=".col(mysql_result($result,0,"flag")).">".$father_name."</a>", "Person_Pict"=>$male_tree, "Person_Stat"=>"Отец"));
			$bsr=qu("SELECT ID,Name,Gender,flag FROM tree WHERE Father=$father_id AND Mother=$mother_id AND (Father NOT LIKE '' OR Mother NOT LIKE '') AND ID not like $toID");
			for($i=0;$i<@mysql_numrows($bsr);$i++)
			{
				$bs_id=mysql_result($bsr,$i,"ID");
				$bs_name=mysql_result($bsr,$i,"Name");
				$bs_gender=mysql_result($bsr,$i,"Gender");
				if($bs_gender=="male") 	{$a = "брат";$b = $male_tree;} else{	$a = "сестра";$b = $female_tree;}
    	     	$ins->tie(array("Child_Hr"=>"<a href=./?toID=$bs_id id=".col(mysql_result($bsr,$i,"flag")).">".$bs_name."</a>", "Child_Pict"=>$b, "Child_Stat"=>$a));
			 	$ins->parse("c","Child");
			}
			$ins->parse("p","F_M");
			$ins->parse("marr","F_M");
			$ins->tie("Child", " ");
		}
		else 
		{
			$father_id=0; $mother_id=0;				
			$ins->tie("F_M"," ");	
			$ins->parse("main","F_M");
		}
		$mariage_list=explode(",",$HasChildrenWith);
		if($person_gender=="male") $p_out=$male_tree; else $p_out=$female_tree;
		$ins->tie(array("Person_Hr"=>"<span id=a>".$person_name."</span>", "Person_Pict"=>$p_out, "Person_Stat"=>" "));
					 $ins->parse("p","H_W");
		for($i=0;$i<count($mariage_list);$i++)
		{
				//--- building table of icons of mariage
				if($mariage_list[$i]!="")
				{
					$result=qu("SELECT Name,Gender,flag FROM tree WHERE ID=".$mariage_list[$i]);
					$m_name=@mysql_result($result,0,"Name");
					$m_gender=@mysql_result($result,0,"Gender");
					$m_id=$mariage_list[$i];
					if($m_gender=="female"){$m_out=$female_tree;$m_str="жена";}else{$m_out=$male_tree;$m_str="муж";	}
					$ins->tie(array("Marr_Hr"=>"<a href=./?toID=$m_id id=".col(mysql_result($result,$i,"flag")).">".$m_name."</a>","Marr_Pict"=>$m_out, "Marr_Stat"=>$m_str));
				//--- getting the children of mariage or relations
					if($person_gender=="male") $p_out="Father"; else $p_out="Mother";
					if($m_gender=="male") $m_out="Father"; else $m_out="Mother";
					$result=qu("SELECT ID,Name,Gender,flag FROM tree WHERE $p_out=$person_id AND $m_out=".$mariage_list[$i]);
					for($i1=0;$i1<@mysql_numrows($result);$i1++)
					{
						if(mysql_result($result,$i1,"Gender")=="male"){$c_out=$male_tree; $c_str="сын";}else{ $c_out=$female_tree; $c_str="дочь"; }
						$ins->tie(array("Child_Hr"=>"<a href=./?toID=".mysql_result($result,$i1,"ID")." id=".col(mysql_result($result,$i1,"flag")).">".mysql_result($result,$i1,"Name")."</a>","Child_Pict"=>$c_out, "Child_Stat"=>$c_str));
						$ins->parse("c","Child");	
					}
					$ins->parse("marr","H_W");			
					$ins->tie("Child", " ");	
				}
			}
	$ins->parse("m");
	$ins->out();
}


if ($_GET["toID"]) {
	$r=query("SELECT * FROM tree WHERE id=".$_GET["toID"], $db);
	if (mysql_numrows($r)) {
		$person_name=mysql_result($r,0,"Name");
	}
}
$p=$_GET["p"];

Head("Родословная".($person_name?" | $person_name":" | стр.".($p+1)),"tree");
echo "<style>#f {font-size:8pt;</style>";
echo "<table width=100%>";
echo "<td width=100% valign=top>";



if($_GET["toID"]){
	$toID=$_GET["toID"];
		get_tree($toID);
}else{
	$result=qu("SELECT ID,Name FROM tree ORDER BY Name");
	if(@mysql_numrows($result)>0)
	{
		$option="";
		for($i=0;$i<mysql_numrows($result);$i++)
		{
			$option.="<option value=".mysql_result($result,$i,"ID").">".mysql_result($result,$i,"Name")."\n";
		}
	}
}



if($_GET["FindPerson"]!=""){
	$FindPerson=$_GET["FindPerson"];
	$search="WHERE name LIKE '$FindPerson%' AND intree NOT LIKE '0'";
} else $search="WHERE intree NOT LIKE '0'";




if (!$toID) { //---------------------------!!!!!!!!!!!!!!!!!!!!!!!!!!
echo "</td><td valign=top>";
echo "<h1>Персонажи</h1><ul type=square>";

$max = 25;
$result=qu("SELECT name,intree FROM participants $search ORDER BY Name");
if($p=="") $p=0;
$pages = ceil(mysql_num_rows($result)/$max);
$min=$max*$p;
$max=$min+$max-1;
if ($max>(mysql_numrows($result))-1) $max=(mysql_numrows($result)-1);
for($i=$min;$i<=$max;$i++)
{
	$id=mysql_result($result,$i,"intree");
	$name=mysql_result($result,$i,"name");
	echo "<li> <a href=./?p=$p&toID=".$id.">".$name."</a>\n";
}

    if($p<3) $i = 0;
	else $i = $p-2;

	echo "</ul>";

	echo "<center><b>Страницы</b>:<br>";

	if($p==0) echo "<span id=e>Назад</span>&nbsp;|&nbsp;";
	else echo "<a href=./?p=".($p-1)."&toID=$toID>Назад</a>&nbsp;|&nbsp;";
	
	for($j=$i;$j<$i+5 && $j<$pages;$j++)
		{
		if($p==$j)	echo "<span id=a><b>".($j+1)."</b></span>&nbsp;|&nbsp;";
	    else echo "<a href=./?p=$j&toID=$toID>".($j+1)."</a>&nbsp;|&nbsp;";
		}
	if($p==$pages-1) echo "<span id=e>Дальше</span>";
	else echo "<a href=./?p=".($p+1)."&toID=$toID>Дальше</a>";
	
	echo "<form action=./><nobr><input type=text name=FindPerson value='' id=f> <input type=Submit value=' Найти ' id=f></nobr></form></td></tr></table>";


    echo "</center>";
}//--------------------------------------!!!!!!!!!!!!!!!!!!!!!!
    echo "</td></tr></table>";

	Foot();

?>