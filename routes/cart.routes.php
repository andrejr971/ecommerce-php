<?php 
  include_once('./middlewares/auth-client.php');
  include_once('./controller/CartController.php');

  function get($resources, $id = null) {
    $auth = auth();

    if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }

    if ($id) {
      $cart = show($auth->id);

      if (isset($cart['error'])) {
        echo json_encode(['status' => 400, 'error' => $cart['error']['message']]);
        header('HTTP/1.1 400');
        return;
      }


      echo json_encode($cart);
      return;
    }

    $carts = index($auth->id);

    echo json_encode($carts);
  }


  function post($resources) {
    $auth = auth();

    if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }

    $data = json_decode(file_get_contents('php://input'));

    $cart = store($data, $auth->id);

    echo json_encode($cart);
  }

  function put($resources, ...$rest) {
    $auth = auth();

    if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }

    $cart = update($rest);

    echo json_encode($cart);
  }

  function delete($resources, $id = null) {
    $auth = auth();

    if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }

    $cart = destroy($id);

    echo json_encode($cart);
  }

