<?php

/**
 * Script to update Swagger documentation for ngrok URLs
 * Usage: php fix-ngrok-swagger.php https://your-ngrok-url.ngrok-free.app
 */

if ($argc < 2) {
    echo "Usage: php fix-ngrok-swagger.php <ngrok-url>\n";
    echo "Example: php fix-ngrok-swagger.php https://a9fd245c1269.ngrok-free.app\n";
    exit(1);
}

$ngrokUrl = rtrim($argv[1], '/');
$swaggerFile = __DIR__ . '/storage/api-docs/api-docs.json';

if (!file_exists($swaggerFile)) {
    echo "Error: Swagger file not found at: $swaggerFile\n";
    echo "Please run: php artisan l5-swagger:generate\n";
    exit(1);
}

$swaggerJson = json_decode(file_get_contents($swaggerFile), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error: Invalid JSON in Swagger file\n";
    exit(1);
}

// Update server URLs
if (isset($swaggerJson['servers']) && is_array($swaggerJson['servers'])) {
    // Keep localhost as first server, add/update ngrok as second
    $swaggerJson['servers'] = [
        [
            'url' => 'http://localhost:8000',
            'description' => 'Local Development Server'
        ],
        [
            'url' => $ngrokUrl,
            'description' => 'Ngrok Public Server'
        ]
    ];
}

file_put_contents($swaggerFile, json_encode($swaggerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "✅ Swagger documentation updated successfully!\n";
echo "   Local: http://localhost:8000/api/documentation\n";
echo "   Ngrok: $ngrokUrl/api/documentation\n";
echo "\n";
echo "Note: You'll need to run this script again if you restart ngrok and get a new URL.\n";
