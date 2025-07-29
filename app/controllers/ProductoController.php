<?php
require_once __DIR__ . '/../models/Producto.php';

class ProductoController {

    private $productoModel;

    public function __construct() {
        $this->productoModel = new Producto();
    }

    
    public function mostrarCatalogo($buscar = '', $categoria = '') {
        // Sanitizar entrada si fuera necesario
        $productos = $this->productoModel->obtenerTodos($buscar, $categoria);
        include __DIR__ . '/../views/catalogo.php';
    }

    public function obtenerProductoPorId($id) {
        if (!is_numeric($id)) {
            return false;
        }
        return $this->productoModel->obtenerPorId($id);
    }

    public function detalle($id) {
        $producto = $this->obtenerProductoPorId($id);
        if (!$producto) {
            // Aquí podrías cargar una vista de error o redirigir
            echo "<h2>Producto no encontrado</h2><a href='catalogo.php'>Volver al catálogo</a>";
            exit;
        }
        include __DIR__ . '/../views/detalle_producto.php';
    }
}
