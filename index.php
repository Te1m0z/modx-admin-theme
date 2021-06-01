<?
include "inc/_config.inc.php";

$tMain = "main";
$tAuthors = "authors";
$tPart = "participants";
$tEvents = "events";
$tCath = "cathegories";

$cImgsPath = "img";
$cThumbImgsPath = "img/thumbs";
$cRecsPerPage = 30;
$cEventLength = 200;

$xAuthors = array();
$dAuthors = $dbMain->query("SELECT * FROM $tAuthors");

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

if (isset($wid)) {

    $wid = round($wid);
    //$dEvents=$dbMain->Query("SELECT * FROM $tMain WHERE id=$wid");
    $dEvents = $dbMain->Query("SELECT * FROM $tMain as t1 Left join $tAuthors as t2 on t1.`author_id`=t2.id WHERE t1.id=$wid");
    if ($dEvents->NumRows()) {
        $dEvents->NextResult();

        $xTitle = $dEvents->Get("title") . ". Автор: " . $dEvents->Get("name");
        $xAuthor = $dEvents->Get("author_id");
        $xCath = $dEvents->Get("cath_id");
        $xEvent = $dEvents->Get("event_id");
        if ($xEvent) {

            $dEvent = $dbMain->Query("SELECT * FROM $tEvents WHERE id=$xEvent");
            if ($dEvent->NumRows()) {
                $dEvent->NextResult();

                list($xxName, $xxDescr) = split("\n", $dEvent->Get("event"), 2);
                $xEv = MakeEvent(trim($xxDescr));
                $xDate = MakeDate($dEvent->Get("from_date"), $dEvent->Get("till_date"));
                $xDescription = str_replace("\n", "<p>", $xEv);
            }
        }
    }
}
?>

<html>

<head>
    <title>Российская история<? echo $xTitle ? " | $xTitle" : " в зеркале изобразительного искусства" ?> </title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <meta name="description"
          content="<?php isset($wid) ? print "На странице представлена картина $xTitle  Картина относится к жанру $xCaths[$xCath] и на ней представлено событие $xxName, дата $xDate " : print "На сайте представлено более 1000 репродукций произведений изобразительного искусства, отражающих ключевые моменты многовековой русской истории. Иллюстративная часть проекта дополнена текстовым материалом - подробным комментарием к сюжетам картин, сведениями о жизни и деятельности представленных на картинах персонажей, данными о творческом пути художников." ?>">
    <meta name="keywords" http-equiv="keywords"
          content="<?php isset($wid) ? print "$xTitle, $xCaths[$xCath], $xxName, $xDate, " : print "Репродукции картин, репродукции произведений изобразительного искусства, художники, авторы, персонажи, люди на картинах, события на картинах, жанры, события в изобразительном искусстве, родословные, хронология событий, российская история, исторические события" ?>">
    <link rel=stylesheet type=text/css href=/css/css.css>
</head>

<body bgcolor=#FFFFFF leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>

<center>
    <table width="800" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="2">
                <a href=./><img src="im/index_01.jpg" width="96" height="28" alt="" border=0></a>
            </td>
            <td colspan="2">
                <!--<a href=contacts><img src="im/contact.jpg" width="89" height="28" alt="" border=0></a></td>-->
                <img src="im/spacer.gif" width="89" height="28" alt="" border=0>
            </td>
            <td width="167" height="28" colspan="4">
                <img src="im/spacer.gif" width="167" height="28" alt="">
            </td>
            <td>
                <img src="im/index_04.jpg" width="62" height="28" alt="">
            </td>
            <td width="386" height="28" colspan="6">
                <img src="im/spacer.gif" width="386" height="28" alt="">
            </td>
        </tr>
        <tr>
            <td colspan="15">
                <img src="im/index_06.jpg" width="800" height="26" alt="">
            </td>
        </tr>
        <tr>
            <td colspan="15">
                <img src="im/index_07.jpg" width="800" height="42" alt="">
            </td>
        </tr>
        <tr>
            <td colspan="15">
                <img src="im/index_08.jpg" width="800" height="29" alt="">
            </td>
        </tr>
        <tr>
            <td>
                <img src="im/index_09.jpg" width="49" height="20" alt="">
            </td>
            <td colspan="2">
                <a href="time/default.php"><img src="im/time.jpg" width="91" height="20" alt="Время" border=0></a>
            </td>
            <td colspan="2">
                <a href="events/default.php"><img src="im/events.jpg" width="112" height="20" alt="События"
                                                  border=0></a>
            </td>
            <td colspan="2">
                <a href="people/default.php"><img src="im/people.jpg" width="91" height="20" alt="Люди" border=0></a>
            </td>
            <td colspan="3">
                <img src="im/index_13.jpg" width="81" height="20" alt="">
            </td>
            <td>
                <a href="class/default.php"><img src="im/type.jpg" width="156" height="20" alt="Жанр" border=0></a>
            </td>
            <td colspan="3">
                <a href="authors/default.php"><img src="im/authors.jpg" width="148" height="20" alt="Авторы"
                                                   border=0></a>
            </td>
            <td>
                <img src="im/index_16.jpg" width="72" height="20" alt="">
            </td>
        </tr>
        <tr>
            <td width="352" height="19" colspan="8">
                <img src="im/spacer.gif" width="352" height="19" alt="">
            </td>
            <td>
                <img src="im/index_18.jpg" width="62" height="19" alt="">
            </td>
            <td width="179" height="19" colspan="3">
                <img src="im/spacer.gif" width="179" height="19" alt="">
            </td>
            <td>
                <a href="tree/default.php"><img src="im/tree.jpg" width="104" height="19" alt="Родословные"
                                                border=0></a>
            </td>
            <td colspan="2">
                <a href="glossary/default.php"><img src="im/glossary.jpg" width="103" height="19" alt="Глоссарий"
                                                    border=0></a>
            </td>
        </tr>
        <tr>
            <td>
                <img src="im/spacer.gif" width="49" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="47" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="44" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="45" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="67" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="51" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="40" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="9" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="62" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="10" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="156" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="13" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="104" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="31" height="1" alt="">
            </td>
            <td>
                <img src="im/spacer.gif" width="72" height="1" alt="">
            </td>
        </tr>
    </table>
    <br>

    <table width=800 border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign=top width=100%>
                <?

                $flag = 1;
                if (isset($wid)) {
                    if ($dEvents->NumRows()) {
                        $flag = 0;
                        echo "<h1>&laquo;$xTitle&raquo;</h1>";

                        $xImage = $dEvents->Get("image");
                        $s = @GetImageSize("$cImgsPath/$xImage");
                        $w = $s[0];
                        $h = $s[1];
                        echo "<script>function ShowIt() {
var win=window.open('','','width=" . ($w + 2) . ",height=" . ($h + 2) . ",toolbar=no,location=0,directories=no,status=no,menubar=0,risizable=no,scrolling=no');			
with (win.document) {
	open();
	writeln(\"<html><head><title>&laquo;" . str_replace("\"", "&quot;", $xTitle) . "&raquo;</title></head><body leftmargin=0 topmargin=0 marginheight=0 marginwidth=0 onClick=window.close()>\");
	writeln(\"<img src=$cImgsPath/$xImage width=$w height=$h border=1>\");
	writeln(\"</body></html>\");
	close();
}
}</script>";


                        $xThumbImage = $dEvents->Get("thumb");
                        $s = @GetImageSize("$cThumbImgsPath/$xThumbImage");
                        echo "<a href='$cImgsPath/$xImage' onclick='javascript:ShowIt();return false;'><img src=$cThumbImgsPath/$xThumbImage " . $s[3] . " border=1 hspace=10 vspace=5 align=left id=img></a>";


                        echo "Автор: <b><a href='authors/?aid=$xAuthor'>" . $xAuthors[$xAuthor] . "</a></b><br>";


                        if ($xCath) echo "Жанр: <b><a href=class/?cid=$xCath>" . $xCaths[$xCath] . "</a></b><br>";

                        echo "Комментарии: " . nl2br($dEvents->Get("comments")) . "<br>";
                        echo "Персонажи: <b><br>";
                        $xParts1 = split(" ", $dEvents->Get("participants_ids"));
                        for ($j = 0; $j < sizeof($xParts); $j++) if ($xParts1[$j]) echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='people/?pid=$xParts1[$j]'>" . $xParts[$xParts1[$j]] . "</a><br>\n";
                        echo "</b><br>";


                        if ($xEvent) {

                            //	$dEvent=$dbMain->Query("SELECT * FROM $tEvents WHERE id=$xEvent");
                            //	if ($dEvent->NumRows()) {
                            //		$dEvent->NextResult();

                            //		list($xxName,$xxDescr)=split("\n",$dEvent->Get("event"),2);
                            //		$xEv=MakeEvent(trim($xxDescr));

                            echo "Событие: <b><a href=./events/?eid=$xEvent>" . $xxName . "</a></b><div id=small style='margin-left:10' align=justify>";
                            echo "Дата: <b>" . $xDate . "</b><br>\n";
                            echo "Описание:<br>" . $xDescription . "\n";
                            echo "</div><br>";
                            //	}
                        }
                    }
                } else {
                    ?>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="303"><img src="im/about.jpg" width="303" height="41" alt=""></td>
                            <td width=100%>
                                <!-- start of banner premiaruneta.ru -->
                                <a href="http://premiaruneta.ru/" target="_blank"><img height="31"
                                                                                       alt="Номинант Премии Рунета 2006"
                                                                                       src="/img/pr06_nom.gif"
                                                                                       width="88" border="0"
                                                                                       align=right></a>
                                <!-- end of banner premiaruneta.ru -->
                            </td>
                        </tr>
                    </table>


                    <div align=justify>
                        <img src="im/peace.jpg" width="58" height="48" alt="" valign=middle>, в котором мы живем,
                        устроен так, что события прошлого навсегда исчезают из объективного восприятия человека. То, что
                        "кануло в Лету", уже никогда не станет реально существующим, зримым. Увидеть "своими глазами", к
                        примеру, крещение князя Владимира или Полтавскую битву, строительство московского Кремля или
                        отречение Николая II, увы, нельзя.
                        <p>Однако у каждого человека есть возможность чуть-чуть приоткрыть завесу времени и взглянуть на
                            застывшее мгновение той или иной ушедшей эпохи. По словам русского художника В.Сурикова для
                            того "чтобы вдохнуть воздух прошлого, нужно достаточно долго и внимательно смотреть на
                            историческое полотно, выполненное хорошим художником".
                        <p>Историческая тема издавна занимает в изобразительном искусстве одно из ведущих мест. Пожалуй,
                            нет ни одного значительного художника, который не отдал бы дань этому жанру. Это утверждение
                            представляется справедливым еще и потому, что работы мастеров любого жанра (портретного,
                            бытового, пейзажного) неизбежно несут на себе отпечаток своего времени.
                        <p>Понятие "историческая живопись" не укладывается в отведенные ей жесткие рамки <i>жанра</i>.
                            Бытовая сцена, портрет, пейзаж рано или поздно поднимаются до уровня исторического обобщения
                            и становятся по сути своей произведением, отражающим историю. К какому жанру следует отнести
                            "Парадный портрет Петра I" кисти Натье, или картину И.Репина "Крестный ход в Курской
                            губернии"? Разве это только <i>портрет</i> и только <i>бытовая сцена</i>?
                        <p>В русском искусстве историческая тематика зарождается в иконописи, где религиозные сюжеты
                            часто окрашиваются реальными историческими событиями. Таковы иконы "Чудо о Знамение" (2-я
                            половина XV в.) со сценами защиты Новгорода от суздальцев и "Церковь воинствующая" (середина
                            XVI в.) с изображением похода Ивана Грозного на Казань.
                        <p>Постепенное приближение к реалистическому изображению исторических событий можно проследить в
                            книжных миниатюрах "Лицевого летописного свода" и "Жития Сергия Радонежского", в которых
                            события истории становятся смысловой доминантой.
                        <p>В России становление исторического жанра как самостоятельной части изобразительного искусства
                            относится ко второй половине XVIII века и связано с основанием Петербургской академии
                            художеств. Работы первых русских живописцев <i>исторической школы</i> были весьма далеки от
                            исторической правды. Если бы не название картины (обычно очень длинное), то трудно было бы
                            определить, к какому времени, к какой стране относится изображаемое событие. Например, в
                            картине И. Акимова "Великий князь Святослав, целующий мать и детей по возвращении с Дуная в
                            Киев" (1773) князь одет в античные доспехи и пышный шлем, дети - в хитонах, и ничего
                            подлинно исторического (кроме самого факта возвращения Святослава) зритель в картине не
                            найдет. В том же ложно историческом ключе решена картина А. Лосенко "Великий князь Владимир
                            сообщает своей жене Рогнеде о победе, одержанной им над ее отцом Рогволдом, князем Полоцким"
                            (1770). Здесь - те же пышные одеяния, театральные позы, по образцу классических трагедий,
                            модных в то время.
                        <p>Но постепенно, под влиянием крепнущего реалистического направления и новых эстетических
                            взглядов, в традициях русского исторического жанра начинают происходить существенные
                            изменения. Главное отличие нового этапа заключается в том, что событие воспроизводится на
                            полотне с высокой исторической достоверностью.
                        <p>Ярчайшими образцами нового этапа в развитии исторического жанра являются картины К.Флавицкого
                            "Княжна Тараканова в Петропавловской крепости во время наводнения", (1864), Н. Ге "Петр
                            допрашивает царевича Алексея" (1871), И. Репина "Иван Грозный и сын его Иван" (1885).
                        <p>Вершиной в русской исторической живописи стало творчество В. Сурикова. Его работы "Утро
                            стрелецкой казни" (1881), "Боярыня Морозова" (1887), "Покорение Сибири Ермаком" (1895),
                            "Переход Суворова через Альпы" (1899) - полотна огромной силы воздействия. Каждая из этих
                            картин поднимает мощнейший пласт русской истории, заставляет зрителя не просто остановиться
                            в восхищении, но глубоко и серьезно задуматься.
                        <p>В творчестве русских художников начала XX века историческая тема начала приобретать черты
                            символической трактовки. Однако реалистические традиции русской живописной школы были
                            настолько сильны, что даже такие поборники романтико-символических фантазий, как М. Врубель,
                            Н.Рерих, А. Бенуа, К. Сомов в своих "исторических пейзажах" и стилизованных "придворных
                            сценах" отталкивались от бытового реализма и тонкого ощущения своеобразия изображаемой
                            эпохи.
                        <p>В изобразительном искусстве советского периода исторический жанр приобретает принципиально
                            иной характер. Основным критерием творческой концепции автора становится идеологическая
                            направленность сюжета. Таковы работы И. Шадра "Булыжник - оружие пролетариата" (1927), А.
                            Дейнеки "Оборона Петрограда" (1928), А. Герасимова "Ленин на трибуне" (1929),
                            Б.Иогансона "Допрос коммунистов" (1933). Обращение художников к событиям отдаленного
                            прошлого чиновниками от искусства не поощрялось. И только в годы Великой Отечественной войны
                            всенародный патриотический подъем привел к появлению монументальных полотен, воспевающих
                            героические страницы истории Древней Руси. Мужеством и гражданским пафосом проникнуты
                            картины П. Корина "Александр Невский" (1942),
                            А.Бубнова "Утро на Куликовом поле" (1943), М. Авилова "Поединок Пересвета с Челубеем"
                            (1943).
                        <p>Однако в последующие годы традиции исторической живописи, свободной от идеологических
                            догматов вновь оказались утраченными. Тем не менее, авторы проекта полагают, что у них нет
                            морального права отправлять в "запасник" картины явно конъюнктурного, псевдоисторического
                            характера, ибо и они также являются документальным свидетельством своей эпохи.
                        <p>В проекте представлено более 1500 репродукций произведений изобразительного искусства,
                            отражающих ключевые моменты многовековой русской истории. Иллюстративная часть проекта
                            дополнена текстовым материалом - подробным комментарием к сюжетам картин, сведениями о жизни
                            и деятельности представленных на картинах персонажей, данными о творческом пути художников.
                        <p>Проект предусматривает возможность использования системы поиска необходимой информации по
                            трем вариантам: <i>событию</i>, <i>дате события</i> (хронологии) и <i>персонажу события</i>.
                            Генеалогия ряда деятелей русской истории, а также расшифровка некоторых специфических
                            терминов выделена в отдельные информационные блоки.
                    </div>

                    <?
                }

                if ($flag && !1) {
                    echo "</td><td valign=top>";
                    echo "<h1>Работы</h1>";

                    if (!$fL) $fL = "А";
                    $xCond = "WHERE UPPER(title) LIKE '$fL%' ORDER BY title";

                    $dEvents = $dbMain->Query("SELECT * FROM $tMain $xCond");

                    //		$dEvents=$dbMain->Query("SELECT * FROM $tMain ORDER BY id");
                    $xRows = $dEvents->NumRows();

                    if ($fPage) $dEvents->Seek($fPage);

                    echo "<ul type=square>";
                    //		for ($i=0;$i<$cRecsPerPage;$i++) {
                    for ($i = 0; $i < $xRows; $i++) {
                        //		if ($fPage+$i>=$xRows) break;

                        $dEvents->NextResult();

                        $xId = $dEvents->Get("id");
                        $xTitle = $dEvents->Get("title");
                        $xAuthor = $dEvents->Get("author_id");
                        $xImage = $dEvents->Get("image");
                        $xThumbImage = $dEvents->Get("thumb");
                        $xParts = $dEvents->Get("participants_ids");

                        $fL = rawurlencode(substr($xTitle, 1, 1));

                        echo "	<li> <a href='$ps?wid=$xId'><b>" . $xTitle . "</b></a> <span id=small>/ <a href='authors/?aid=$xAuthor'>" . $xAuthors[$xAuthor] . "</a></span><div id=smallest>" . nl2br($xDescr) . "</div>\n";
                    }
                    echo "</ul>";

                    /*	$p=new Pages($xRows,$cRecsPerPage,$fPage);
$p->SetScript($ps);
$p->SetParameterName("fPage");
$p->SetAddParams("code=$code");
$p->SetShownPages(10);

$p->SetDivider(" | ");
$p->SetText("Работы");
$p->SetPrevNext("&laquo; назад","дальше &raquo;");

$p->SetStyle("current","color:red; font-weight:bold");
$p->SetStyle("dividers","color:C0C0C0; font-size:7pt");*/

                    echo "<center id=smallest style='padding:0 10 0 10'>";
                    LetterPages("fL", $fL);
                    echo "</center>";
                }


                ?>
                <br><img src=/i/spacer.gif width=250 height=1 border=0>
            </td>
        </tr>
    </table>


    <table width=800 border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td>
                <img src="images/index_22.jpg" width=800 height=17 alt="">
            </td>
        </tr>
        <tr>
            <td>
                <center><?
                    include_once "inc/banner.inc.php";
                    print "<div style='text-align: center; padding-top: 5px'>" . getHtml() . "</div>";
                    ?></center>
            </td>
        </tr>
    </table>
    <br>
    <!--
<a href="http://real.mdc.ru" target="_blank"><img
src="http://real.mdc.ru/img/ris.gif" alt="Новая реальность" border=0></a>-->

</center>
</body>

</html>