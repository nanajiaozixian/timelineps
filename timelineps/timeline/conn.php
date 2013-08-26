
<?php

$link = mysql_connect('localhost', '', '');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
//echo 'Connected successfully<br>';


$db_selected = mysql_select_db('test');
if (!$db_selected) {
    die('Could not select database: ' . mysql_error());
}
//echo "DB connected successfully<p>";

?>