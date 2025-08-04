<?php
require_once 'DB.php';

/**
 * Clase Reserva que maneja las operaciones relacionadas con las reservas de productos.
 */
class Reserva {
    private $pdo;

    public $id_reserva;
    public $id_producto;
    public $id_usuario;
    public $fecha_reserva;
    public $pago_adelantado;
    public $estado;

    /**
     * Constructor que inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    /**
     * Crea una nueva reserva.
     *
     * @param int $id_producto ID del producto a reservar.
     * @param int $id_usuario ID del usuario que realiza la reserva.
     * @param float $pago_adelantado Pago adelantado para la reserva.
     * @param string $estado Estado inicial de la reserva (por defecto 'pendiente').
     * @return bool Resultado de la operación de inserción.
     */
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

    /**
     * Obtiene una reserva por su ID.
     *
     * @param int $id_reserva ID de la reserva.
     * @return array|false Datos de la reserva o false si no se encuentra.
     */
    public function obtenerPorId($id_reserva) {
        $sql = "SELECT * FROM reservas WHERE id_reserva = :id_reserva";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_reserva' => $id_reserva]);
        return $stmt->fetch();
    }

    /**
     * Obtiene todas las reservas de un usuario específico.
     *
     * @param int $id_usuario ID del usuario.
     * @return array Lista de reservas.
     */
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

    /**
     * Obtiene todas las reservas de un vendedor específico.
     *
     * @param int $id_vendedor ID del vendedor.
     * @return array Lista de reservas.
     */
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

    /**
     * Actualiza el estado de una reserva.
     *
     * @param int $id_reserva ID de la reserva.
     * @param string $estado Nuevo estado.
     * @return bool Resultado de la operación de actualización.
     */
    public function actualizar($id_reserva, $estado) {
        $sql = "UPDATE reservas SET estado = :estado WHERE id_reserva = :id_reserva";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':id_reserva' => $id_reserva
        ]);
    }

    /**
     * Elimina una reserva por su ID.
     *
     * @param int $id_reserva ID de la reserva a eliminar.
     * @return bool Resultado de la operación de eliminación.
     */
    public function eliminar($id_reserva) {
        $sql = "DELETE FROM reservas WHERE id_reserva = :id_reserva";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_reserva' => $id_reserva]);
    }

    /**
     * Obtiene reservas detalladas de un usuario, incluyendo información del producto y vendedor.
     *
     * @param int $id_usuario ID del usuario.
     * @return array Lista de reservas con detalles.
     */
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

    /**
     * Obtiene reservas detalladas de un vendedor, incluyendo información del comprador.
     *
     * @param int $id_vendedor ID del vendedor.
     * @return array Lista de reservas con detalles.
     */
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

    /**
     * Cancela una reserva y actualiza el stock del producto correspondiente.
     *
     * @param int $id_reserva ID de la reserva a cancelar.
     * @throws Exception Si ocurre un error durante la transacción.
     */
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
