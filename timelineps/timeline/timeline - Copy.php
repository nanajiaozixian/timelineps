
<?php

include 'conn.php'; 

// echo "Hi ".htmlspecialchars($_POST['user']); 
// echo "You are ".$_POST['password'];

$usern = $_POST['user'];
$pass = $_POST['password'];

if($usern == "" || $pass == ""){

    echo "empty!";
    header("Location: login.php");
}



$sql = "SELECT password, username FROM chaunceyzhang where username = '$usern'";
//$sql = "SELECT * FROM chaunceyzhang";
//echo $sql;

$result = mysql_query($sql);

//echo $result;

if (!$result) {
    die('Query failed: ' . mysql_error());
}


for ($i = mysql_num_rows($result) - 1; $i >= 0; $i--) {
    //echo mysql_data_seek($result, $i);
    if (!mysql_data_seek($result, $i)) {
        echo "Cannot seek to row $i: " . mysql_error() . "\n";
        continue;
    }

    if (!($row = mysql_fetch_assoc($result))) {
        continue;
    }

    // if($row['username'] != $usern){
    //     echo "SB";
    // }

    // else{
    //     echo $row['username'] . ' ' . $row['password'] . "<br />\n";       
    // }
   

    if ($row['password']!= $pass){
        echo "SB";
        header("Location: login.php");
    }

    else{
    // // if (!$row){
    // echo $row['username'] . ' ' . $row['password'] . "<br />\n";
    // // echo "sb";
    // // }
    //echo $usern . ' ' . $row['password'] . "<br />\n";

        header("Location: go.php");
    }
}


mysql_free_result($result);

?>
