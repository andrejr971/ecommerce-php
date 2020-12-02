<?php 
  include_once('./controller/ClientController.php');
  include_once('./middlewares/permission.php');
  include_once('./middlewares/auth-client.php');

  function get($resources, $id = null) {
    $auth = auth();

    if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }
    
    if ($id) {
      $user = show($id);

      if (isset($user['error'])) {
        echo json_encode(['status' => 400, 'error' => $user['error']['message']]);
        header('HTTP/1.1 400');
        return;
      }
    
      echo (json_encode($user));
      return;
    } 

    $users = index();
    echo (json_encode($users));
  }

  function post($resources) {
    $data = json_decode(file_get_contents('php://input'));

    if ($data) {
      $user = store($data);  

      if (isset($user['error'])) {
        echo json_encode(['status' => 400, 'error' => $user['error']['message']]);
        header('HTTP/1.1 400');
        return;
      }
      
      echo json_encode($user);
    } else {
      echo json_encode(array('status' => 422, 'message' => 'Failed to register user'));
      header('HTTP/1.1 422');
    }
  }

  function delete ($resources, $id) {
    $auth = auth();

    if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }
    
    $product = destroy($id);
    
    echo json_encode($product);
  }