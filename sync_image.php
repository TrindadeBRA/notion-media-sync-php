<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Ajuste conforme necessário
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Método não permitido']));
}

// Recebe a URL do arquivo via POST
$data = json_decode(file_get_contents('php://input'), true);
$fileUrl = $data['fileUrl'] ?? null;
$fileFolder = $data['fileFolder'] ?? null;

if (!$fileFolder) {
    http_response_code(400);
    die(json_encode(['error' => 'Pasta não fornecida']));
}

if (!$fileUrl) {
    http_response_code(400);
    die(json_encode(['error' => 'URL do arquivo não fornecida']));
}

// Pasta para salvar os arquivos
$uploadDir = 'file-uploads/' . ($fileFolder ? $fileFolder . '/' : '');
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Gera nome do arquivo sanitizado a partir do nome original
$originalFileName = basename(parse_url($fileUrl, PHP_URL_PATH));
$extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
$nameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);

// Sanitiza o nome do arquivo
$sanitizedName = preg_replace('/[^a-zA-Z0-9-_.]/', '-', $nameWithoutExtension);
$fileName = $sanitizedName . '.' . $extension;
$filePath = $uploadDir . $fileName;

// Verifica se o arquivo já existe
$isExisting = file_exists($filePath);
if (!$isExisting) {
    // Verifica se a URL existe antes de tentar baixar
    $headers = @get_headers($fileUrl);
    if (!$headers || strpos($headers[0], '200') === false) {
        http_response_code(404);
        die(json_encode(['error' => 'URL da imagem inválida ou arquivo não encontrado']));
    }

    // Baixa o arquivo
    $fileContent = file_get_contents($fileUrl);
    if ($fileContent === false) {
        http_response_code(500);
        die(json_encode(['error' => 'Erro ao baixar o arquivo']));
    }

    // Salva o arquivo
    if (!file_put_contents($filePath, $fileContent)) {
        http_response_code(500);
        die(json_encode(['error' => 'Erro ao salvar o arquivo']));
    }
}

// Retorna o caminho do arquivo
echo json_encode([
    'success' => true,
    'url' => BASE_URL . $filePath,
    'isExisting' => $isExisting
]); 