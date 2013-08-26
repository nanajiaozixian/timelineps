<?php
function connect_mysql(){
	$conn = @mysql_connect('localhost', 'timeline', 'hitimeline');

		if(!$conn){
			die("Could not link to mysql".mysql_error());
		}
		$db = mysql_select_db('timelinedb', $conn);
		if (!$db) {
    	die ('Can\'t use foo : ' . mysql_error());
		}
		
		return $conn;
}

function getPageId($userid_in, $pagename_in){
	$query_pageid = "select pageid from userpages where userpages.userid = '".$userid_in."' and userpages.pagename = '".$pagename_in."'";
	$result=mysql_query($query_pageid);
	if (!$result) {
    die("Could not find".mysql_error());
	}
	if(mysql_num_rows($result) <= 0){
		$query = "insert into userpages (userid, pagename) values ('".$userid_in."', '".$pagename_in."')";
		$result=mysql_query($query);
		if (!$result) {
    	die("Insert failure".mysql_error());
		}
		getPageId($userid_in, $pagename_in);
	}
	if(mysql_num_rows($result) > 0){
		while ($row = mysql_fetch_assoc($result)) {
        echo $row['pageid'];
        return $row['pageid'];
    }
	}
}


function getPageVersion($pageid_in){
	$query = "select versionid from pages where pages.pageid = '".$pageid_in."'";
	$result=mysql_query($query);
	if (!$result) {
    die("Could not find".mysql_error());
	}
	if(mysql_num_rows($result) <= 0){
		return 0;
	}
	if(mysql_num_rows($result) >0){
		$query = "select version, versionid from pages where version = (select max(version) from pages)";
		$result=mysql_query($query);
		if(mysql_num_rows($result) > 0){
		while ($row = mysql_fetch_assoc($result)) {
        return $row['version'];
    }
	}
	}
}
?>