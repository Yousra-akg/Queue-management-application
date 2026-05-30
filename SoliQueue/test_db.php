<?php
try {
    new PDO('mysql:host=127.0.0.1;port=3306;dbname=SoliQueue', 'soliqueue_app', 'YouSra@2005A');
    echo "3306 success\n";
} catch(Exception $e) {
    echo "3306 fail: " . $e->getMessage() . "\n";
}

try {
    new PDO('mysql:host=127.0.0.1;port=3307;dbname=SoliQueue', 'soliqueue_app', 'YouSra@2005A');
    echo "3307 success\n";
} catch(Exception $e) {
    echo "3307 fail: " . $e->getMessage() . "\n";
}
