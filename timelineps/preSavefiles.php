<?php
include_once("global.php");

$url = "";
//获取网页url
if(isset($_POST['pageurl_d'])){
	$url = $_POST['pageurl_d'];
}else{
	$url = "";
}

//判断url的有效性
if(get_headers($url)===false){
	return;
}
$parts = parse_url($url);//解析url
$host = $parts['host'];//获取hostname

$str_file = EnCodeForGoogleHK($url);

$main_file  = "source.html";
$copy_file  = "copy.html";//所有链接都是绝对路径的临时的html文件
file_put_contents($main_file, $str_file);
$str_copy = $str_file;
$str_new = relative_to_absolute($str_copy, $url);
file_put_contents($copy_file, $str_new);
echo $copy_file; 

//因为google.hk页面的编码方式是CP936，需要特殊处理，如果有更好方法可以改进这个函数
function EnCodeForGoogleHK($url){
	$content = file_get_contents($url);
	$list = array("ASCII","UTF-8","GB2312","GBK","BIG5");
  $encode =  mb_detect_encoding($content,$list);
  if($encode=="CP936"){
  	header('Content-Type: text/html; charset=big5');
  	$contentnew = file_get_contents($url);
  	$contentnew = str_replace('<head>','<head><meta http-equiv="content-type" content="text/html; charset=big5">',$contentnew);
  	return $contentnew;
  }else{
  	return $content;
  }
}
?>