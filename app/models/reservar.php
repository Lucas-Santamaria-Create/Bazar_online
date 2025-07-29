<?php
require_once 'DB.php';

class Reserva {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function crearReserva($id_usuario, $id_producto, $pago_adelantado = 0.00, $estado = 'pendiente') {
    $sql = "INSERT INTO reservas (id_usuario, id_producto, fecha_reserva, pago_adelantado, estado) 
            VALUES (:id_usuario, :id_producto, NOW(), :pago_adelantado, :estado)";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':id_producto' => $id_producto,
        ':pago_adelantado' => $pago_adelantado,
        ':estado' => $estado,
    ]);
}

    public function yaReservado($id_usuario, $id_producto) {
        $sql = "SELECT COUNT(*) FROM reservas WHERE id_usuario = :id_usuario AND id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_producto' => $id_producto,
        ]);
        return $stmt->fetchColumn() > 0;
    }
}
