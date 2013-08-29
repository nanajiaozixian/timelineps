<?php
reqiure_once("wrMysql.php");
if(!isset($_POST['pageid_d'])){
	return;
}
$userid = $_POST['pageid_d'];

$conn = connect_mysql();
$page_arr = getAllPages($userid);
$json_str = json_encode($page_arr);
echo $json_str;

?>