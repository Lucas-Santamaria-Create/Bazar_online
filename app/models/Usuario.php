<?php
require_once 'DB.php';

/**
 * Clase Usuario que maneja las operaciones relacionadas con los usuarios del sistema.
 */
class Usuario {
    private $pdo;

    public $id_usuario;
    public $nombre;
    public $email;
    public $password;
    public $rol;
    public $fecha_registro;

    /**
     * Constructor que inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     *
     * @param string $nombre Nombre del usuario.
     * @param string $email Correo electrónico del usuario.
     * @param string $password Contraseña del usuario (debe estar hasheada).
     * @param string $rol Rol del usuario (por defecto 'comprador').
     * @return bool Resultado de la operación de inserción.
     */
    public function crear($nombre, $email, $password, $rol = 'comprador') {
        $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $password,
            ':rol' => $rol
        ]);
    }

    /**
     * Obtiene un usuario por su correo electrónico.
     *
     * @param string $email Correo electrónico del usuario.
     * @return array|false Datos del usuario o false si no se encuentra.
     */
    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Obtiene un usuario por su ID.
     *
     * @param int $id_usuario ID del usuario.
     * @return array|false Datos del usuario o false si no se encuentra.
     */
    public function obtenerPorId($id_usuario) {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetch();
    }

    /**
     * Actualiza los datos de un usuario (sin incluir la contraseña).
     *
     * @param int $id_usuario ID del usuario.
     * @param string $nombre Nuevo nombre del usuario.
     * @param string $email Nuevo correo electrónico del usuario.
     * @param string $rol Nuevo rol del usuario.
     * @return bool Resultado de la operación de actualización.
     */
    public function actualizar($id_usuario, $nombre, $email, $rol) {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':rol' => $rol,
            ':id_usuario' => $id_usuario
        ]);
    }

    /**
     * Actualiza los datos de un usuario incluyendo la contraseña.
     *
     * @param int $id_usuario ID del usuario.
     * @param string $nombre Nuevo nombre del usuario.
     * @param string $email Nuevo correo electrónico del usuario.
     * @param string $password Nueva contraseña del usuario.
     * @return bool Resultado de la operación de actualización.
     */
    public function actualizarConPassword($id_usuario, $nombre, $email, $password) {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :password WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $password,
            ':id_usuario' => $id_usuario
        ]);
    }

    /**
     * Actualiza los datos de un usuario sin modificar la contraseña.
     *
     * @param int $id_usuario ID del usuario.
     * @param string $nombre Nuevo nombre del usuario.
     * @param string $email Nuevo correo electrónico del usuario.
     * @return bool Resultado de la operación de actualización.
     */
    public function actualizarSinPassword($id_usuario, $nombre, $email) {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':id_usuario' => $id_usuario
        ]);
    }

    /**
     * Elimina un usuario por su ID.
     *
     * @param int $id_usuario ID del usuario a eliminar.
     * @return bool Resultado de la operación de eliminación.
     */
    public function eliminar($id_usuario) {
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_usuario' => $id_usuario]);
    }

    /**
     * Actualiza el rol de un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @param string $rol Nuevo rol del usuario.
     * @return bool Resultado de la operación de actualización.
     */
    public function actualizarRol($id_usuario, $rol) {
        $sql = "UPDATE usuarios SET rol = :rol WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':rol' => $rol,
            ':id_usuario' => $id_usuario
        ]);
    }

    /**
     * Verifica si un correo electrónico ya existe en la base de datos.
     *
     * @param string $email Correo electrónico a verificar.
     * @return bool True si existe, false si no.
     */
    public function existeEmail($email) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
}
?>
