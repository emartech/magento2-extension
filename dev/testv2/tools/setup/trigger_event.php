<?php
header('Content-Type: application/json');

$payload = json_decode(file_get_contents('php://input'), true);

// JSON decode and type check
if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 1, 'error' => 'Invalid JSON or not an object']);
    exit;
}

// ID validation
if (empty($payload['id']) || !is_numeric($payload['id'])) {
    echo json_encode(['status' => 1, 'error' => ['2' => '  The "--id" option requires a value.']]);
    exit;
}

// Data validation
if (!isset($payload['data']) || !is_array($payload['data'])) {
    echo json_encode(['status' => 1, 'error' => 'The "--data" option requires an array/object']);
    exit;
}

// Store ID validation (if provided)
if (isset($payload['store_id']) && !is_numeric($payload['store_id'])) {
    echo json_encode(['status' => 1, 'error' => 'The "--store_id" option must be numeric']);
    exit;
}

$args = [
    '/app/bin/magento',
    'emartech:customevent:create',
    '--data',
    json_encode($payload['data']),
    '--id',
    (string)$payload['id']
];

if (!empty($payload['store_id'])) {
    $args[] = '--store_id';
    $args[] = (string)$payload['store_id'];
}

$command = implode(' ', array_map('escapeshellarg', $args));
exec($command . ' 2>&1', $output, $return_status);
$response = ['status' => $return_status];

if ($return_status !== 0) {
    $response['error'] = $output;
}

echo json_encode($response);
?>
