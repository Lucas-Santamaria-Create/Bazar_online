<?php
require_once __DIR__ . '/app/controllers/ProductoController.php';

$buscar = $_GET['buscar'] ?? '';
$categoria = $_GET['categoria'] ?? '';

$controller = new ProductoController();
$controller->mostrarCatalogo($buscar, $categoria);
