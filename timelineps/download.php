<?php
require_once "global.php";
require_once "savefiles.php";

if(isset($_POST['csshref'])){
	$csshref = $_POST['csshref'];
	//var_dump($csshref);
}else{
	$csshref = "";
}

if(isset($_POST['jssrcs'])){
	$jssrcs = $_POST['jssrcs'];
	//var_dump($jssrcs);
}else{
	$jssrcs = "";
}


if(isset($_POST['page'])){
	$page = $_POST['page'];
	//echo $page;
}else{
	$page = "";
}
if(isset($_POST['copyfile'])){
	$copyfile = $_POST['copyfile'];
	//echo $copyfile;
}else{
	$copyfile = "";
}

if(isset($_POST['userid'])){
	$userid = $_POST['userid'];
	//echo $copyfile;
}else{
	$userid = 0;
}
if($copyfile!=""){
	$str_file = file_get_contents($copyfile);
	$local_file  = "local.html";
	$newcsslinks = "";
	//file_put_contents($local_file, $str_file);
	
	//�Ա�css�ļ������Ƿ���Ҫ����µĶ�̬���ص�css�ļ�
	$str_file = relative_to_absolute($str_file, $page);
	preg_match_all("/<link\s+.*?href=[\"|'](.+?)[\"|'].*?>/",$str_file,$links, PREG_SET_ORDER);//links �ﱣ���˴�ҳ���ȡ������css�ļ���·��
	if(sizeof($links)!==sizeof($csshref)){
		
		$linksarr = array();
		foreach($links as $val){			
			array_push($linksarr, $val[1]);
		}
		$diff = ary_diff( $csshref, $linksarr );	
		if(is_array($diff)){
  		foreach($diff as $val){
  			$newcsslinks=$newcsslinks.'<link type="text/css" rel="stylesheet" href="'.$val.'"/>\r\n';
  		}
		}
		
	}
	
	
		//�Ա�js�ļ������Ƿ���Ҫ����µĶ�̬���ص�js�ļ�
	preg_match_all("/<script\s+.*?src=[\"|']([^\"']*)[\"|'].*?>/",$str_file,$scripts, PREG_SET_ORDER);//scripts �ﱣ���˴�ҳ���ȡ������js�ļ���·��
	if(sizeof($scripts)!==sizeof($jssrcs)){
		
		$linksarr = array();
		foreach($scripts as $val){			
			array_push($linksarr, $val[1]);
		}
		$diff = ary_diff( $jssrcs, $linksarr );	
		if(is_array($diff)){	
  		foreach($diff as $val){
  			$newcsslinks=$newcsslinks.'<script type="text/javascript" src="'.$val.'"></script>\r\n';
  		}
		}
	}
	
		$newcsslinks = $newcsslinks."</head>";
		$new_str = str_replace("</head>", $newcsslinks, $str_file);
		$new_str = relative_to_absolute($new_str, $page);
		file_put_contents($local_file, $new_str);
		downloadFiles($new_str, $page);
}



?>