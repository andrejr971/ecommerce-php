<?php 
  include_once('./controller/SessionController.php');
  include_once('./middlewares/auth.php');

  function post($resources) {
    $data = json_decode(file_get_contents('php://input'));

    if ($data) {
      $session = store($data);

      if (isset($session['error'])) {
        echo json_encode(['status' => 400, 'error' => $session['error']['message']]);
        header('HTTP/1.1 400');
        return;
      }

      echo json_encode($session);
    } else {
      echo json_encode(array('status' => 400, 'message' => 'Email or password incorrected'));
      header('HTTP/1.1 400');
    }
  }

  function delete() {
    $auth = auth();

    if (isset($auth['error'])) {
      echo json_encode(['status' => 401, 'error' => $auth['error']['message']]);
      header('HTTP/1.1 401');
      return;
    }

    destroy();
  }