TEST
<?php

$servername = "localhost:3306";
$username = "root";
$password = "passroot";
$dbname = "Test";

$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
