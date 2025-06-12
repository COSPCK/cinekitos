<?php
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

// Si la solicitud es AJAX (desde fetch), devuelve un JSON
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Sesión cerrada.']);
    exit();
} else {
    // Si no es AJAX, redirige como de costumbre
    header('Location: cinekitos.php');
    exit();
}
?>