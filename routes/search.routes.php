<?php 
  include_once('./controller/SearchController.php');

  function get($resources, ...$rest) {
    $params = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    parse_str($params, $querys);

    $products = show($querys);

    if (isset($products['error'])) {
      echo json_encode(['status' => 400, 'error' => $products['error']['message']]);
      header('HTTP/1.1 400');
      return;
    }

    echo json_encode($products);
  }