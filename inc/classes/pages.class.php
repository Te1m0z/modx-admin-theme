<?

//------------------------------------------------------------------------
// PAGES class 1.2
//		Simple pages listing class (1|2|3|4|>>)
//
//		Last updated: 20.IV.2003
//		Author: Nick S. Bogdanov <nick@sgu.ru>
//
//------------------------------------------------------------------------

require_once "basic.class.php";

class Pages extends Basic {
	var $Total=0;
	var $Current=0;
	var $PerPage=10;

	var $ShowRange=5;

	var $PagesText="Pages:";
	var $Divider=" | ";
	var $PrevText="&laquo;";
	var $NextText="&raquo;";

	var $ScriptName="./";
	var $Parameter="from";
	var $AddParams="";

	var $Styles=array(
"title"=>"color:606060",
"dividers"=>"color:E0E0E0",
"current"=>"font-weight:bold",
"link"=>"color:808080;"
	);

	function Pages($total,$perpage=10,$current=0) {
		$this->Total=$total;
		$this->PerPage=$perpage;
		$this->Current=$current;
	}

	function SetScript($script="./") {
		$this->ScriptName=$script;
	}

	function SetShownPages($pages=5) {
		$this->ShowRange=$pages;
	}

	function SetAddParams($params) {
		$this->AddParams=$params;
	}

	function SetParameterName($name="from") {
		$this->Parameter=$name;
	}

	function SetText($text) {
		$this->PagesText=$text;
	}

	function SetDivider($text) {
		$this->Divider=$text;
	}

	function SetPrevNext($prev,$next) {
		$this->PrevText=$prev;
		$this->NextText=$next;
	}

	function MakeLink($from,$text) {
		echo "<a href='".$this->ScriptName."?".$this->Parameter."=$from";
		if ($this->AddParams) echo "&".$this->AddParams;
		echo "' ".$this->InsertStyle("link").">".$text."</a>";
	}
	
	function PrintPages() {
		$pages=ceil($this->Total/$this->PerPage);
		$page=round($this->Current/$this->PerPage);

		echo "<div ".$this->InsertStyle("title").">".$this->PagesText." (".($page+1)."/".$pages.")</div>";
		echo "<span ".$this->InsertStyle("dividers").">";

		if ($this->Current) $this->MakeLink($this->Current-$this->PerPage,$this->PrevText);
		else echo "<span ".$this->InsertStyle("link").">".$this->PrevText."</span>";
		echo $this->Divider;

		$fr=0;
		$to=$pages;

		if ($pages>$this->ShowRange) {
			$left_half=floor($this->ShowRange/2);
			$right_half=ceil($this->ShowRange/2);

			$fr=$page-$left_half;
			$to=$page+$right_half;

			if ($fr<0) {
				$fr=0;
				$to=$this->ShowRange;
			}
			if ($to>$pages) {
				$to=$pages;
				$fr=$pages-$this->ShowRange;
			}
		}

		for ($i=$fr;$i<$to;$i++) {
		    $j=$i+1;
			$from=$i*$this->PerPage;
			if ($i!=$fr) echo $this->Divider;
			
			if ($from==$this->Current) echo "<span ".$this->InsertStyle("current").">".$j."</span>";
			else $this->MakeLink($from,$j);
		}

		echo $this->Divider;
		if (($this->Current+$this->PerPage)<$this->Total) $this->MakeLink($this->Current+$this->PerPage,$this->NextText);
		else echo "<span ".$this->InsertStyle("link").">".$this->NextText."</span>";
		echo "</span>";


	}
}

?>