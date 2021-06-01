<?
// get params from $_GET n $_POST
foreach ($_GET as $k => $v) {
    $GLOBALS[$k] = $v;
}


foreach ($_POST as $k => $v) {
    $GLOBALS[$k] = $v;
}


//-------- Settings -----------
$site_name = "Сайт &laquo;Российская история ... &raquo;";

$db_name = "history";
$host = "localhost";
$user = "root";
$pass = "";

$months = array("январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь");
$mons = array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
$days = array("пн.", "вт.", "ср.", "чт.", "пт.", "сб.", "вс.");
$d = array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
$fullname = "";

$cCenturies = array("- век -" => "00", "VIII" => "08", "IX" => "09", "X" => "10", "XI" => "11", "XII" => "12", "XIII" => "13", "XIV" => "14", "XV" => "15", "XVI" => "16", "XVII" => "17", "XVIII" => "18", "XIX" => "19", "XX" => "20", "XXI" => "21");
$cDays = array("- день -" => "00");
for ($i = 1; $i <= 31; $i++) {
    $j = sprintf("%02d", $i);
    $cDays[$j] = $j;
}
$cMonths = array("- месяц -" => "00", "январь" => "01", "февраль" => "02", "март" => "03", "апрель" => "04", "май" => "05", "июнь" => "06", "июль" => "07", "август" => "08", "сентябрь" => "09", "октябрь" => "10", "ноябрь" => "11", "декабрь" => "12");


$br = substr_count($_SERVER['HTTP_USER_AGENT'], "MSIE");
if ($br) {
    $browser = "Internet Explorer";
    $br = "IE";
} else {
    $browser = "Netscape Navigator";
    $br = "NN";
}

$ps = $_SERVER['HTTP_HOST'];


//-------- Includes -----------

//require "classes/calendar.class.php";
require "classes/form.class.php";
require "classes/pages.class.php";
require "classes/sql.class.php";

//-------- Settings -----------

$db = mysql_connect($host, $user, $pass);
mysql_select_db($db_name, $db) or die("Can't select DataBase!<br>\n");
mysql_query("set names 'cp1251'", $db);

$dbMain = new SQL($db_name, $host, $user, $pass);


$tMain = "main";
$tAuthors = "authors";
$tPart = "participants";
$tEvents = "events";
$tCath = "cathegories";

$cImgsPath = "/img";
$cThumbImgsPath = "/img/thumbs";
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


//-------- Functions ----------
function error($msg)
{
    echo "<div id=error>[!] <b>$msg</b></div>\n";
}

function query($q, $db)
{
//    error($q);
    $q = str_replace("insert", "insert delayed", $q);
    $result = MYSQL_QUERY($q, $db) or error("Ошибка в запросе: $q");
    return $result;
}

function upload($file, $path)
{

//    echo "file: $file<br>path: $path<br>";
    if (!is_uploaded_file($file)) {
        error("Файл не загружен!");
        return 0;
    }

    if (!move_uploaded_file($file, $path)) {
        error("Не могу скопировать временный файл в $path!");
        return 0;
    }
    return 1;
}

function table_exists($tn, $dbn)
{
    $r = mysql_list_tables($dbn);
    $pass = 0;
    for ($i = 0; $i < mysql_numrows($r); $i++) {
        $t = mysql_tablename($r, $i);
        if ($tn == $t) return 1;
    }
    return 0;
}

function datetime_form($d, $m, $y, $hh, $mm, $dn, $mn, $yn, $ht, $mt)
{
    global $mons;
    if ($yn == "b_year") {
        $fr_d = 1950;
        $to_d = 2010;
    } else {
        $fr_d = date("Y") - 2;
        $to_d = date("Y") + 2;
    }

    if ($dn != "") {
        echo "<select name=$dn id=sform><option value=''>День";
        for ($i = 1; $i <= 31; ++$i) {
            echo "<option value=$i";
            if ($d == $i) {
                echo " selected";
            }
            echo ">$i</option>";
        }
        echo "</select>\n";
    }
    if ($mn != "") {
        echo "<select name=$mn id=sform><option value=''>Месяц";
        for ($i = 1; $i <= 12; ++$i) {
            $j = $i;
            echo "<option value=$i";
            if ($m == $i) {
                echo " selected";
            }
            echo ">" . $mons[$j - 1];
        }
        echo "</select>\n";
    }

    if ($yn != "") {
        echo "<select name=$yn id=sform><option value=''>Год";
        for ($i = $fr_d; $i <= $to_d; ++$i) {
            echo "<option value=$i";
            if ($y == $i) {
                echo " selected";
            }
            echo ">$i";
        }
        echo "</select>\n";
    }
    if ($ht != "") {
        echo " - <select name=$ht id=sform><option value=''>Час.";
        for ($i = 0; $i <= 24; ++$i) {
            echo "<option value=$i";
            if ($hh == $i) {
                echo " selected";
            }
            echo ">$i";
        }
        echo "</select>";
    }
    if ($mt != "") {
        echo ":<select name=$mt id=sform><option value=''>Мин.";
        for ($i = 0; $i <= 59; ++$i) {
            $mins = sprintf("%02d", $i);
            echo "<option value=$i";
            if ($mm == $i) {
                echo " selected";
            }
            echo ">$mins";
        }
        echo "</select>\n";
    }
}

function date_form($d, $m, $y, $dn, $mn, $yn)
{
    global $mons;
    if ($dn != "") {
        echo "<select name=$dn id=sform><option value=''>День";
        for ($i = 1; $i <= 31; ++$i) {
            echo "<option value=$i";
            if ($d == $i) {
                echo " selected";
            }
            echo ">$i";
        }
        echo "</select>\n";
    }
    if ($mn != "") {
        echo "<select name=$mn id=sform><option value=''>Месяц";
        for ($i = 1; $i <= 12; ++$i) {
            $j = $i;
            echo "<option value=$i";
            if ($m == $i) {
                echo " selected";
            }
            echo ">" . $mons[$j - 1];
        }
        echo "</select>\n";
    }
    if ($yn != "") {
        echo "<select name=$yn id=sform><option value=''>Год";
        for ($i = 2001; $i <= 2010; ++$i) {
            echo "<option value=$i";
            if ($y == $i) {
                echo " selected";
            }
            echo ">$i";
        }
        echo "</select>\n";
    }
}

function mod($val, $divi)
{
    return ($val - ($divi * floor($val / $divi)));
}

function MakeEvent($a)
{
    $a = preg_replace("/<pid=([0-9]+)>/", "<a href=/people?pid=\\1>", $a);
    $a = preg_replace("/<\/pid>/", "</a>", $a);
    return $a;
}

function MakeDate($fFrom, $fTill)
{
    global $xCenturies, $cMonths, $months;

//    var_dump($fFrom);

    $xFrom = explode(".", $fFrom);
    $xTill = explode(".", $fTill);
    $cPostFix = array("в.", "г.", "", "");
    $xOut = "";
    $xFlag = -1;
    $xOne = 0;
//    print_r($xFrom);

    for ($i = 0; $i < 4; $i++) {
        $a = $xFrom[$i];
        $b = $xTill[$i];

        if ($a && $b && $a == $b && $xFlag == ($i - 1)) {
            $xFlag = $i;
        } else {
            if ((!round($a) || !round($b)) && (round($a) || round($b))) $xOne = 1;
        }
    }

    $xa = "";
    $xb = "";
    $xBreak = 0;
    for ($i = 0; $i < 4; $i++) {
        $a = $xFrom[$i];
        $b = $xTill[$i];

        $a = intval($a);
        $b = intval($b);

//        var_dump($a);

        if ($i == 2) {
            $a = $months[$a];
            $b = $months[$b];
        }

        if ($i <= $xFlag || $xOne) {
            if ($a && $a != "00" && $a != "0000") $xOut .= ($xOut ? ", " : "") . $a . ($cPostFix[$i] ? " " . $cPostFix[$i] : "");
            else break;
        } else {
            if ($a && $a != "00" && $a != "0000") $xa .= ($xa ? " " : "") . $a . ($cPostFix[$i] ? " " . $cPostFix[$i] : "");
            else $xBreak = 1;
            if ($b && $b != "00" && $b != "0000") $xb .= ($xb ? " " : "") . $b . ($cPostFix[$i] ? " " . $cPostFix[$i] : "");
            else $xBreak = 1;
        }
        if ($xBreak) break;
    }
    if (($xa || $xb) && !$xOne) $xOut .= ($xOut ? ", " : "") . $xa . "-" . $xb;
    return $xOut;
}

function LetterPages($params, $fActive = "", $table_name = "")
{
    global $ps;
    global $dbMain;
    //print_r($_GLOBALS);
    if ($table_name == "") {

        $cLetters = "%АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
        $cSLetters = "%абвгдеёжзийклмнопрстуфхцчшщъыьэюя";

        for ($i = 0; $i < strlen($cLetters); $i++) {
            if ($i) echo " | ";
            $xLet = substr($cLetters, $i, 1);
            $xLetS = substr($cSLetters, $i, 1);
            if ($fActive != $xLet) echo "<a href=$ps?$params=" . rawurlencode($xLet) . ">" . mb_convert_encoding($xLet, 'utf-8', 'cp1251') . "</a>";
            else echo "<font color=red>$xLet</font>";
        }
    } else {

        $letters = $dbMain->Query("SELECT SUBSTR(TRIM(name),1,1) as firstletter from $table_name GROUP BY firstletter");
        //print $letters->NumRows();
        for ($i = 0; $i < $letters->NumRows(); $i++) {
            if ($i) echo " | ";
            $letters->NextResult();
            $letter = $letters->Get("firstletter");

            if ($fActive != $letter) echo "<a href=$ps?$params=" . rawurlencode($letter) . ">" . mb_convert_encoding($letter, 'utf-8', 'cp1251') . "</a>";
            else echo "<font color=red>$letter</font>";

        }


    }
}


//-------- Functions ----------
?>
