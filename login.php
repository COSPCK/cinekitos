<?php
include 'conexion.php';

session_start(); // Iniciar sesión para almacenar el estado del usuario

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'user_name' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($nombre_usuario) || empty($contrasena)) {
        $response['message'] = 'Ambos campos son obligatorios.';
        echo json_encode($response);
        $conn->close();
        exit();
    }

    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, contrasena FROM Usuarios WHERE nombre_usuario = ? OR email = ?");
    $stmt->bind_param("ss", $nombre_usuario, $nombre_usuario); // Permite login con username o email
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verificar la contraseña hasheada
        if (password_verify($contrasena, $user['contrasena'])) {
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
            $response['success'] = true;
            $response['message'] = '¡Inicio de sesión exitoso!';
            $response['user_name'] = $user['nombre_usuario'];
        } else {
            $response['message'] = 'Contraseña incorrecta.';
        }
    } else {
        $response['message'] = 'Usuario no encontrado.';
    }
    $stmt->close();
} else {
    $response['message'] = 'Método de solicitud no válido.';
}

echo json_encode($response);
$conn->close();
?>