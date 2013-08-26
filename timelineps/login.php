<?php
	require_once("wrMysql.php");
	if(!isset($_POST['username']) || !isset($_POST['password'])){
		echo "Username and Password Error!";
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	login_mysql();
	
	
	function login_mysql(){
		global $username;
		global $password;
		$conn = connect_mysql();
		$query = "select userid from users where users.username = '".$username."' and users.password = '".$password."'";
		$result=mysql_query($query);
		if (!$result) {
    die("Could not find".mysql_error());
		}
		if(mysql_num_rows($result) <= 0){
			die ("Username or Password isnot correct!");
		}
		if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
    	//ВщевБэuserpages
        //$query = "select pageid from userpages where userpages.userid = '".$row['userid']."'";
        echo $row['userid'];
    }
}
		
		mysql_close($conn); 
	}
?>