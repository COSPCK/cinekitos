<?php
$host = "localhost";
$user = "root";
$password = "bellakitos";
$db = "cinedb";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}
?>