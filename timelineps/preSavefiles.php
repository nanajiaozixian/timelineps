<?php
require_once "global.php";

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

$str_file = file_get_contents($url);
$main_file  = "source.html";
$copy_file  = "copy.html";//�������Ӷ��Ǿ���·������ʱ��html�ļ�
file_put_contents($main_file, $str_file);

$str_copy = $str_file;
$str_new = relative_to_absolute($str_copy, $url);
file_put_contents($copy_file, $str_new);
echo $copy_file; 
?>