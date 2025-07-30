<?php
require_once 'DB.php';

class Reserva {
    private $pdo;

    public $id_reserva;
    public $id_producto;
    public $id_usuario;
    public $fecha_reserva;
    public $pago_adelantado;
    public $estado;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function crear($id_producto, $id_usuario, $pago_adelantado, $estado = 'pendiente') {
        $sql = "INSERT INTO reservas (id_producto, id_usuario, pago_adelantado, estado) VALUES (:id_producto, :id_usuario, :pago_adelantado, :estado)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_producto' => $id_producto,
            ':id_usuario' => $id_usuario,
            ':pago_adelantado' => $pago_adelantado,
            ':estado' => $estado
        ]);
    }

    public function obtenerPorId($id_reserva) {
        $sql = "SELECT * FROM reservas WHERE id_reserva = :id_reserva";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_reserva' => $id_reserva]);
        return $stmt->fetch();
    }

    public function obtenerPorUsuario($id_usuario) {
        $sql = "SELECT * FROM reservas WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

    public function obtenerPorVendedor($id_vendedor) {
        $sql = "SELECT r.*, u.nombre as nombre_comprador, p.nombre as nombre_producto 
                FROM reservas r
                JOIN productos p ON r.id_producto = p.id_producto
                JOIN usuarios u ON r.id_usuario = u.id_usuario
                WHERE p.id_usuario = :id_vendedor
                ORDER BY r.fecha_reserva DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_vendedor' => $id_vendedor]);
        return $stmt->fetchAll();
    }

    public function actualizar($id_reserva, $estado) {
        $sql = "UPDATE reservas SET estado = :estado WHERE id_reserva = :id_reserva";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':id_reserva' => $id_reserva
        ]);
    }

    public function eliminar($id_reserva) {
        $sql = "DELETE FROM reservas WHERE id_reserva = :id_reserva";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_reserva' => $id_reserva]);
    }
}
?>
