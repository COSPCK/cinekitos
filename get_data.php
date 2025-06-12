<?php
header('Content-Type: application/json');
include 'conexion.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'funciones':
        getFunciones($conn);
        break;
    case 'asientos':
        getAsientos($conn);
        break;
    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}

function getFunciones($conn) {
    $idPelicula = $_GET['id_pelicula'] ?? 0;
    if (!$idPelicula) {
        echo json_encode(['error' => 'ID de película no proporcionado']);
        return;
    }

    $sql = "SELECT 
                f.id_funcion, f.fecha_hora, f.precio,
                s.id_sala, s.numero_sala,
                c.id_cine, c.nombre AS nombre_cine
            FROM Funciones f
            JOIN Salas s ON f.id_sala = s.id_sala
            JOIN Cines c ON s.id_cine = c.id_cine
            WHERE f.id_pelicula = ? AND f.fecha_hora > NOW()
            ORDER BY c.nombre, f.fecha_hora";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPelicula);
    $stmt->execute();
    $result = $stmt->get_result();

    $cines = [];
    $funciones = [];
    while ($row = $result->fetch_assoc()) {
        if (!in_array(['id_cine' => $row['id_cine'], 'nombre' => $row['nombre_cine']], $cines)) {
            $cines[] = ['id_cine' => $row['id_cine'], 'nombre' => $row['nombre_cine']];
        }
        if (!isset($funciones[$row['id_cine']])) {
            $funciones[$row['id_cine']] = [];
        }
        $funciones[$row['id_cine']][] = [
            'id_funcion' => $row['id_funcion'],
            'fecha_hora' => $row['fecha_hora'],
            'precio' => $row['precio']
        ];
    }
    
    echo json_encode(['cines' => $cines, 'funciones' => $funciones]);
    $stmt->close();
}

function getAsientos($conn) {
    $idFuncion = $_GET['id_funcion'] ?? 0;
    if (!$idFuncion) {
        echo json_encode(['error' => 'ID de función no proporcionado']);
        return;
    }

    // Obtener la sala de la función
    $stmt_sala = $conn->prepare("SELECT id_sala FROM Funciones WHERE id_funcion = ?");
    $stmt_sala->bind_param("i", $idFuncion);
    $stmt_sala->execute();
    $result_sala = $stmt_sala->get_result();
    if ($result_sala->num_rows === 0) {
        echo json_encode(['error' => 'Función no encontrada']);
        return;
    }
    $id_sala = $result_sala->fetch_assoc()['id_sala'];
    $stmt_sala->close();

    // Obtener asientos ocupados
    $stmt_ocupados = $conn->prepare("SELECT id_asiento FROM CompraBoletos WHERE id_funcion = ?");
    $stmt_ocupados->bind_param("i", $idFuncion);
    $stmt_ocupados->execute();
    $result_ocupados = $stmt_ocupados->get_result();
    $asientos_ocupados = [];
    while($row = $result_ocupados->fetch_assoc()) {
        $asientos_ocupados[] = $row['id_asiento'];
    }
    $stmt_ocupados->close();

    // Obtener todos los asientos de la sala
    $stmt_todos = $conn->prepare("SELECT id_asiento FROM Asientos WHERE id_sala = ?");
    $stmt_todos->bind_param("i", $id_sala);
    $stmt_todos->execute();
    $result_todos = $stmt_todos->get_result();
    
    $asientos = [];
    while($row = $result_todos->fetch_assoc()) {
        $asientos[] = [
            'id_asiento' => $row['id_asiento'],
            'ocupado' => in_array($row['id_asiento'], $asientos_ocupados)
        ];
    }
    
    echo json_encode(['asientos' => $asientos]);
    $stmt_todos->close();
}

$conn->close();
?>
