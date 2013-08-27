<?php
require_once("wrMysql.php");

$g_pageid = 0;

if(!isset($_POST["pageid"])){
	return;
}
$g_pageid = $_POST["pageid"];
$conn = connect_mysql();
$pageversions_arr = array();
$pageversions_arr = getPageVersions($g_pageid);
$json_string = json_encode($pageversions_arr);
echo $json_string;
mysql_close($conn);
?>