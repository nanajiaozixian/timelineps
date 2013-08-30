<?php
require_once("wrMysql.php");
	if(!isset($_POST['pageid_d'])){
		return;
	}
	$pageid = $_POST['pageid_d'];
	$conn = connect_mysql();
	$pageinf = getPageInfor($pageid);
	$json_str = json_encode($pageinf);
	echo $json_str;
	mysql_close($conn);
?>