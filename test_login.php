<?php
$url = 'http://127.0.0.1:8080/api/login';
$data = ['email' => 'admin@eshasilk.com', 'password' => 'admin@123'];

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
$status_line = $http_response_header[0];

echo "Status: $status_line\n";
echo "Response: $result\n";
