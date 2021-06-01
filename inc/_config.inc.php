<?
require "site.inc.php";
function Head($Title = "", $img = "events", $keywords = "Репродукции картин, репродукции произведений изобразительного искусства, художники, авторы, персонажи, люди на картинах, события на картинах, жанры, события в изобразительном искусстве, родословные, хронология событий, российская история, исторические события", $description = "На сайте представлено более 1000 репродукций произведений изобразительного искусства, отражающих ключевые моменты многовековой русской истории. Иллюстративная часть проекта дополнена текстовым материалом - подробным комментарием к сюжетам картин, сведениями о жизни и деятельности представленных на картинах персонажей, данными о творческом пути художников.")
{
    //$description.="  ";
    //$keywords.=", "

    ?>

    <html>

    <head>
        <title>Российская история в зеркале изобразительного искусства<?php if ($Title) echo " | " . $Title ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
        <meta name="description" content="<?= $description ?>">
        <meta name="keywords" http-equiv="keywords" content="<?= $keywords ?>">
        <link rel=stylesheet type=text/css href=/css/css.css>
    </head>

    <body bgcolor=#FFFFFF leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>
    <center>
    <table width="800" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <a href="/"><img src="/im/others_01.gif" width="104" height="29" alt="" border=0></a>
            </td>
            <td colspan="2">
                <!--			<a href=/contacts/><img src="/im/others_02.gif" width="95" height="29" alt="" border=0></a></td>
--><img src="/im/spacer.gif" width="95" height="29" alt="" border=0>
            </td>
            <td width="459" height="29" colspan="5">
                <img src="/im/spacer.gif" width="459" height="29" alt="">
            </td>
            <td colspan="3">
                <img src="/im/others_04.gif" width="63" height="29" alt="">
            </td>
            <td width="79" height="29">
                <img src="/im/spacer.gif" width="79" height="29" alt="">
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <img src="/im/_<?= $img ?>.jpg" width="800" height="79" alt="">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <a href="/time/default.php"><img src="/im/_nb1_01.gif" width="146" height="20" alt="Время" border=0></a>
            </td>
            <td colspan="2">
                <a href="/events/default.php"><img src="/im/_nb1_02.gif" width="139" height="20" alt="События" border=0></a>
            </td>
            <td>
                <a href="/people/default.php"><img src="/im/_nb1_03.gif" width="131" height="20" alt="Люди"
                                                   border=0></a>
            </td>
            <td>
                <a href="/class/default.php"><img src="/im/_nb1_04.gif" width="136" height="20" alt="Жанр" border=0></a>
            </td>
            <td colspan="3">
                <a href="/authors/default.php"><img src="/im/_nb1_05.gif" width="115" height="20" alt="Авторы" border=0></a>
            </td>
            <td colspan="3">
                <img src="/im/_nb1_06.gif" width="133" height="20" alt="">
            </td>
        </tr>
        <tr>
            <td width="561" height="23" colspan="7">
                <img src="/im/spacer.gif" width="561" height="23" alt="">
            </td>
            <td colspan="3">
                <a href="/tree/default.php"><img src="/im/_tree.gif" width="114" height="23" alt="Родословные" border=0></a>
            </td>
            <td colspan="2">
                <a href="/glossary/default.php"><img src="/im/_glossary.gif" width="125" height="23" alt="Глоссарий"
                                                     border=0></a>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/im/spacer.gif" width="104" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="42" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="53" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="86" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="131" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="136" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="9" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="97" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="9" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="8" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="46" height="1" alt="">
            </td>
            <td>
                <img src="/im/spacer.gif" width="79" height="1" alt="">
            </td>
        </tr>
    </table>

    <table width=760 border=0 cellpadding=0 cellspacing=0>
    <tr>
    <td>
    <!-- Here Comest the Content -->
    <?php
}

function Foot()
{
    ?>

    <br><br><!-- Here Comest the Content -->
    </td>
    </tr>
    </table>
    <table width=800 border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td>
                <img src="/i/index_08.gif" width=800 height=16 alt="">
            </td>
        </tr>
        <tr>
            <td>
                <center><?
                    require "banner.inc.php";
                    print "<div style='text-align: center; padding-top: 5px'>" . getHtml() . "</div>";
                    ?></center>
            </td>
        </tr>
    </table>
    </center>

    </body>

    </html>


    <?php
}

?>