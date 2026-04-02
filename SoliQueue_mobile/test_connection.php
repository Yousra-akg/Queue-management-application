<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;

$url = "https://bin.nativephp.com/main/versions.json";
echo "Fetching $url...\n";

try {
    $client = new Client(['verify' => false]); // Test without SSL verification
    $response = $client->get($url);
    echo "Success! Length: " . strlen($response->getBody()) . "\n";
    echo "Content snippet: " . substr($response->getBody(), 0, 100) . "...\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
