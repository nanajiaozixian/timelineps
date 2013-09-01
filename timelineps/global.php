<?php
require_once("wrMysql.php");
require_once("wrMongodb.php");

set_time_limit(300);
//error_reporting(0); //如果需要测试bug，可以先把这行注释掉
error_reporting(E_ERROR | E_PARSE);

//把文本内的相对路径换成绝对路径
function relative_to_absolute($content, $feed_url){
	//echo "feed_url: $feed_url<br/>";
	$url = $feed_url;
	preg_match('/(http|https|ftp):\/\//', $feed_url, $protocol);
	//echo "protocol: <br/>";
	//var_dump($protocol);
	$server_url = preg_replace("/(http|https|ftp|news):\/\//", "", $feed_url);

 $server_url = preg_replace("/\/.*/", "", $server_url);
 //echo "server_url: $server_url<br/>";

    if ($server_url == '') {

        return $content;

    }
 if (isset($protocol[0])) {
 	//echo "replace<br/>";
				
        $new_content = preg_replace('/href="\/([^\/]+)/', 'href="'.$protocol[0].$server_url.'/'.'\\1', $content);
        $new_content = preg_replace('/href="\/\//', 'href="'.$protocol[0], $new_content);

        //$new_content = preg_replace('/src="\//', 'src="'.$protocol[0].$server_url.'/', $new_content);
        $new_content = preg_replace('/src="\/([^\/]+)/', 'src="'.$protocol[0].$server_url.'/'.'\\1', $new_content);
        $new_content = preg_replace('/src="\/\//', 'src="'.$protocol[0], $new_content);
         
        $new_content = preg_replace('/url\((["|\']?)\/([^\/]+?)/', 'url('.'\\1'.$protocol[0].$server_url.'/'.'\\2', $new_content);
		$new_content = preg_replace('/url\((["|\']?)\/\//', 'url('.'\\1'.$protocol[0], $new_content);
				
		preg_match_all("/<link\s+[^>]*?href=[\"|'](.+?)[\"|'].*?>/",$new_content,$links, PREG_SET_ORDER);//links 里保存了从页面获取的所有css文件的路径
        
        $new_content = 	regLinksToAbsoulte($links, $url, $new_content);
        preg_match_all("/<script\s+[^>]*?src=[\"|']([^\"']*)[\"|'].*?>/",$new_content,$scripts, PREG_SET_ORDER);
        $new_content = 	regLinksToAbsoulte($scripts, $url, $new_content);
        
        preg_match_all("/<img\s+[^>]*?src=[\"|']([^\"']*)[\"|'].*?>/",$new_content,$images, PREG_SET_ORDER);
        $new_content = 	regLinksToAbsoulte($images, $url, $new_content);
        
        preg_match_all("/url\([\"|']?([^\"|']*?)[\"|']?\)/",$new_content,$images, PREG_SET_ORDER);
        $new_content = 	regLinksToAbsoulte($images, $url, $new_content);
       
			

    } else {
	//echo "not replace<br/>";
        $new_content = $content;

    }

    return $new_content;

}

function regLinksToAbsoulte($links, $url, $content){
	$arr_link = array(); //保存css 文件完整link
	$arr_abpath = array();//保存css 文件本地存储路径
	foreach($links as $val){	
		if(in_array($val[1],$arr_link) || !isNormalFile($val[1])){
			continue;
		}
		
		array_push($arr_link,$val[1]);
		
		if(strpos($val[1], "http:")!==0){
			
			$val[1] = format_url($val[1], $url);
		
		}			
		//判断链接有效性
		//echo $val[1]."<br/>";
		if(get_headers($val[1])!==false){		
				array_push($arr_abpath,$val[1]);   	
							
    }
	}
	
	//把html文件里的css路径更改指向保存的路径
	$str_new = $content;
	$str_new = str_replace($arr_link, $arr_abpath, $str_new);
	return $str_new;
}

function isNormalFile($filename_in){
	$filename = trim($filename_in);
	$filetype = substr($filename, -4);
	if($filetype!==".css" && $filetype!==".img" && $filetype!==".gif" && $filetype!==".png" && $filetype!==".jpg"){
		$filetype = substr($filename, -3);
		if($filetype!==".js"){
			$filetype = substr($filename, -5);
			if($filetype!==".icon"){
				return false;
			}
		}
	}
	return true;
	
}
function format_url($srcurl, $baseurl) {  
  $srcinfo = parse_url($srcurl);
  if(isset($srcinfo['scheme'])) {
    return $srcurl;
  }
  $baseinfo = parse_url($baseurl);
  $url = $baseinfo['scheme'].'://'.$baseinfo['host'];
  if(substr($srcinfo['path'], 0, 1) == '/') {
    $path = $srcinfo['path'];
  }else{
  	$basedirname = str_replace("\\","/",dirname($baseinfo['path']));//Doris
    $path = $basedirname.'/'.$srcinfo['path'];
    
  }
  $rst = array();
  $path_array = explode('/', $path);

  if(!$path_array[0]) {
    $rst[] = '';
  }
  foreach ($path_array AS $key => $dir) {
    if ($dir == '..') {
      if (end($rst) == '..') {
        $rst[] = '..';
      }elseif(!array_pop($rst)) {
        $rst[] = '..';
      }
    }elseif($dir && $dir != '.') {
      $rst[] = $dir;
      //var_dump($rst);
    }
   }
  if(!end($path_array)) {
    $rst[] = '';
  }
  $url .= implode('/', $rst);
  

  return str_replace('\\', '/', $url);
}


//找出两个数组的不同
function ary_diff( $ary_1, $ary_2 ) {
  // compare the value of 2 array
  // get differences that in ary_1 but not in ary_2
  // get difference that in ary_2 but not in ary_1
  // return the unique difference between value of 2 array
  if(!is_array($ary_1) || !is_array($ary_2)){
  	return false;
  }
  $diff = array();

  // get differences that in ary_1 but not in ary_2
  foreach ( $ary_1 as $v1 ) {
    $flag = 0;
    foreach ( $ary_2 as $v2 ) {
      $flag |= ( $v1 == $v2 );
      if ( $flag ) break;
    }
    if ( !$flag ) array_push( $diff, $v1 );
  }

  return $diff;
}
?>