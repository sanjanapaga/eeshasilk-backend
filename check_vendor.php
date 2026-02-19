<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Vendor Check</h1>";
$files = [
    'app/Config/Paths.php',
    'vendor/autoload.php',
    'vendor/codeigniter4/framework/system/Boot.php',
    '.env'
];

foreach ($files as $file) {
    echo "Checking $file: " . (file_exists($file) ? "✅ FOUND" : "❌ MISSING") . "<br>";
}

if (file_exists('vendor/codeigniter4/framework/system/Boot.php')) {
    echo "<br><b>Vendor seems ok.</b>";
} else {
    echo "<br><b style='color:red'>Your vendor folder is MISSING or incomplete!</b>";
}
?>
