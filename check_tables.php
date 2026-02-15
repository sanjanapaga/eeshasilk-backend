<?php
require 'vendor/autoload.php';
require 'system/Test/bootstrap.php';

$db = \Config\Database::connect();
$tables = $db->listTables();

echo "Tables in " . $db->getDatabase() . ":\n";
foreach ($tables as $table) {
    echo "- $table\n";
}
if (empty($tables)) {
    echo "No tables found.\n";
}
