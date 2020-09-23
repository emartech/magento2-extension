<?php
  $payload = json_decode(file_get_contents('php://input'), true);

  $params = ' --data \'' . json_encode($payload['data']) . '\' --id ' . $payload['id'];

  if ($payload['store_id']) {
    $params .= ' --store_id ' . $payload['store_id'];
  }

  exec(
    '/app/bin/magento emartech:customevent:create ' . $params . ' 2>&1',
    $output,
    $return_status
  );

  $response = [
    'status' => $return_status
  ];

  if ($return_status !== 0) {
    $response['error'] = $output;
  }

  echo json_encode($response);
?>
