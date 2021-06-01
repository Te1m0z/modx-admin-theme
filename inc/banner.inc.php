<?
function getStat(){
  return file_get_contents('http://sgu.ru/exchange/index.php?stat1=1');
}

function getGA($site){
  return file_get_contents('http://sgu.ru/exchange/index.php?stat='.$site);
}

function getBanner(){
  return file_get_contents('http://sgu.ru/exchange/index.php?banner=1');
}

function getData($site){
	$fName = dirname(__FILE__)."/"."cache.".$site.".txt";
	$mtime = @filemtime($fName);
	$d = 24 * 60 * 60;
	if (!$mtime || $mtime < (time() - $d)){
		$data['stat'] = getStat();
		$data['ga'] = getGA($site);
		$data['banner'] = getBanner();
		file_put_contents($fName, serialize($data));
		return $data;
	}
	return unserialize(file_get_contents($fName));
}

function getHtml($site = ""){
	if (!$site){
		$site= $_SERVER['HTTP_HOST'];
	}
	$data = getData($site);
	$s = $data['stat'];
	$s .= $data['ga'];
	$s .= $data['banner'];
	return $s;
}