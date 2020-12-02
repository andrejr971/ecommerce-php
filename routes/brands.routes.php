<?php
  include_once('./controller/BrandController.php');
  include_once('./middlewares/auth.php');
  include_once('./middlewares/permission.php');

  function get($resources, $id = null) {
    $auth = auth();

    if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }

    $permission = permission($auth);

    if (isset($permission)) {
      echo json_encode(['status' => 401, 'error' => $permission['error']['message']]);
      header('HTTP/1.1 401');
      return; 
    }

    if ($id) {
      $brand = show($id);

      if (isset($brand['error'])) {
        echo json_encode(['status' => 400, 'error' => $brand['error']['message']]);
        header('HTTP/1.1 400');
        return;
      }


      echo json_encode($brand);
      return;
    }

    $brands = index();

    echo json_encode($brands);
  }

  function post ($resources) {
    $auth = auth();

  if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }

    $permission = permission($auth);

    if (isset($permission)) {
      echo json_encode(['status' => 401, 'error' => $permission['error']['message']]);
      header('HTTP/1.1 401');
      return; 
    }

    $data = json_decode(file_get_contents('php://input'));

    $brand = store($data);

    echo json_encode($brand);
  }

  function put ($resources, $id) {
    $auth = auth();

  if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }

    $permission = permission($auth);

    if (isset($permission)) {
      echo json_encode(['status' => 401, 'error' => $permission['error']['message']]);
      header('HTTP/1.1 401');
      return; 
    }

    $data = json_decode(file_get_contents('php://input'));

    if ($data) {      
      $brand = update($data, $id);
      
      echo json_encode($brand);
    } else {
      echo json_encode(array('status' => 422, 'message' => 'Failed to register new brand'));
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

    $permission = permission($auth);

    if (isset($permission)) {
      echo json_encode(['status' => 401, 'error' => $permission['error']['message']]);
      header('HTTP/1.1 401');
      return; 
    }
    $brand = destroy($id);
    
    echo json_encode($brand);
  }