<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Tratamento especial para requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
require_once 'router.php';
require_once 'sync_image.php';

$router = new Router();

// Definir todas as rotas aqui
$router->addRoute('GET', '/health', 'handleHealth');
$router->addRoute('POST', '/sync_image', 'handleSyncImage');

// Processa a requisição
$result = $router->handleRequest();
if ($result) {
    echo json_encode($result);
}