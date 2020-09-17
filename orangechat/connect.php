<?php
/*
I assume you know how to and already have created a mysql DB through phpMyAdmin
*/
$servername = "localhost"; //Usually localhost but could be a db address

$dbusername = ""; //Your db username

$dbpassword = ""; //Your db password

$dbname = ""; //Your db name

$conn = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

if (!$conn) {

    echo "DB is not connected...";

}

?>