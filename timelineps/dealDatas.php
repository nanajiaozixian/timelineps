<?php

/******
**作者： Doris
**时间： 2013-8-14
**作用： timeline数据处理
******/

require_once("wrMongodb.php");


define('VERSIONS', 'versions');//保存所有版文件的文件夹名字
define('BROWSER_SEPARATOR', '/');

$version_template = "pages".BROWSER_SEPARATOR;
global $localfilepath;

newMongoClient();//开启mongo
if(isset($_POST['pageurl'])){
	$pageURL = $_POST['pageurl'];
	$parts = parse_url($pageURL);//解析url
	$host = $parts['host'];//获取hostname
	$main_file_init = basename($parts['path']);//获取pathname
	if($main_file_init===""){
		$main_file_init = "index.html";
	}
	if(substr($main_file_init,-5)!==".html"){
		$main_file_init = $main_file_init.".html";
	}
	$folder_name = preg_replace("/(\w+)\.(\w+)\.(\w+)/i", "$3.$2.$1", $host);
	$folder_name = substr($main_file_init,0,-5).".".$folder_name;
	//echo "folder_name: $folder_name<br/>";
	getWebPageInfor($folder_name);
	
}




/***************************************************************各种类*********************************************************************/

/**
**类名： WebPageInfor
**代表网页的各种信息和操作
**/
class WebPageInfor{
	public $max_version=0;
	public $min_version=0;
	public $local_file_name;
	public $files_path = array();

}
$myWebPage = new WebPageInfor();
$myWebPage->files_path = array('v0'=>'pages/cn.com.adobe.www/versions/v0/cn_local.html',
																'v1'=>'pages/cn.com.adobe.www/versions/v1/cn_local.html',
																'v2'=>'pages/cn.com.adobe.www/versions/v2/cn_local.html',
																'v3'=>'pages/cn.com.adobe.www/versions/v3/cn_local.html');
$myWebPage->max_version = 3;
$myWebPage->local_file_name ="cn_local.html";

//sentJSON($myWebPage);

/**************************************************************各种方法*********************************************************************/
/**
**函数名： getWebPageInfor
**作用： 获取网页的各种信息
**var url 网页的链接
**/
function getWebPageInfor($pagehostname){
	//从mongodb获取网页的各种信息
	//echo "pagehostname: $pagehostname";
	$index = getMyPageCollect($pagehostname);
	/*if($index === false){
		$index = array();
	}*/
	sentJSON($index);
	
}


function sentJSON($page){	
	$json_string = json_encode($page);
	echo $json_string;
		
}




?>