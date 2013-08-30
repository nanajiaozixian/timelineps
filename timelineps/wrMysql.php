<?php
function connect_mysql(){

		$hostname = "wartisantimeline.db.11092185.hostedresource.com";
    $username = "wartisantimeline";
    $dbname = "wartisantimeline";

    //These variable values need to be changed by you before deploying
    $password = "Wartisan@2";

    //Connecting to your database
    $conn = mysql_connect($hostname, $username, $password) OR DIE ("Unable to 
    connect to database! Please try again later.");
    $db = mysql_select_db($dbname);

		if (!$db) {
    	die ('Can\'t use foo : ' . mysql_error());
		}
		
		
		
		return $conn;
}
function getAllPages($userid_in){
	$query_pages = "select * from userpages where userpages.userid = '".$userid_in."'";
	$result=mysql_query($query_pages);
	if(!$result){
		return;
	}
	if(mysql_num_rows($result)<=0){
		return;
	}else{
		$pages_arry =array();
		while($row=mysql_fetch_assoc($result)){
			array_push($pages_arry, $row);
		}
		return $pages_arry;
	}
}

function getPageId($userid_in, $pagename_in, $url_in){
	$query_pageid = "select pageid from userpages where userpages.userid = '".$userid_in."' and userpages.pagename = '".$pagename_in."'";
	$result=mysql_query($query_pageid);
	if (!$result) {
    die("Could not find".mysql_error());
	}
	if(mysql_num_rows($result) <= 0){
		$query = "insert into userpages values (NULL,'".$pagename_in."', '".$userid_in."', '".$url_in."')";
		$result=mysql_query($query);
		if (!$result) {
    	die("Insert failure".mysql_error());
		}
		getPageId($userid_in, $pagename_in, $url_in);
	}else if(mysql_num_rows($result) > 0){
		while ($row = mysql_fetch_assoc($result)) {
					
		       return $row['pageid'];
    }
	}
}

function getPageInfor($pageid){
	$query = "select * from userpages where userpages.pageid = '".$pageid."'";
	$result = mysql_query($query);
	if (!$result) {
    die("Could not find ".mysql_error());
	}
	if(mysql_num_rows($result) <= 0){
		return;
	}else if(mysql_num_rows($result) > 0){
		while ($row = mysql_fetch_assoc($result)) {
					
		       return $row;
    }
	}
}

function getMaxPageVersion($pageid_in){
	$query = "select versionid from pages where pages.pageid = '".$pageid_in."'";
	$result=mysql_query($query);
	if (!$result) {
    die("Could not find".mysql_error());
	}
	if(mysql_num_rows($result) <= 0){
		return 1;
	}
	if(mysql_num_rows($result) >0){
		$query = "select pageid, max(version) as maxversion from pages where pages.pageid = '".$pageid_in."' group by pageid";
		$result=mysql_query($query);
		if(mysql_num_rows($result) > 0){
		while ($row = mysql_fetch_assoc($result)) {
        return $row['maxversion']+1;
    }
	}
	}
}


function getPageVersions($pageid_in){
	$versions_arr = array();
	$query = "select * from pages where pages.pageid = '".$pageid_in."' order by version desc";
	$result = mysql_query($query);
	if (!$result) {
		echo 'query: $query';
    die("Could not find. ".mysql_error());
	}
	if(mysql_num_rows($result) <= 0){
		return null;//网页没有存任何版本
	}
	if(mysql_num_rows($result) > 0){
		while ($row = mysql_fetch_assoc($result)) {
        array_push($versions_arr, $row);
    }
  }
  
  return $versions_arr;
}
?>