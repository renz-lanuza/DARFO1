<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_darfo1";

$conn = mysqli_connect($servername, $username, $password, $database);

if ($conn) {
} else {
    die("connection failed" . mysqli_connect_error());
}
