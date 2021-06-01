<?//<font color=red>Диспетчер пользователей</font>

//------------------------------------------------------------------------
// Admin script 1.0
//		Administrative tool used for:
//		- managing users, that have administrative privileges
//		- set to users permission to different parts of site
//		- refresh list of site parts
//
//		Last updated: 13.XII.2002
//		Author: Nick S. Bogdanov <nick@sgu.ru>
//
//------------------------------------------------------------------------

	$exclude=1;

	include("admin.inc.php");
	$table=$admin_table;
	$show_on_page=30;

    if (table_exists($table,$db_name)) $pass=1; else $pass=0;
	
	$navbar=array(
	"2::!\$pass::$ps?go=install&code=$code::Инсталляция",
	"2::\$pass::$ps?go=uninstall&code=$code::Деинсталляция::1",
	"2::\$pass::$ps?go=adduser&code=$code::Добавить пользователя",
	"2::\$pass::$ps?go=refine&code=$code::Обновить список компонентов::1"
	);	
	
	$op_names=array(
	"del"=>"Удаление пользователя|2",
	"install"=>"Инсталляция|2",
	"uninstall"=>"Деинсталляция|2",
	"insert"=>"Изменение/добавление данных",
	"adduser"=>"Добавление пользователя",
	"refine"=>"Обновление списка разделов",
	"access"=>"Доступ к разделам|2",
	"store_access"=>"Доступ к разделам|2",
	"edit"=>"Редактирование|2"
	);

	$ss=split("\|",$op_names[$go]);
	$s=$ss[0];
	$p=round($ss[1]);
	if (!$global_access || ($p && $p>$global_access && $global_access>=0)) {$s=$access_denied; $go="";}
	
	head("Система администрирования",$s,"$ps?code=$code");
	
	if (!$pass) {error("Авторизация не требуется!<br><br>");}
	
	if ($go=="edit") $go="adduser";

	navigation($navbar);
	middle($s);
	
    
	function alter($l) {
	  global $db,$db_name,$table,$registry,$ma;
//-------------- Уничтожение таблиц для обновления реестра ---------
//		$r=query("DROP TABLE $registry",$db);
//		$r=query("CREATE TABLE $registry(id INT NOT NULL AUTO_INCREMENT, script VARCHAR(200) NOT NULL, name VARCHAR(200),PRIMARY KEY(id))",$db);
		$r=query("DELETE FROM $registry WHERE sub=''",$db);
//-------------- Уничтожение таблиц для обновления реестра ---------

		$columns="";
		$handle=opendir("./");
		while (($file = readdir($handle))!==false) {
			if (!is_dir("./$file") && substr($file,0,1)=="_") {

			    $sn=split("\.",$file);
			    $fn=$sn[0];

			    $fd=fopen ("$file", "r");
			    $b=fgets($fd, 4096);
			    $c=fgets($fd, 4096);
			    fclose ($fd);
				if (substr($b,0,4)=="<?//") {$scn=preg_replace("/\s*$/","",substr($b,4));} else {$scn=$fn;}
			    $r=query("INSERT INTO $registry(script,name) VALUES('$fn','$scn')",$db);
			}
		}
	}


	switch ($go) {
	 	case "store_access": {
			$r=query("SELECT * FROM $registry",$db);
			if ($id) {
				for ($i=0;$i<mysql_numrows($r);$i++) {
					$sn=mysql_result($r,$i,"script");
					$sub=mysql_result($r,$i,"sub");
					$n=$sn."N$sub";
					$n=$$n;
					$rr=query("DELETE FROM $priv WHERE userid=$id AND script LIKE '$sn' AND sub LIKE '$sub'",$db);
					if ($n) $rr=query("INSERT INTO $priv(userid,script,sub,permission) VALUES('$id','$sn','$sub','$n')",$db);
//					echo "$sn:$sub - $n<br>";
				}
				error("Запись обновлена!");
			}
			break;
	 	}
	 	case "access": {
	 		if ($id) {
	 			$r=query("SELECT * FROM $priv WHERE userid=$id ORDER BY script ASC, sub ASC",$db);
	 		    if (mysql_numrows($r)) for ($i=0;$i<mysql_numrows($r);$i++) {
	 		    	$perm[mysql_result($r,$i,"script").".".mysql_result($r,$i,"sub")]=mysql_result($r,$i,"permission");
	 		    }

	 		    $r=query("SELECT * FROM $registry",$db);
	 		    $ns=array();
	 		    if (mysql_numrows($r)) for ($i=0;$i<mysql_numrows($r);$i++) $ns[mysql_result($r,$i,"script").".".mysql_result($r,$i,"sub")]=mysql_result($r,$i,"name");
	 			
				$i=0;
				ksort($ns);

	 			if (mysql_numrows($r)) {
	 			    echo "<form action=$ps method=GET><input type=hidden name=code value='$code'><input type=hidden name=go value='store_access'><input type=hidden name=id value='$id'>";
	 			    echo "<b>Привелегии пользователя ".$fe["login"].":</b><br>";
 				    echo "<table width=100% cellpadding=2 cellspacing=1><tr><th width=100% colspan=2>Название раздела</th><th>Уровень&nbsp;доступа</th></tr>";
	 				while (list($k,$name)=each($ns)) {
	 				    $aa=split("\.",$k);
	 					$sn=$aa[0];
	 					$sub=$aa[1];
	 					$level=$perm["$k"];

	 			    	echo "<tr id=small bgcolor=";
	 				    if ($sub) echo "d0d0d0"; else echo "c0c0c0";
 				    	echo "><td";

 				    	if ($sub) echo " bgcolor=e0e0e0>&nbsp;</td><td id=small width=100%><b>$k</b> / $name";
 				    	else echo " colspan=2><b>$name</b> / $sn";

 				    	echo "</td><td><select name=$sn"."N$sub id=small>";
 				    	for ($j=0;$j<sizeof($rs);$j++) {
 				    		echo "<option value='$j'";
 				    		if ($j==$level) echo " selected";
 				    		echo ">$rs[$j]\n";
 				    	}
 				    	echo "</select></td></tr>\n";
	 				}

	 				echo "</table>";
	 				echo "<input type=submit value=' Готово ' id=nform></form>";
	 			} else error("Пользователь не найден!");
	 		}
			break;
	 	}
	 	case "refine": {
	 	    alter("");
			break;
	 	}
	 	case "install": {
	 		if (!$pass || !table_exists($table,$db_name)) {
			    $r=query("CREATE TABLE $table (id int(11) NOT NULL auto_increment,
 login VARCHAR(20) NOT NULL,
 pass VARCHAR(20) NOT NULL,
 name VARCHAR(50) NOT NULL,
 email VARCHAR(30) NOT NULL,
 code VARCHAR(20),
 pong INT(12),
 ip VARCHAR(30),
 PRIMARY KEY (id))",$db);
			    $r=query("CREATE TABLE $priv (id int(11) NOT NULL auto_increment,
 userid int(11) NOT NULL,
 script VARCHAR(20) NOT NULL,
 sub VARCHAR(50) NOT NULL,
 permission SMALLINT(1),
 PRIMARY KEY (id))",$db);
	 			error("Таблицы созданы!");

	 			$r=query("INSERT INTO $table(login,pass,name) VALUES ('admin','password','Администратор')",$db);
	 			error("Создан пользователь с логином 'admin' и паролем 'password' (данные рекомендуется изменить)!");

	 			$r=query("INSERT INTO $priv(userid,script,sub,permission) VALUES ('1','_admin','',2)",$db);
                alter("admin");
	 		} else {
	 			error("Таблица уже существует!");
	 		}
			$pass=1;
	 		break;
	 	}
	 	case "uninstall": {
			if ($pass) {
				$r=query("DROP TABLE $table",$db);
				$r=query("DROP TABLE $priv",$db);
				error("Таблица уничтожена!");
				$pass=0;
			}
	 		break;
	 	}
	 	case "del": {
			if ($id) {
				$res=query("DELETE FROM $table WHERE id=$id",$db);
				error("Запись успешно удалена!");
			}
	  		break;
	  	}
		case "insert": {
			$err="";                                                                          

		    if (ereg("\'",$login) || ereg("\'",$passwd)) $err.="Логин и пароль не должны содержать специальных символов!";
			$r=query("SELECT * FROM $table WHERE LOWER(login)=LOWER('$login')",$db);
			if (mysql_numrows($r) && !$id) $err.="Пользователь <u>$login</u> уже зарегистрирован в системе!<br>";
			if (!$name) $err.="Вы не ввели своё имя!<br>";
			if (strlen($passwd)<5 && $passwd) $err.="Пароль должен быть не короче 5 символов!<br>";
			if ($passwd!=$confpasswd && $passwd) $err.="Введённые пароли не совпадают!<br>";

			if ($err) {
				error("Ошибка:<br>$err<br>");
				$go="add";
			} else {
				$dat=date("Y-m-d H:i:s");
//		        $vl=$HTTP_SERVER_VARS["REMOTE_ADDR"];
		        $vl="";
//				$host=gethostbyaddr($vl);

				if (!$id) {
					$res=query("INSERT INTO $table(login,pass,name,email,ip) VALUES ('$login','$passwd','$name','$email','$vl')",$db);
					error("Пользователь добавлен!");
				} else {
				    if ($passwd) $ad="pass='$passwd',"; else $ad="";
					$res=query("UPDATE $table SET login='$login',$ad name='$name',email='$email' WHERE id=$id",$db);
					error("Запись успешно изменена!");
				}
			}
			break;
		}
		case "adduser": {
			if ($id) {
			    echo "<b>Редактирование данных:</b><br>";
				$res=query("SELECT * FROM $table WHERE id=$id",$db);
				$id=mysql_result($res,0,"id");
				$login=mysql_result($res,0,"login");
				$name=mysql_result($res,0,"name");
				$email=mysql_result($res,0,"email");
				$pong=mysql_result($res,0,"pong");
			} else {echo "<b>Новый пользователь:</b><br>";}
	
				echo "<form action=$ps method=POST><input type=hidden name=go value=insert>
<input type=hidden name=id value='$id'>
<input type=hidden name=from value='$from'>
<input type=hidden name=code value='$code'>
Логин<br>
<input name=login size=50 value='$login' id=nform><br>
Пароль<br>
<input name=passwd size=50 value='' id=nform type=password><br>
Потверждение пароля<br>
<input name=confpasswd size=50 value='' id=nform type=password><br>

Имя<br>
<input name=name size=50 value='$name' id=nform><br>

e-mail адрес<br>
<input name=email size=50 value='$email' id=nform><br>
<input type=submit value=' Готово ' id=nform> <input type=reset value=' Сброс ' id=nform></form>";
			break;
		}
	}

	if ($pass && $global_access==2) {
		echo "<b>Зарегистрированные пользователи:</b><br>";
		$res=query("SELECT * FROM $table ORDER BY id DESC",$db);
		$n=mysql_numrows($res);
		if (!$n) {error("Нет записей!");
		} else {
			$f=round($from);
			$t=$from+$show_on_page;
			if ($n<$t) $t=$n;

			echo "<table width=100% cellpadding=3 cellspacing=1>
<tr><th>%/#</th><th>Логин</th><th width=100%>Имя</th><th>ip/host</th><th>Статус</th></tr>";
			for ($i=$f;$i<$t;$i++) {
				$id=mysql_result($res,$i,"id");
				$login=mysql_result($res,$i,"login");
				$name=mysql_result($res,$i,"name");
				$email=mysql_result($res,$i,"email");
				$c=mysql_result($res,$i,"code");
				$pong=mysql_result($res,$i,"pong");
				$ip=mysql_result($res,$i,"ip");
				if (!$pong || time()-$pong>$expired) {$st="<div id=del>Off-line</div>";} else {$st="<div id=ed>On-line</div>";}

				echo "<tr bgcolor=d0d0d0><td><nobr><a href=$ps?go=access&id=$id&from=$from&code=$code id=add>Access</a> 
<a href=$ps?go=edit&id=$id&from=$from&code=$code id=ed>Edit</a> 
<a href=$ps?go=del&id=$id&from=$from&code=$code id=del>Del</a></nobr></td>
<td id=small>$login</td><td id=small><b>$name</b></td><td id=small>$ip</td><td>$st</td></tr>";
			}
			echo "</table>";
		}
	}
	
	foot();
?>