<?

//------------------------------------------------------------------------
// Auth change 2.0
//		Changing user authintification status / personal data
//
//		Last updated: 23.XII.2002
//		Author: Nick S. Bogdanov <nick@sgu.ru>
//
//------------------------------------------------------------------------

    
    if (ereg("\'",$new_login) || ereg("\'",$ch_pass)) {
    	error("Логин и пароль не должны содержать специальных символов!");
    	$go="edit";
    }
	switch ($auth) {
		case "logout": {
			$let=0;
			$t=0;
			$r=query("UPDATE $admin_table SET code='',pong='',ip='' WHERE code='$code'",$db);
			$code="";

			add_log("Выход из системы! ($my_login, $ip)","",2);

			break;
		}
		case "edit": {
			head("Система администрирования",$my_name,"$ps?code=$code");
			echo "<ul id=smallul><li> <a href=$ps?code=$code id=ml>Назад</a></ul>";
			middle("");
			echo "<b>Изменение регистрационых данных:</b><br>";
			echo "<form action=$ps method=GET>
<input type=hidden name=ch_id value='$my_id'>
<input type=hidden name=code value='$code'>
<input type=hidden name=auth value=store>
Логин:<br>
<input name=new_login value='$my_login' size=50 id=nform><br>
Пароль:<br>
<input name=ch_pass value='' size=50 type=password id=nform><br>
Подтвержение пароля:<br>
<input name=ch_cpass value='' size=50 type=password id=nform><br>
E-mail адрес:<br>
<input name=ch_email value='$my_mail' size=50 id=nform><br>
Имя:<br>
<input name=ch_name value='$my_name' size=50 id=nform><br>
<input type=submit value=' Изменить ' id=nform>
</form>";
			foot();
			exit;
		}
		case "store": {
			$err="";
			if (!$new_login) $err.="Не указан логин!<br>";
			if ($ch_pass && strlen($ch_pass)<6) $err.="Пароль должен быть не короче 6 символов!<br>";
			if ($ch_pass && $ch_pass!=$ch_cpass) $err.="Введённые пароли не совпадают!<br>";
			if ($err) {
				head("Система администрирования",$my_name,"$ps?code=$code");
				error($err);
				error("Данные не были изменены!");
				echo "<br>";
				foot();
				exit;
			} else if ($ch_id) {
			    if ($ch_pass) $ad="pass='$ch_pass',"; else $ad="";
				$r=query("UPDATE $admin_table SET login='$new_login',$ad name='$ch_name',email='$ch_email' WHERE id=$ch_id",$db);
				add_log("Изменение персональных данных! ($my_login, $ip)","",1);
			}
		}
	}
?>