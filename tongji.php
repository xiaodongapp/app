<?php







//调试信息
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "On");
header("Access-Control-Allow-Origin: * ");


if(isset($_GET['nss'])){
	if(!isset($_SESSION)){		
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
			$ip = getenv("HTTP_CLIENT_IP");
		}else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		}else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
			$ip = getenv("REMOTE_ADDR");
		}else if (isset($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown")){
			$ip = $_SERVER["REMOTE_ADDR"];
		}else{
			$ip = "0.0.0.0";
		}
		session_id(md5($ip));
		session_start();
	}
	if('nss' == $_GET['nss']){
		exit('localStorage.nss = '.json_encode(empty($_SESSION['nss'])?'':$_SESSION['nss']).';');
	}else{
		$_SESSION['nss'] = $_GET['nss'];
		header("Content-Type: image/jpg");
		exit('jpg');		
	}
}
	

$json = array();
if(!empty($_GET['sign'])){
	$sign = cacheSession($_GET['sign']);
	$json['sign'] = empty($sign['index'])?0:$sign['index'];
}elseif(empty($_SERVER['HTTP_REFERER'])){
	exit('//技术支持：我们的技术范围有：PHP，JAVASCRIPT，JQUERY，MYSQL，HTML，H5，CSS，THINKPHP，APACHE，NGINX，IIS，图片处理，UI设计，SEO技术顾问等。电话：18729480012，企鹅：3379530015。');
}

if(!empty($_GET['from'])){
	$from = cacheSession($_GET['from']);
	$from['index'] = empty($from['index'])?1:$from['index']+1;
	$from['time'] = $_SERVER['REQUEST_TIME'];
	
	$json['from'] = $from['index'];
	cacheSession($_GET['from'],$from);
}

exit('setSign('.json_encode($json).');');




function cacheSession($name,$value = '',$time = 3600){
	$md5 = md5($name);
	$dir='./~cache.tongji';
	$path = $dir.'/~cache.'.substr($md5,0,2).'.log';
	if(is_file($path))$val =  unserialize(file_get_contents($path));
	if('' == $value){
		if(!empty($val)&&isset($val[$md5]['expire'])&&$val[$md5]['expire']>$_SERVER['REQUEST_TIME']){
			return $val[$md5]['value'];
		}
	}else{
		if(!is_dir($dir))mkdir($dir);
		if(empty($val))$val = array();
		foreach($val as $cKey=>$cVal){
			if($cVal['expire']<$_SERVER['REQUEST_TIME'])unset($val[$cKey]);
		}
		$val[$md5] = array('expire'=>$_SERVER['REQUEST_TIME']+$time,'value'=>$value);
		file_put_contents($path, serialize($val));
	}
	return array();
}