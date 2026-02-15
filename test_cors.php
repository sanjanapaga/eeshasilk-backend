<?php
$url = 'http://127.0.0.1:8080/api/products';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: http://localhost:3000'
]);
curl_setopt($ch, CURLOPT_HEADER, true);
$res = curl_exec($ch);
echo $res;
curl_close($ch);
