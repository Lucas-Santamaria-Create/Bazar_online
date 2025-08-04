<?php
/**
 * Clase DB que implementa el patrón Singleton para la conexión a la base de datos.
 */
class DB {
    private static $instance = null;
    private $pdo;

    /**
     * Constructor privado que establece la conexión PDO a la base de datos.
     */
    private function __construct() {
        $host = 'localhost';
        $db   = 'bazar_online';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Obtiene la instancia única de la clase DB.
     *
     * @return DB Instancia de la clase DB.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    /**
     * Obtiene la conexión PDO.
     *
     * @return PDO Conexión PDO a la base de datos.
     */
    public function getConnection() {
        return $this->pdo;
    }
}
?>
