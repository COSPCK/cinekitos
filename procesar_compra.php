<?php
header('Content-Type: application/json');
include 'conexion.php';

// Asumimos que el usuario está logueado y tenemos su ID (para este ejemplo, usamos un ID fijo)
// En una aplicación real, obtendrías esto de la sesión: $_SESSION['id_usuario']
$id_usuario_logueado = 1; 

// Obtener datos del POST
$id_pelicula = $_POST['id_pelicula'] ?? 0;
$id_funcion = $_POST['id_funcion'] ?? 0;
$tipo_pago = $_POST['tipo_pago'] ?? '';
$asientos = json_decode($_POST['asientos'] ?? '[]', true);
$precio_total = $_POST['precio_total'] ?? 0;

// Validación básica
if (empty($id_pelicula) || empty($id_funcion) || empty($tipo_pago) || empty($asientos)) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos para procesar la compra.']);
    exit;
}

$conn->begin_transaction();

try {
    $validacion_tarjeta = null;
    $folio_pago_efectivo = null;

    if ($tipo_pago == 'Tarjeta') {
        // Simulación de validación y generación de folio de tarjeta
        $validacion_tarjeta = 'TRX' . strtoupper(uniqid());
    } else {
        // Simulación de generación de folio para pago en efectivo
        $folio_pago_efectivo = 'PAG-EFEC-' . strtoupper(uniqid());
    }
    
    $stmt = $conn->prepare("INSERT INTO CompraBoletos (id_usuario, id_funcion, id_asiento, folio_boleto, tipo_pago, validacion_tarjeta, folio_pago_efectivo) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $folio_base = 'BOL-' . time();
    $i = 0;
    foreach ($asientos as $id_asiento) {
        $folio_boleto = $folio_base . '-' . $i;
        $stmt->bind_param("iiissss", $id_usuario_logueado, $id_funcion, $id_asiento, $folio_boleto, $tipo_pago, $validacion_tarjeta, $folio_pago_efectivo);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new Exception("No se pudo registrar el asiento ID: $id_asiento.");
        }
        $i++;
    }

    $conn->commit();
    $stmt->close();
    
    // Obtener datos para el ticket
    $sql_ticket = "SELECT p.titulo, c.nombre as cine, s.numero_sala, f.fecha_hora 
                   FROM Funciones f
                   JOIN Peliculas p ON f.id_pelicula = p.id_pelicula
                   JOIN Salas s ON f.id_sala = s.id_sala
                   JOIN Cines c ON s.id_cine = c.id_cine
                   WHERE f.id_funcion = ?";
    $stmt_ticket = $conn->prepare($sql_ticket);
    $stmt_ticket->bind_param("i", $id_funcion);
    $stmt_ticket->execute();
    $ticket_data = $stmt_ticket->get_result()->fetch_assoc();
    
    $asientos_str = implode(', ', array_map(function($id) use ($conn) {
        $res = $conn->query("SELECT CONCAT(fila, numero) as num FROM Asientos WHERE id_asiento = $id");
        return $res->fetch_assoc()['num'] ?? $id;
    }, $asientos));


    echo json_encode([
        'success' => true,
        'ticket' => [
            'folio' => $folio_base,
            'titulo' => $ticket_data['titulo'],
            'cine' => $ticket_data['cine'],
            'sala' => $ticket_data['numero_sala'],
            'fecha_hora' => $ticket_data['fecha_hora'],
            'asientos' => $asientos_str,
            'total' => number_format($precio_total, 2),
            'pago' => $tipo_pago
        ]
    ]);
    $stmt_ticket->close();

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error en la transacción: ' . $e->getMessage()]);
}

$conn->close();
?>
