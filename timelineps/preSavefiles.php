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

$str_file = file_get_contents($url);
$main_file  = "source.html";
$copy_file  = "copy.html";//所有链接都是绝对路径的临时的html文件
file_put_contents($main_file, $str_file);

$str_copy = $str_file;
$str_new = relative_to_absolute($str_copy, $url);
file_put_contents($copy_file, $str_new);
echo $copy_file; 
?>