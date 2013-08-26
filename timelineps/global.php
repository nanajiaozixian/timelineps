<?php

//把文本内的相对路径换成绝对路径
function relative_to_absolute($content, $feed_url){
	//echo "feed_url: $feed_url<br/>";
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

    } else {
	//echo "not replace<br/>";
        $new_content = $content;

    }

    return $new_content;

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