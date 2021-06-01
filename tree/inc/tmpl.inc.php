<?
class temp
{
 var $filelist = array(), $ties = array();
 var $output, $delimiters;
 
 function temp($list = "", $path = "./") 
 {
  if(!is_dir($path = preg_replace ("/([^\/])$/", "\\1/", $path))) $this->kill("temp");
  if(!is_array($list) || empty($list)) $this->kill("temp");
  else
  {
   while(list($desc, $filename) = each($list)) 
   {
   	if(!(empty($desc)) && (file_exists($path.$filename))) $this->filelist["$desc"] = $path.$filename;
    else $this->kill("temp");	
   }
  }
 }
 
 function delimit($d1 = "#", $d2 = "#") 
 {
  $this->delimiters[0] = preg_quote($d1);
  $this->delimiters[1] = preg_quote($d2);
 }
 
 function tie($arr = "", $val = "") 
 {
  if(empty($arr)) $this->kill("tie");
  if(!is_array($arr)) $this->ties["$arr"] = $val;
  else
   while(list($key, $val) = each ($arr))
    if(!empty($key)) $this->ties["$key"] = $val;
 }
 
 function parse($descfile = "", $temp = "") 
 {
  if(empty($descfile)) $this->killme("parse");
  $f = (!empty($this->filelist["$descfile"])) ? file($this->filelist["$descfile"]) : $descfile;
  if(!empty($temp)) $this->ties["$temp"] .= $this->get($f);
  else $this->output .= $this->get($f);
 }
 
 function get($data = "") 
 {
  if(empty($data) && !(is_array($data))) $this->kill("get");

  if (is_array($data)) $data = implode("", $data);
  else $data="";

  reset($this->ties);
  while (list($key, $val) = each($this->ties)) 
  {
   $data = preg_replace("/".$this->delimiters[0]."\s*".preg_quote($key)."\s*".$this->delimiters[1]."/", $val, $data);
  }
   return $data;
 }
 
 function _temp() 
 {
  unset($this->output,$this->filelist,$this->ties);
 }
 
 function out($t = "")
 {
  echo (empty($t)? $this->output :  $this->ties["$t"]); 
 }
 
 function kill($msg = "")
 {
  die((empty($msg)?"killed":"killed by ".$msg));
 }
}
?>