<?

//------------------------------------------------------------------------
// FORM class 2.3
//		Managing HTML forms & elements
//
//		Last updated: 22.IV.2003
//		Author: Nick S. Bogdanov <nick@sgu.ru>
//
//------------------------------------------------------------------------


require_once "basic.class.php";

class Form extends Basic {
    var $Name;
    var $Method;
    var $Action;
    var $Enctype;

    var $Content = array();
    var $Index=0;
	
	var $DefaultStyle="elements";
	var $Styles=array(
"elements"=>"font-family:verdana,arial;font-size:8pt;background-color:white;color:202020; border-bottom: 1px solid; border-left: 1px solid; border-right: 1px solid; border-top: 1px solid",
	);

	var $isJSLoaded=0;
	var $Common=array();
	var $Temp=array();

	function CommonValues($params) {
		$this->Common=$params;
	}
	
	function Form($params=array()) {
		$this->CommonValues($params);
	}

	function AddStyle($style) {
		$s=$this->InsertStyle($style);
		if (!$s) return $this->InsertStyle($this->DefaultStyle); else return $s;
	}

	function AddHTML($html) {
		$this->Content[$this->Index++]=$html;
	}

	function Get($name,$Arr=array(),$new=0) {
		if ($new) $this->Temp=array_merge($this->Common,$Arr);
		$a=$this->Temp[$name];
		return $a;
	}
	
	function Select($values,$params=array()) {
		$tmp=$this->Get("",$params,1);
		$choosed=$this->Get("choosed");

	    $a="";
	    while (list($k,$v)=each($values)) {
	    	$a.="<option value='$v'";
	    	if ($choosed && $choosed==$v) $a.=" selected";
	    	$a.=">$k\n";
	    }
		$this->AddHTML($this->Get("title")."<select name='".$this->Get("name")."' ".$this->AddStyle("list").">".$a."</select>".$this->Get("after"));
	}

	function Hidden($params=array()) {
		$tmp=$this->Get("",$params,1);
		$this->AddHTML($this->Get("title")."<input type=hidden name='".$this->Get("name")."' value=\"".htmlspecialchars($this->Get("value"))."\">");
	}

	function Text($params=array()) {
		$tmp=$this->Get("",$params,1);
		$this->AddHTML($this->Get("title")."<input type=text name='".$this->Get("name")."' size='".$this->Get("size")."' maxlength='".$this->Get("maxlentgh")."' value=\"".htmlspecialchars($this->Get("value"))."\" ".$this->AddStyle("textfield")." ".$this->Get("events").">".$this->Get("after"));
	}

	function Password($params=array()) {
		$tmp=$this->Get("",$params,1);
		$this->AddHTML($this->Get("title")."<input type=password name='".$this->Get("name")."' size='".$this->Get("size")."' maxlength='".$this->Get("maxlentgh")."' value=\"".htmlspecialchars($this->Get("value"))."\" ".$this->AddStyle("password").">".$this->Get("after"));
	}

	function File($params=array()) {
		$tmp=$this->Get("",$params,1);
		$this->AddHTML($this->Get("title")."<input type=file name='".$this->Get("name")."' size='".$this->Get("size")."' maxlength='".$this->Get("maxlentgh")."' value=\"".htmlspecialchars($this->Get("value"))."\" ".$this->Get("events")." ".$this->AddStyle("file").">".$this->Get("after"));
		$this->Common["method"]="POST";
		$this->Common["enctype"]="multipart/form-data";
	}

	function Radio($params=array()) {
		$tmp=$this->Get("",$params,1);
		$state=$this->Get("state");
		$value=$this->Get("value");

	    if ($state && ($state==$value || $state=="checked")) $s=" checked"; else $s="";

		$this->AddHTML("<input type=radio name='".$this->Get("name")."' value=\"".htmlspecialchars($value)."\"$s>".$this->Get("title").$this->Get("after"));
	}

	function CheckBox($params=array()) {
		$tmp=$this->Get("",$params,1);
		$state=$this->Get("state");

	    if ($state) $s=" checked"; else $s="";

		$this->AddHTML("<input type=checkbox name='".$this->Get("name")."' value=\"".htmlspecialchars($this->Get("value"))."\"$s>".$this->Get("title").$this->Get("after"));
	}

	function TextArea($params=array()) {
		$tmp=$this->Get("",$params,1);
		$this->AddHTML($this->Get("title")."<textarea name='".$this->Get("name")."' cols='".$this->Get("cols")."' rows='".$this->Get("rows")."' ".$this->AddStyle("textarea").">".htmlspecialchars($this->Get("value"))."</textarea>".$this->Get("after"));
	}

	function SubmitReset($params=array()) {
		$tmp=$this->Get("",$params,1);
		$a="";
		if ($this->Get("submittitle")) $a.="<input type=submit name='".$this->Get("submitname")."' value='".$this->Get("submittitle")."' ".$this->AddStyle("submit")."> ";
		if ($this->Get("resettitle")) $a.="<input type=reset name='".$this->Get("resetname")."' value='".$this->Get("resettitle")."' ".$this->AddStyle("reset").">".$this->Get("after");
		$this->AddHTML($a);
	}
	
	function ExtendedTextArea($params=array()) {
		$tmp=$this->Get("",$params,1);
		$name=$this->Get("name");
		$html=$this->Get("html");
		$tabquotes=$this->Get("tabquotes");

	    if (!$name) return 0;

		if (($html || $tabquotes) && !$this->isJSLoaded) {
			$this->isJSLoaded=1;
			$this->AddHTML("<script>

var formname='".$this->Get("formname")."';
var bigStart=0, d=document.edit;

function add_sub_old(sub) {
	d.content.value+=sub;
	d.content.focus();
}

function storeCaret(text,where) { 
	if (text.createTextRange) {
		text.caretPos = document.selection.createRange().duplicate();
		text.Selected = document.selection.createRange().text;
		text.elName=where;
		
		window.status=text.Selected;
//		window.status=text.Selected+' : '+where;

//		bigStart=text.caretPos;
	}
}

function add_sub(name,pre,post) {
    if (!((navigator.appName == \"Netscape\" && navigator.appVersion.charAt(0) >= 4) || (navigator.appName == \"Microsoft Internet Explorer\" && navigator.appVersion.charAt(0) >= 4) || (navigator.appName == \"Opera\" && navigator.appVersion.charAt(0) >= 4))) return;

	d=document.forms[formname];
	dd=d.elements[name];
	if (dd.createTextRange && dd.caretPos) {      

		var caretPos = dd.caretPos;
		var Selected = dd.Selected;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? pre + Selected + post + ' ' : pre + Selected + post;
	}
	else dd.value += pre+post;  
	dd.focus();
}</script>\n");

		}
		$this->AddHTML($this->Get("title")."<textarea name='$name' cols='".$this->Get("cols")."' rows='".$this->Get("rows")."' ".$this->AddStyle("textarea")." onSelect=\"javascript:storeCaret(this,'$name');\" onClick=\"javascript:storeCaret(this,'$name');\" onKeyUp=\"javascript:storeCaret(this,'$name');\" onChange=\"javascript:storeCaret(this,'$name');\">".htmlspecialchars($this->Get("value"))."</textarea><br>");
//		$this->AddHTML(($title ? "$title<br>" : "")."<textarea name='$name' $ads ".$this->AddStyle("textarea")."  onSelect=\"javascript:storeCaret(this,'$name');\" onClick=\"javascript:storeCaret(this,'$name');\" onKeyUp=\"javascript:storeCaret(this,'$name');\" onChange=\"javascript:storeCaret(this,'$name');\">".htmlspecialchars($value)."</textarea><br>\n");

		if ($html) {
			$a = "<input type=button value=' BR ' onClick=\"add_sub('$name','\\n\\<br\\>','');\" ".$this->AddStyle("button")."> ";
			$a.= "<input type=button style='font-weight:bold' value=' B ' onClick=\"add_sub('$name','\\<strong\\>','\\</strong\\>');\" ".$this->AddStyle("button")."> ";
			$a.= "<input type=button style='font-style:italic' value=' I ' onClick=\"add_sub('$name','\\<em\\>','\\</em\\>');\" ".$this->AddStyle("button")."> ";
			$a.= "<input type=button style='text-decoration:underline' value=' U ' onClick=\"add_sub('$name','\\<u\\>','\\</u\\>');\" ".$this->AddStyle("button").">&nbsp;&nbsp;";
			$this->AddHTML($a);
		}

		if ($tabquotes) {
			$a = "<input type=button value=' Tab ' onClick=add_sub('$name','\\t',''); ".$this->AddStyle("button")."> ";
			$a.= "<input type=button value=' &laquo;&raquo; ' onClick=\"add_sub('$name','&laquo;','&raquo;');\" ".$this->AddStyle("button")."> ";
			$this->AddHTML($a);
		}

		$this->AddHTML($this->Get("after"));
	}


	
	
	function PrintForm() {
		echo "<form name='".$this->Get("formname")."' method='".$this->Get("method")."' action='".$this->Get("action")."' enctype='".$this->Get("enctype")."'>\n";

		for ($i=0;$i<sizeof($this->Content);$i++) echo $this->Content[$i]."\n";

		echo "</form>";
	}
}

?>