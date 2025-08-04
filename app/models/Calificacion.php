<?php
require_once 'DB.php';

/**
 * Clase Calificacion que maneja las operaciones relacionadas con las calificaciones de productos.
 */
class Calificacion {
    private $pdo;

    public $id_calificacion;
    public $id_producto;
    public $id_usuario;
    public $estrellas;
    public $comentario;
    public $fecha;

    /**
     * Constructor que inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    /**
     * Crea una nueva calificación para un producto.
     *
     * @param int $id_producto ID del producto a calificar.
     * @param int $id_usuario ID del usuario que realiza la calificación.
     * @param int $estrellas Número de estrellas otorgadas.
     * @param string|null $comentario Comentario opcional.
     * @return bool Resultado de la operación de inserción.
     */
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

    /**
     * Obtiene todas las calificaciones de un producto específico, junto con el nombre del usuario que las realizó.
     *
     * @param int $id_producto ID del producto.
     * @return array Lista de calificaciones.
     */
    public function obtenerPorProducto($id_producto) {
        $sql = "SELECT c.*, u.nombre AS nombre_usuario
                FROM calificaciones c
                LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.id_producto = :id_producto
                ORDER BY c.fecha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_producto' => $id_producto]);
        return $stmt->fetchAll();
    }

    /**
     * Elimina una calificación por su ID.
     *
     * @param int $id_calificacion ID de la calificación a eliminar.
     * @return bool Resultado de la operación de eliminación.
     */
    public function eliminar($id_calificacion) {
        $sql = "DELETE FROM calificaciones WHERE id_calificacion = :id_calificacion";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_calificacion' => $id_calificacion]);
    }
}
?>
