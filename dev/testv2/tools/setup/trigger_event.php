<?php
  $payload = json_decode(file_get_contents('php://input'), true);

  if (!isset($payload['id']) || !is_numeric($payload['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'error' => 'Invalid id']);
    exit;
  }
  $id = escapeshellarg($payload['id']);

  $store_id = '';
  if (isset($payload['store_id'])) {
    if (!is_numeric($payload['store_id'])) {
      http_response_code(400);
      echo json_encode(['status' => 'error', 'error' => 'Invalid store_id']);
      exit;
    }
    $store_id = ' --store_id ' . escapeshellarg($payload['store_id']);
  }

  if (!isset($payload['data']) || !is_array($payload['data'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'error' => 'Invalid data']);
    exit;
  }
  $data = escapeshellarg(json_encode($payload['data']));

  $params = ' --data ' . $data . ' --id ' . $id . $store_id;

  exec(
    '/app/bin/magento emartech:customevent:create ' . $params . ' 2>&1',
    $output,
    $return_status
  );

  $response = [
    'status' => $return_status
  ];

  if ($return_status !== 0) {
    // Ne adjunk vissza részletes hibát, csak naplózzuk
    error_log('trigger_event.php error: ' . print_r($output, true));
    $response['error'] = 'Internal error';
  }

  echo json_encode($response);
?>
