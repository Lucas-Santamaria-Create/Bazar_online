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
        $sql = "SELECT r.*, p.nombre AS nombre_producto 
                FROM reservas r
                JOIN productos p ON r.id_producto = p.id_producto
                WHERE r.id_usuario = :id_usuario
                ORDER BY r.fecha_reserva DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

 public function obtenerPorVendedor($id_vendedor)
{
    $sql = "SELECT r.*, 
                   p.nombre AS nombre_producto, 
                   u.email AS email_comprador,
                   u.nombre AS nombre_comprador
            FROM reservas r
            JOIN productos p ON r.id_producto = p.id_producto
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            WHERE p.id_usuario = ?";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id_vendedor]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

public function obtenerReservasPorUsuario($id_usuario) {
    $sql = "SELECT r.*, p.nombre AS nombre_producto, u.email AS email_vendedor
            FROM reservas r
            JOIN productos p ON r.id_producto = p.id_producto
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            WHERE r.id_usuario = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id_usuario]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function obtenerReservasPorVendedor($id_vendedor) {
    $query = $this->pdo->prepare("
        SELECT r.*, p.nombre AS nombre_producto, u.nombre AS nombre_comprador, u.email AS email_comprador
        FROM reservas r
        INNER JOIN productos p ON r.id_producto = p.id_producto
        INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE p.id_vendedor = :id_vendedor
    ");
    $query->bindParam(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


public function cancelarReserva($id_reserva) {
    $this->pdo->beginTransaction();

    try {
        // Obtener id_producto para aumentar su stock
        $queryInfo = $this->pdo->prepare("SELECT id_producto FROM reservas WHERE id_reserva = ?");
        $queryInfo->execute([$id_reserva]);
        $reserva = $queryInfo->fetch(PDO::FETCH_ASSOC);

        if ($reserva) {
            // Devolver 1 unidad al stock (campo disponibles)
            $queryStock = $this->pdo->prepare("UPDATE productos SET disponibles = disponibles + 1 WHERE id_producto = ?");
            $queryStock->execute([$reserva['id_producto']]);
        }

        // Cambiar estado de la reserva a 'cancelada'
        $query = $this->pdo->prepare("UPDATE reservas SET estado = 'cancelada' WHERE id_reserva = ?");
        $query->execute([$id_reserva]);

        $this->pdo->commit();
    } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}

}
?>
