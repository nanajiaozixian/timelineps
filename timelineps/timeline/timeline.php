
<?php

include 'conn.php'; 

$usern = $_POST['user'];
$pass = $_POST['password'];

if($usern == "" || $pass == ""){

    echo "empty!";
    header("Location: login.php");
}



$sql = "SELECT password, username FROM chaunceyzhang where username = '$usern'";

$result = mysql_query($sql);


if (!$result) {
    die('Query failed: ' . mysql_error());
}


else{
     if (mysql_num_rows($result) == 0){
     echo "You win";
     header("Location: login.php");
     }
}


for ($i = mysql_num_rows($result) - 1; $i >= 0; $i--) {
    //echo mysql_data_seek($result, $i);f
    if (!mysql_data_seek($result, $i)) {
        echo "Cannot seek to row $i: " . mysql_error() . "\n";
        
        continue;
    }

    if (!($row = mysql_fetch_assoc($result))) {
        continue;
    }


    if ($row['username']!= $usern){
        echo "SB";
        header("Location: login.php");
    }
     else{


        header("Location: go.php");
    }

    if ($row['password']!= $pass){
        echo "SB";
        header("Location: login.php");
    }

    else{

        header("Location: go.php");
    }
}


mysql_free_result($result);

?>
