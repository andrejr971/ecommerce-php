<?php 
  include_once('./controller/ProfileController.php');
  include_once('./controller/UpdateAvatarUserController.php');
  include_once('./middlewares/auth.php');

  function get() {
    $auth = auth();

    if (isset($auth['error'])) {
      echo json_encode(['status' => 401, 'error' => $auth['error']['message']]);
      header('HTTP/1.1 401');
      return;
    }

    $user = show();

    echo json_encode($user);
  }

  function put($resources) {
    $auth = auth();

    if (isset($auth['error'])) {
      echo json_encode(['status' => 401, 'error' => $auth['error']['message']]);
      header('HTTP/1.1 401');
      return;
    }

    $data = json_decode(file_get_contents('php://input'));

    $user = update($data);

    echo json_encode($user);
  }

  function post($resources, $path) { 

    if ($path !== 'avatar') {
      echo json_encode(['status' => 404, 'error' => 'Route not found']);
      header('HTTP/1.1 404');
      return;
    }

    $auth = auth();

    if (isset($auth['error'])) {
      echo json_encode(['status' => 401, 'error' => $auth['error']['message']]);
      header('HTTP/1.1 401');
      return;
    }

    $data = $_FILES['file'];

    $user = store($data);

    echo json_encode($user);
  }