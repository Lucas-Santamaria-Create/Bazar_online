<?php
require_once 'DB.php';

/**
 * Clase Producto que maneja las operaciones relacionadas con los productos en la base de datos.
 */
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

    /**
     * Constructor que inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    /**
     * Crea un nuevo producto en la base de datos.
     *
     * @param int $id_usuario ID del usuario que crea el producto.
     * @param string $nombre Nombre del producto.
     * @param string $descripcion Descripción del producto.
     * @param float $precio Precio del producto.
     * @param string $imagen Nombre del archivo de imagen del producto (por defecto 'default.jpg').
     * @param int|null $disponibles Cantidad disponible del producto.
     * @param string $categoria Categoría del producto (por defecto 'Otros').
     * @return bool Resultado de la operación de inserción.
     */
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

    /**
     * Obtiene un producto por su ID.
     *
     * @param int $id_producto ID del producto.
     * @return array|false Datos del producto o false si no se encuentra.
     */
    public function obtenerPorId($id_producto) {
        $sql = "SELECT * FROM productos WHERE id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_producto' => $id_producto]);
        return $stmt->fetch();
    }

    /**
     * Obtiene todos los productos de un usuario específico.
     *
     * @param int $id_usuario ID del usuario.
     * @return array Lista de productos.
     */
    public function obtenerPorUsuario($id_usuario) {
        $sql = "SELECT * FROM productos WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

    /**
     * Actualiza los datos de un producto.
     *
     * @param int $id_producto ID del producto.
     * @param string $nombre Nuevo nombre del producto.
     * @param string $descripcion Nueva descripción del producto.
     * @param float $precio Nuevo precio del producto.
     * @param string $imagen Nuevo nombre del archivo de imagen.
     * @param int|null $disponibles Nueva cantidad disponible.
     * @param string $categoria Nueva categoría.
     * @return bool Resultado de la operación de actualización.
     */
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

    /**
     * Elimina un producto por su ID.
     *
     * @param int $id_producto ID del producto a eliminar.
     * @return bool Resultado de la operación de eliminación.
     */
    public function eliminar($id_producto) {
        $sql = "DELETE FROM productos WHERE id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_producto' => $id_producto]);
    }

    /**
     * Actualiza la cantidad disponible de un producto.
     *
     * @param int $id_producto ID del producto.
     * @param int $nueva_cantidad Nueva cantidad disponible.
     * @return bool Resultado de la operación de actualización.
     */
    public function actualizarDisponibilidad($id_producto, $nueva_cantidad) {
        $sql = "UPDATE productos SET disponibles = :disponibles WHERE id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':disponibles' => $nueva_cantidad,
            ':id_producto' => $id_producto
        ]);
    }

    /**
     * Obtiene todos los productos, con opción de filtrar por nombre y categoría.
     *
     * @param string $buscar Texto para buscar en el nombre del producto.
     * @param string $categoria Categoría para filtrar.
     * @return array Lista de productos con información del vendedor.
     */
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

    /**
     * Obtiene el detalle de un producto junto con el nombre del vendedor.
     *
     * @param int $id ID del producto.
     * @return array|false Detalle del producto con información del vendedor o false si no se encuentra.
     */
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
