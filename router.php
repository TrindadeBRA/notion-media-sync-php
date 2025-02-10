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
        
        // Melhoria no tratamento do path base
        // $basePath = '/notion-media-sync-php';
        // if (strpos($path, $basePath) === 0) {
        //     $path = substr($path, strlen($basePath));
        // }
        
        // Garantir que o path comece com /
        $path = '/' . ltrim($path, '/');

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
        try {
            $headers = getallheaders();
            if ($headers === false || !is_array($headers)) {
                $this->sendAuthError(500, 'Erro ao ler cabeçalhos da requisição');
            }
            
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : 
                         (isset($headers['authorization']) ? $headers['authorization'] : '');
            
            if (empty($authHeader)) {
                $this->sendAuthError(401, 'Token de autenticação não fornecido');
            }
            
            if (!preg_match('/Bearer\s+(.*)/', $authHeader, $matches)) {
                $this->sendAuthError(401, 'Formato de token inválido');
            }
            
            $token = $matches[1];
            
            if ($token !== API_KEY) {
                $this->sendAuthError(403, 'Token de autenticação inválido');
            }
        } catch (Exception $e) {
            $this->sendAuthError(500, 'Erro interno ao validar token');
        }
    }

    private function sendAuthError($statusCode, $message) {
        header('HTTP/1.1 ' . $statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
        exit();
    }
}

// Configuração das rotas
$router = new Router();
$router->addRoute('GET', '/health', '/health.php');
$router->addRoute('POST', '/sync_image', '/sync_image.php');

// Processa a requisição
$router->handleRequest(); 