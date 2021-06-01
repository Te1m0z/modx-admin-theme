<?
require_once "basic.class.php";

//------------------------------------------------------------------------
// SQL class 1.3
//		Managing MySQL operations
//
//		Last updated: 20.IV.2003
//		Author: Nick S. Bogdanov <nick@sgu.ru>
//
//------------------------------------------------------------------------

class Query extends Basic{

	var $Q;

	var $Queried=0;
	var $Record;
	var $RecordIndex=0;

	var $Debugging=0; 		// Debugging mode

	var $DB_used=0;
	
	function Query($query,$DB=0) {
		//print 1;
		if (!$DB) {
			if (!$this->DB_used) {
				$this->AddError("Нет соединения с MySQL сервером!");
				return 0;
			} else $DB=$this->DB_used;
		} else $this->DB_used=$DB;
		
		$this->Q=@mysql_query($query,$DB);

		if ($this->Debugging) $this->AddError("Query: <b>$query</b>");

		if ($this->Q) $this->Queried=1; else $this->AddError("Ошибка в запросе: <strong>$q</strong>!");
	}

	function NumRows() {
		if (!$this->Queried) {
			$this->AddError("Не определены результаты запроса!");
			return 0;
		}
		return @mysql_numrows($this->Q);
	}

	function NextResult() {
		if (!$this->Queried) {
			$this->AddError("Не определены результаты запроса!");
			return 0;
		}
		
    	$this->Record = @mysql_fetch_array($this->Q);
	    $this->RecordIndex++;
	    if ($this->RecordIndex-1>=$this->NumRows() || !$this->NumRows()) {
    		mysql_data_seek ($this->Q, 0);
    		return 0;
	    } else return 1;
	}
	function Seek($offset) {
		if (!$this->Queried) {
			$this->AddError("Не определены результаты запроса!");
			return 0;
		}
		
		if ($offset>$this->NumRows()) $offset=$this->NumRows();
    	@mysql_data_seek ($this->Q, $offset);
	}

	function Get($name) {
		return $this->Record[$name];
	}
}




class SQL extends Basic {
    var $DB;
    
    var $DB_name;
	var $Host;
	var $User;
	var $UserPassword;

	var $Connected=0;

	var $Errors=array();
	var $ErrorsIndex=0;


	function SQL($db_name,$host,$user,$pass) {
		$this->DB_name=$db_name;
		$this->Host=$host;
		$this->User=$user;
		$this->UserPassword=$pass;

		$this->DB = @mysql_connect($this->Host, $this->User, $this->UserPassword);
		if (!$this->DB) $this->AddError("Ошибка подключения к MySQL серверу!");

		if (@mysql_select_db($this->DB_name, $this->DB)) {
                   $this->Connected=1; 
                   mysql_query("set names 'cp1251'", $this->DB);
                } else $this->AddError("Невозможно выбрать базу данных <b>$db_name</b>!");
	}


	function Query($query) {
		if (!$query) return 0;
//		if (!$this->Connected) return 0;

		$a=new Query($query,$this->DB);
		return $a;
	}
}


?>
