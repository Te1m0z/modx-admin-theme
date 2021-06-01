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

$xPreview = "";


$is = 0;
if (isset($century)) {

    $century = round($century);
    $dEventOld = $dbMain->Query("SELECT * FROM $tEvents WHERE from_date LIKE '" . sprintf("%02d", $century) . ".%' OR till_date LIKE '" . sprintf("%02d", $century) . ".%' ORDER BY from_date ASC");

    Head("Время | $century в.", "time");
    echo "<table width=100% border=0>";


    if ($eid) {
        echo "<td width=100% valign=top>";


        $dEvent = $dbMain->Query("SELECT * FROM $tEvents WHERE id=$eid");
        if ($dEvent->NumRows()) {
            $dEvent->NextResult();

            $xDate = $dEvent->Get("date");
            $xName = $dEvent->Get("event");

            $xFromDate = $dEvent->Get("from_date");
            $xTillDate = $dEvent->Get("till_date");

            echo "<h1>" . MakeDate($xFromDate, $xTillDate) . "</h1>";

            if ($xName) echo "<div align=justify>" . str_replace("\n", "<p>", MakeEvent($xName)) . "</div>";
//				else echo "Информация отстутствует";

            $dEvents = $dbMain->Query("SELECT * FROM $tMain WHERE event_id=$eid ORDER BY title");
            $xNumRows = $dEvents->NumRows();
            if ($xNumRows) {
                echo "<h1>Произведения:</h1>";
                echo "<ul type=square>";

                $xMarquee = "<table border=0 cellpadding=3 cellsapcing=2><tr>";
                $xMovie = 0;
                for ($i = 0; $i < $xNumRows; $i++) {
                    $dEvents->NextResult();

                    $xId = $dEvents->Get("id");
                    $xThumb = $dEvents->Get("thumb");
                    $xTitle = $dEvents->Get("title");

                    $xMarquee .= "<td align=center width=100><a href=../?wid=$xId title='$xTitle'><img src=$cThumbImgsPath/$xThumb border=1 id=img></a></td>";
                    $xMovie++;

                    echo "	<li> <a href='/?wid=$xId'>&laquo;<b>" . $xTitle . "</b>&raquo;</a>\n";
                }
                $xMarquee .= "</tr></table>";
                echo "</ul>";
            }
        }
        echo "</td>";
        $is = 1;

        //echo "<td width=250 valign=top><h1>$century век</h1>";
    } else {
        echo "<td width=100% valign=top><h1>$century век</h1>";

        $is = 1;

        echo "<ul type=square>";

        for ($i = 0; $i < $dEventOld->NumRows(); $i++) {
            $dEventOld->NextResult();

            $xId = $dEventOld->Get("id");

            $xDate = $dEventOld->Get("date");
            $xName = $dEventOld->Get("event");

            if (!$xName) $xName = "<i>(портрет, пейзаж, быт и др.)</i>";

            $xFromDate = $dEventOld->Get("from_date");
            $xTillDate = $dEventOld->Get("till_date");

            if ($xFromDate) echo "	<li> <a href='./?century=$century&eid=$xId&fPage=$fPage'><b>" . MakeDate($xFromDate, $xTillDate) . "</b>" . (trim($xName) ? " - " : "") . (strlen($xName) > $cCut ? substr($xName, 0, $cCut) . "..." : $xName) . "</a>\n";
        }
        echo "</ul>" . ($eid ? "<img src=/i/spacer.gif width=250 height=1 border=0>" : "") . "</td>";
    }
}
if (!$is) {
    Head("Время", "time");
    echo "<table width=100%>";
}


if (!isset($eid)) {
    echo "<td valign=top>";
    echo "<h1>Временной отрезок</h1>";

    $dEvents = $dbMain->Query("SELECT * FROM $tEvents ORDER BY from_date");
    $xRows = $dEvents->NumRows();

    if (isset($fPage)) $dEvents->Seek($fPage);

    $xLast = 0;
    echo "<ul type=square>";
    for ($i = 0; $i < $xRows; $i++) {
        $dEvents->NextResult();

        $xId = $dEvents->Get("id");
        $xDate = $dEvents->Get("date");
        $xName = $dEvents->Get("event");

        $xFromDate = $dEvents->Get("from_date");
        $xTillDate = $dEvents->Get("till_date");

        $xCentury = substr($xFromDate, 0, 2);

        if ($xLast != $xCentury) {
            echo "<li> <a href=" . $ps . "?century=" . $xCentury ."><b>" .$xCentury . "</b></a>\n";
            $xLast = $xCentury;
        }
    }
    echo "</ul>";

    echo "<br><img src=/i/spacer.gif width=250 height=1 border=0></td></tr></table>";
} else echo "</table>";

if (isset($xMovie)) {
    echo "<center><img src=/i/index_08.gif><br><marquee width=600 scrollamount=10 onMouseOver='this.stop()' onMouseOut='this.start()'>" . $xMarquee . "</marquee></center>";
}

Foot();
?>