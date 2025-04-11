<?php
$method = $_SERVER['REQUEST_METHOD'];
$url = $_GET['url'] ?? '';

if (!$url) {
    http_response_code(400);
    echo "Missing URL";
    exit;
}

$headers = [
    "User-Agent: Mozilla/5.0"
];

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $headers[] = "Authorization: " . $_SERVER['HTTP_AUTHORIZATION'];
}

if (isset($_SERVER['CONTENT_TYPE'])) {
    $headers[] = "Content-Type: " . $_SERVER['CONTENT_TYPE'];
}

$options = [
    "http" => [
        "method" => $method,
        "header" => implode("\r\n", $headers) . "\r\n",
        "ignore_errors" => true
    ]
];

if ($method === 'POST') {
    $body = file_get_contents("php://input");
    $options['http']['content'] = $body;
}

$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

foreach ($http_response_header as $header) {
    if (stripos($header, "Content-Type:") !== false) {
        header($header);
        break;
    }
}

echo $response;
?>