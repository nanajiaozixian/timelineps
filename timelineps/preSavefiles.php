<?php
include_once("global.php");

$url = "";
//��ȡ��ҳurl
if(isset($_POST['pageurl_d'])){
	$url = $_POST['pageurl_d'];
}else{
	$url = "";
}

//�ж�url����Ч��
if(get_headers($url)===false){
	return;
}
$parts = parse_url($url);//����url
$host = $parts['host'];//��ȡhostname

$str_file = EnCodeForGoogleHK($url);

$main_file  = "source.html";
$copy_file  = "copy.html";//�������Ӷ��Ǿ���·������ʱ��html�ļ�
file_put_contents($main_file, $str_file);
$str_copy = $str_file;
$str_new = relative_to_absolute($str_copy, $url);
file_put_contents($copy_file, $str_new);
echo $copy_file; 

//��Ϊgoogle.hkҳ��ı��뷽ʽ��CP936����Ҫ���⴦������и��÷������ԸĽ��������
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