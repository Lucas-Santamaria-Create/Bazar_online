<?php
require_once 'DB.php';

class Producto {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    // Obtener productos con filtros opcionales buscar y categoria
    public function obtenerTodos($buscar = '', $categoria = '') {
    $sql = "SELECT p.*, u.nombre AS vendedor 
            FROM productos p
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            WHERE 1=1";

    $params = [];

    if ($buscar !== '') {
        $sql .= " AND (p.nombre LIKE :buscarNombre OR p.descripcion LIKE :buscarDescripcion)";
        $params[':buscarNombre'] = '%' . $buscar . '%';
        $params[':buscarDescripcion'] = '%' . $buscar . '%';
    }

    if ($categoria !== '') {
        $sql .= " AND p.categoria = :categoria";
        $params[':categoria'] = $categoria;
    }

    $sql .= " ORDER BY p.fecha_publicacion DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorId($id) {
    $sql = "SELECT p.*, u.nombre AS vendedor 
            FROM productos p
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            WHERE p.id_producto = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}
?>
