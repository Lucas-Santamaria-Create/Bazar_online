<?php
require_once 'DB.php';

class Usuario {
    private $pdo;

    public $id_usuario;
    public $nombre;
    public $email;
    public $password;
    public $rol;
    public $fecha_registro;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

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

    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function obtenerPorId($id_usuario) {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetch();
    }

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

    public function actualizarSinPassword($id_usuario, $nombre, $email) {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':id_usuario' => $id_usuario
        ]);
    }

    public function eliminar($id_usuario) {
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_usuario' => $id_usuario]);
    }

    public function actualizarRol($id_usuario, $rol) {
        $sql = "UPDATE usuarios SET rol = :rol WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':rol' => $rol,
            ':id_usuario' => $id_usuario
        ]);
    }

    public function existeEmail($email) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
}
?>
