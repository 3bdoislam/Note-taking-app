<?php
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "123";
$dbName = "note_app";

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
