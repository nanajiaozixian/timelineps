<?php

/******
**���ߣ� Doris
**ʱ�䣺 2013-8-14
**���ã� timeline���ݴ���
******/

require_once("wrMongodb.php");


define('VERSIONS', 'versions');//�������а��ļ����ļ�������
define('BROWSER_SEPARATOR', '/');

$version_template = "pages".BROWSER_SEPARATOR;
global $localfilepath;

newMongoClient();//����mongo
if(isset($_POST['pageurl'])){
	$pageURL = $_POST['pageurl'];
	$parts = parse_url($pageURL);//����url
	$host = $parts['host'];//��ȡhostname
	$main_file_init = basename($parts['path']);//��ȡpathname
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




/***************************************************************������*********************************************************************/

/**
**������ WebPageInfor
**������ҳ�ĸ�����Ϣ�Ͳ���
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

/**************************************************************���ַ���*********************************************************************/
/**
**�������� getWebPageInfor
**���ã� ��ȡ��ҳ�ĸ�����Ϣ
**var url ��ҳ������
**/
function getWebPageInfor($pagehostname){
	//��mongodb��ȡ��ҳ�ĸ�����Ϣ
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