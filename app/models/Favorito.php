<?php
require_once 'DB.php';

/**
 * Clase Favorito que maneja las operaciones relacionadas con los productos favoritos de los usuarios.
 */
class Favorito {
    private $pdo;
    public $id_favorito;
    public $id_usuario;
    public $id_producto;
    public $fecha_agregado;

    /**
     * Constructor que inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    /**
     * Agrega un producto a los favoritos de un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @param int $id_producto ID del producto.
     * @return bool Resultado de la operación de inserción.
     */
    public function agregar($id_usuario, $id_producto) {
        $sql = "INSERT INTO favoritos (id_usuario, id_producto) VALUES (:id_usuario, :id_producto)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_producto' => $id_producto
        ]);
    }

    /**
     * Verifica si un producto ya está en los favoritos de un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @param int $id_producto ID del producto.
     * @return bool True si existe, false si no.
     */
    public function exists($id_usuario, $id_producto) {
        $sql = "SELECT COUNT(*) FROM favoritos WHERE id_usuario = :id_usuario AND id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_producto' => $id_producto
        ]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtiene todos los productos favoritos de un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @return array Lista de productos favoritos.
     */
    public function obtenerPorUsuario($id_usuario) {
        $sql = "SELECT * FROM favoritos WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

    /**
     * Elimina un producto de los favoritos por su ID.
     *
     * @param int $id_favorito ID del favorito a eliminar.
     * @return bool Resultado de la operación de eliminación.
     */
    public function eliminar($id_favorito) {
        $sql = "DELETE FROM favoritos WHERE id_favorito = :id_favorito";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_favorito' => $id_favorito]);
    }
}
?>
