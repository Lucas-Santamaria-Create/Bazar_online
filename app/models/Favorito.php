<?php
require_once 'DB.php';

class Favorito {
    private $pdo;
    public $id_favorito;
    public $id_usuario;
    public $id_producto;
    public $fecha_agregado;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function agregar($id_usuario, $id_producto) {
        $sql = "INSERT INTO favoritos (id_usuario, id_producto) VALUES (:id_usuario, :id_producto)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_producto' => $id_producto
        ]);
    }

    public function exists($id_usuario, $id_producto) {
        $sql = "SELECT COUNT(*) FROM favoritos WHERE id_usuario = :id_usuario AND id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_producto' => $id_producto
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerPorUsuario($id_usuario) {
        $sql = "SELECT * FROM favoritos WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

    public function eliminar($id_favorito) {
        $sql = "DELETE FROM favoritos WHERE id_favorito = :id_favorito";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_favorito' => $id_favorito]);
    }
}
?>
