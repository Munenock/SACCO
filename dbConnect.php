<?php

$host = "localhost";
$user = "root";
$password = "";
$databasename = "Saccodb";

$conn = mysqli_connect($host, $user, $password, $databasename);//connecting to the database

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
