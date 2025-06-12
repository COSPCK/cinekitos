<?php
include 'conexion.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $email = $_POST['email'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($nombre_usuario) || empty($email) || empty($contrasena)) {
        $response['message'] = 'Todos los campos son obligatorios.';
        echo json_encode($response);
        $conn->close();
        exit();
    }

    // Hashear la contraseña antes de guardarla
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Verificar si el nombre de usuario o email ya existen
    $stmt = $conn->prepare("SELECT id_usuario FROM Usuarios WHERE nombre_usuario = ? OR email = ?");
    $stmt->bind_param("ss", $nombre_usuario, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response['message'] = 'El nombre de usuario o email ya están registrados.';
    } else {
        // Insertar nuevo usuario
        $stmt_insert = $conn->prepare("INSERT INTO Usuarios (nombre_usuario, email, contrasena) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sss", $nombre_usuario, $email, $contrasena_hash);

        if ($stmt_insert->execute()) {
            $response['success'] = true;
            $response['message'] = '¡Registro exitoso! Ya puedes iniciar sesión.';
        } else {
            $response['message'] = 'Error al registrar el usuario: ' . $stmt_insert->error;
        }
        $stmt_insert->close();
    }
    $stmt->close();
} else {
    $response['message'] = 'Método de solicitud no válido.';
}

echo json_encode($response);
$conn->close();
?>