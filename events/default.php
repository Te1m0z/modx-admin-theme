<?
require "../inc/_config.inc.php";
$tMain = "main";
$tAuthors = "authors";
$tPart = "participants";
$tEvents = "events";
$tCath = "cathegories";

$cCut = 100;

$cImgsPath = "../img";
$cThumbImgsPath = "../img/thumbs";
$cRecsPerPage = 20;
$cEventLength = 200;

$xAuthors = array();
$dAuthors = $dbMain->Query("SELECT * FROM $tAuthors");
for ($i = 0; $i < $dAuthors->NumRows(); $i++) {
    $dAuthors->NextResult();
    $xId = $dAuthors->Get("id");
    $xName = $dAuthors->Get("name");
    $xAuthors[$xId] = $xName;
}
$xCaths = array();
$dCaths = $dbMain->Query("SELECT * FROM $tCath");
for ($i = 0; $i < $dCaths->NumRows(); $i++) {
    $dCaths->NextResult();
    $xId = $dCaths->Get("id");
    $xName = $dCaths->Get("name");
    $xCaths[$xId] = $xName;
}
$xParts = array();
$dParts = $dbMain->Query("SELECT * FROM $tPart");
for ($i = 0; $i < $dParts->NumRows(); $i++) {
    $dParts->NextResult();
    $xId = $dParts->Get("id");
    $xName = $dParts->Get("name");
    $xParts[$xId] = $xName;
}


$is = 0;
$FLAG = 1;

//if (isset($fPage)) {
$fPage = round($fPage);
//};

if (isset($eid)) {

    $eid = round($eid);

    $dEvent = $dbMain->Query("SELECT * FROM $tEvents WHERE id=$eid");
    if ($dEvent->NumRows()) {
        $dEvent->NextResult();
        $flag = 0;

        $xDate = $dEvent->Get("date");
        $xName = $dEvent->Get("event");
        //var_dump($xName);

        $xFromDate = $dEvent->Get("from_date");
        $xTillDate = $dEvent->Get("till_date");
        list($xName, $xDescr) = split("\n", $xName, 2);
        $xDate = MakeDate($xFromDate, $xTillDate);

        $dEvents = $dbMain->Query("SELECT * FROM $tMain WHERE event_id=$eid ORDER BY title ASC");
        $xNumRows = $dEvents->NumRows();
        if ($xNumRows) {
            for ($i = 0; $i < $xNumRows; $i++) {
                $dEvents->NextResult();
                $xEvents[$i]["xId"] = $dEvents->Get("id");
                $xEvents[$i]["xTitle"] = $dEvents->Get("title");
                $eventsList[$i] = "«" . $xEvents[$i]["xTitle"] . "»";
                $xEvents[$i]["xThumb"] = $dEvents->Get("thumb");
            }
        }
        $xEvList = implode(",", $eventsList);
        Head("События | $xDate | $xName", "events", "$xDate, $xName, $xEvList", "На странице представлено описание такого события как $xName ($xDate) и картины относящиеся к этому событию.");
        echo "<table width=100%>";
        $is = 1;

        echo "<td width=100% valign=top><h1>" . $xDate . "</h1>";
//			echo "<td width=100% valign=top><h1>$xDate</h1>";


        if ($xName || $xDescr) {
            echo "<div align=justify>";
            if ($xName) echo "<b>" . $xName . "</b><br>";
            echo str_replace("\n", "<p>", MakeEvent($xDescr));
            echo "</div>";
        } else echo "Информация отстутствует";


        if ($xNumRows) {
            echo "<h1>Произведения:</h1>";
            echo "<ul type=square>";
            $xMarquee = "<table border=0 cellpadding=3 cellsapcing=2><tr>";
            for ($i = 0; $i < $xNumRows; $i++) {
                //$dEvents->NextResult();

                $xId = $xEvents[$i]["xId"]; //$dEvents->Get("id");
                $xTitle = $xEvents[$i]["xTitle"];//$dEvents->Get("title");
                $xThumb = $xEvents[$i]["xThumb"];//$dEvents->Get("thumb");
                echo "	<li> <a href='/?wid=$xId'>&laquo;<b>" . $xTitle . "</b>&raquo;</a>\n";
                $xMarquee .= "<td align=center width=100><a href=../?wid=$xId title='$xTitle'><img src=$cThumbImgsPath/$xThumb border=1 id=img></a></td>";
            }
            $xMarquee .= "</tr></table>";
            $xMovie = $i;
            echo "</ul>";
        }

        echo "</td>";
    }
}


if (empty($is)) {

    Head("События | стр." . ($fPage / $cRecsPerPage + 1), "events", "Список событий стр." . ($fPage / $cRecsPerPage + 1), "На данной странице представлен список событий представленных на сайте. ");
    echo "<table width=100%>";


    echo "<td valign=top>";
    echo "<h1>События</h1>";

    $dEvents = $dbMain->Query("SELECT * FROM $tEvents WHERE event NOT LIKE ' ' AND event NOT LIKE '' ORDER BY event,from_date");
    $xRows = $dEvents->NumRows();

    if ($fPage) $dEvents->Seek($fPage);

    echo "<ul type=square>";
    for ($i = 0; $i < $cRecsPerPage; $i++) {
        if ($fPage + $i >= $xRows) break;

        $dEvents->NextResult();

        $xId = $dEvents->Get("id");
        $xDate = $dEvents->Get("date");
        $xName = $dEvents->Get("event");

        $xFromDate = $dEvents->Get("from_date");
        $xTillDate = $dEvents->Get("till_date");

        list($xName, $xDescr) = split("\n", $xName, 2);

        if (trim($xName) || $xFromDate) {
            echo "	<li> <a href='$ps?eid=$xId&fPage=$fPage'><b>";
            if (!$xName) echo(strlen($xDescr) > $cCut ? substr($xDescr, 0, $cCut) . "..." : $xDescr);
            else echo mb_convert_encoding($xName, 'utf-8', 'cp1251');

            echo "</b> (" . MakeDate($xFromDate, $xTillDate) . ")</a>\n";
        } else $cRecsPerPage++;
    }
    echo "</ul>";

    $p = new Pages($xRows, $cRecsPerPage, $fPage);
    $p->SetScript($ps);
    $p->SetParameterName("fPage");
    $p->SetAddParams("code=$code");

    $p->SetDivider(" | ");
    $p->SetText("События");
    $p->SetPrevNext("&laquo; назад", "дальше &raquo;");

    $p->SetStyle("current", "color:red; font-weight:bold");
    $p->SetStyle("dividers", "color:C0C0C0; font-size:7pt");

    echo "<center>";
    $p->PrintPages();
    echo "</center>";
} //-----------------!!!!!!!!!!!!

echo "<br><img src=/i/spacer.gif width=250 height=1 border=0></td></tr></table>";
echo "<a name=marquee></a>";
if (isset($xMovie)) {
    echo "<center><img src=/i/index_08.gif><br><marquee width=600 scrollamount=10 onMouseOver='this.stop()' onMouseOut='this.start()'>" . $xMarquee . "</marquee></center>";
}
Foot();