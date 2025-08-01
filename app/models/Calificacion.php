<?php
require_once 'DB.php';

class Calificacion {
    private $pdo;

    public $id_calificacion;
    public $id_producto;
    public $id_usuario;
    public $estrellas;
    public $comentario;
    public $fecha;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function crear($id_producto, $id_usuario, $estrellas, $comentario = null) {
        $sql = "INSERT INTO calificaciones (id_producto, id_usuario, estrellas, comentario) VALUES (:id_producto, :id_usuario, :estrellas, :comentario)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_producto' => $id_producto,
            ':id_usuario' => $id_usuario,
            ':estrellas' => $estrellas,
            ':comentario' => $comentario
        ]);
    }

    public function obtenerPorProducto($id_producto) {
        $sql = "SELECT * FROM calificaciones WHERE id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_producto' => $id_producto]);
        return $stmt->fetchAll();
    }

    public function eliminar($id_calificacion) {
        $sql = "DELETE FROM calificaciones WHERE id_calificacion = :id_calificacion";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_calificacion' => $id_calificacion]);
    }
}
?>
