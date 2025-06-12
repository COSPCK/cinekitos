<?php
session_start();
header('Content-Type: application/json');

$response = ['logged_in' => false, 'user_name' => null];

if (isset($_SESSION['id_usuario']) && isset($_SESSION['nombre_usuario'])) {
    $response['logged_in'] = true;
    $response['user_name'] = $_SESSION['nombre_usuario'];
}

echo json_encode($response);
?>