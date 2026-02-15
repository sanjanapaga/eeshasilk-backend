<?php
$baseUrl = 'http://127.0.0.1:8080/api';
$token = '';

function request($url, $method = 'GET', $data = null, $token = null) {
    $options = [
        'http' => [
            'method'  => $method,
            'header'  => "Content-Type: application/json\r\n" . 
                         ($token ? "Authorization: Bearer $token\r\n" : ""),
            'content' => $data ? json_encode($data) : null,
            'ignore_errors' => true
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $headers = http_get_last_response_headers();
    return ['status' => $headers[0], 'body' => json_decode($result, true), 'raw' => $result];
}

echo "=== STARTING API TESTS ===\n\n";

// 1. Login
echo "1. Testing Login... ";
$login = request("$baseUrl/login", 'POST', ['email' => 'admin@eshasilk.com', 'password' => 'admin@123']);
if (strpos($login['status'], '200') !== false) {
    echo "PASS\n";
    $token = $login['body']['token'];
} else {
    echo "FAIL: " . $login['status'] . "\n";
}

// 2. Fetch Products
echo "2. Testing Fetch Products... ";
$products = request("$baseUrl/products");
echo (strpos($products['status'], '200') !== false) ? "PASS\n" : "FAIL\n";

// 3. Simple Product CRUD - Create
echo "3. Testing Create Product... ";
$newProduct = [
    'name' => 'API Test Saree',
    'category' => 'saree',
    'price' => 5000,
    'stock_quantity' => 10,
    'description' => 'Tested via script',
    'image' => 'https://via.placeholder.com/150'
];
$createProduct = request("$baseUrl/products", 'POST', $newProduct, $token);
if (strpos($createProduct['status'], '201') !== false) {
    echo "PASS\n";
    $productId = $createProduct['body']['product']['id'];
} else {
    echo "FAIL: " . $createProduct['status'] . " - " . json_encode($createProduct['body']) . "\n";
}

// 4. Update Product
echo "4. Testing Update Product... ";
if (isset($productId)) {
    $updateData = ['price' => 5500];
    $update = request("$baseUrl/products/$productId", 'PUT', $updateData, $token);
    echo (strpos($update['status'], '200') !== false) ? "PASS\n" : "FAIL\n";
} else { echo "SKIP\n"; }

// 5. Fetch Categories
echo "5. Testing Fetch Categories... ";
$categories = request("$baseUrl/categories");
echo (strpos($categories['status'], '200') !== false) ? "PASS\n" : "FAIL\n";

// 6. Fetch Offers
echo "6. Testing Fetch Offers... ";
$offers = request("$baseUrl/offers");
echo (strpos($offers['status'], '200') !== false) ? "PASS\n" : "FAIL\n";

// 7. Testing Wishlist (GET)
echo "7. Testing Fetch Wishlist... ";
$wishlist = request("$baseUrl/wishlist", 'GET', null, $token);
echo (strpos($wishlist['status'], '200') !== false) ? "PASS\n" : "FAIL\n";

// 8. Testing Orders (GET)
echo "8. Testing Fetch Orders... ";
$orders = request("$baseUrl/orders", 'GET', null, $token);
echo (strpos($orders['status'], '200') !== false) ? "PASS\n" : "FAIL\n";

// 9. Clean up - Delete Product
echo "9. Testing Delete Product... ";
if (isset($productId)) {
    $delete = request("$baseUrl/products/$productId", 'DELETE', null, $token);
    echo (strpos($delete['status'], '200') !== false) ? "PASS\n" : "FAIL\n";
} else { echo "SKIP\n"; }

echo "\n=== TESTS COMPLETED ===\n";
