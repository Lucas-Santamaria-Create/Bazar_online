<?php
require_once __DIR__ . '/../models/Producto.php';

class ProductoController {

    public function mostrarCatalogo($buscar = '', $categoria = '') {
        $productoModel = new Producto();
        $productos = $productoModel->obtenerTodos($buscar, $categoria);
        include __DIR__ . '/../views/catalogo.php'; // Vista que lista todos los productos
    }

    public function obtenerProductoPorId($id) {
        $productoModel = new Producto();
        return $productoModel->obtenerPorId($id);
    }

    public function detalle($id) {
        $producto = $this->obtenerProductoPorId($id);
        if (!$producto) {
            echo "<h2>Producto no encontrado</h2><a href='catalogo.php'>Volver al catálogo</a>";
            exit;
        }
        include __DIR__ . '/../views/detalle_producto.php'; // Vista que muestra detalle
    }
}
