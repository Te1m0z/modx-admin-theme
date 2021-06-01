<?

//------------------------------------------------------------------------
// Basic class
//		Contained main classes functions
//		All classes extend this
//
//		Last updated: 17.I.2003
//		Author: Nick S. Bogdanov <nick@sgu.ru>
//
//------------------------------------------------------------------------

class Basic {
    var $Errors=array();
    var $ErrMsg="Ошибки:";
    var	$Index=0;
	
	var $Styles=array(
"errmsg"=>"color:red; font-weight:bold",
"errors"=>"font-size:8pt; color:red"
	);


	
	function InsertStyle($name) {
		$style=$this->Styles[$name];
		if (!$style) return;

		if (!ereg("^style=",$style) && !ereg("id=",$style) && !ereg("class=",$style)) return "style='".$style."'"; else return $style;
	}

	function SetStyle($element,$style) {
		$this->Styles[$element]=$style;
	}


	function Errors() {
		return $this->Index;
	}

	function AddError($error) {
		$this->Errors[$this->Index++]=$error;

		echo "<div>[!] <b>$error</b></div>";
	}
	
	function PrintErrors($ErrM=0) {
		if (!$ErrM) $ErrM=$this->ErrMsg;
		if (!$this->Index) return 0;

		echo "<ul type=square ".$this->InsertStyle("errors")."><div ".$this->InsertStyle("errmsg").">".$ErrM."</div>\n";
		for ($i=0;$i<$this->Index;$i++) echo "	<li> ".$this->Errors[$i]."\n";
		echo "</ul>\n";
		return 1;
	}

}

?>