<?php
$url = 'http://127.0.0.1:8080/api/register';
$data = [
    'username' => 'testuser_' . time(),
    'email' => 'test_' . time() . '@example.com',
    'password' => 'password123',
    'role' => 'user'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ],
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$status_line = http_get_last_response_headers()[0];

echo "Status: $status_line\n";
echo "Response: $result\n";
