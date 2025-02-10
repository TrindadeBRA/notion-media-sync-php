<?php
class Router {
    private $routes = [];

    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function handleRequest() {
        // Validação do token API para todas as requisições
        $this->validateApiToken();
        
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        
        // Remove o caminho base da URL se necessário
        $basePath = '/notion-media-sync-php';
        $path = str_replace($basePath, '', $path);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                require_once __DIR__ . '/' . ltrim($route['handler'], '/');
                return;
            }
        }

        // Rota não encontrada
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada']);
    }

    private function validateApiToken() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)/', $authHeader, $matches)) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            die(json_encode(['error' => 'Token de autenticação não fornecido'], JSON_UNESCAPED_UNICODE));
        }
        
        $token = $matches[1];
        
        if ($token !== API_KEY) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            die(json_encode(['error' => 'Token de autenticação inválido'], JSON_UNESCAPED_UNICODE));
        }
    }
}

// Configuração das rotas
$router = new Router();
$router->addRoute('GET', '/health', '/health.php');
$router->addRoute('POST', '/sync_image', '/sync_image.php');

// Processa a requisição
$router->handleRequest(); 