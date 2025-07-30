<?php
require_once 'DB.php';

class Producto {
    private $pdo;

    public $id_producto;
    public $id_usuario;
    public $nombre;
    public $descripcion;
    public $precio;
    public $imagen;
    public $fecha_publicacion;
    public $disponibles;
    public $categoria;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function crear($id_usuario, $nombre, $descripcion, $precio, $imagen = 'default.jpg', $disponibles = null, $categoria = 'Otros') {
        $sql = "INSERT INTO productos (id_usuario, nombre, descripcion, precio, imagen, disponibles, categoria) VALUES (:id_usuario, :nombre, :descripcion, :precio, :imagen, :disponibles, :categoria)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':imagen' => $imagen,
            ':disponibles' => $disponibles,
            ':categoria' => $categoria
        ]);
    }

    public function obtenerPorId($id_producto) {
        $sql = "SELECT * FROM productos WHERE id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_producto' => $id_producto]);
        return $stmt->fetch();
    }

    public function obtenerPorUsuario($id_usuario) {
        $sql = "SELECT * FROM productos WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

    public function actualizar($id_producto, $nombre, $descripcion, $precio, $imagen, $disponibles, $categoria) {
        $sql = "UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio, imagen = :imagen, disponibles = :disponibles, categoria = :categoria WHERE id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':imagen' => $imagen,
            ':disponibles' => $disponibles,
            ':categoria' => $categoria,
            ':id_producto' => $id_producto
        ]);
    }

    public function eliminar($id_producto) {
        $sql = "DELETE FROM productos WHERE id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_producto' => $id_producto]);
    }

    public function obtenerTodos($buscar = '', $categoria = '')
    {
        $sql = "SELECT p.*, u.nombre AS vendedor 
                FROM productos p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE 1=1";

        $params = [];

        if (!empty($buscar)) {
            $sql .= " AND p.nombre LIKE :buscar";
            $params[':buscar'] = '%' . $buscar . '%';
        }

        if (!empty($categoria)) {
            $sql .= " AND p.categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function obtenerDetalleConVendedor($id) {
        $sql = "SELECT p.*, u.nombre AS vendedor
                FROM productos p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_producto = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        }


    }
?>
