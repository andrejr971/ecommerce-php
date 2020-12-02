<?php
  include_once('./controller/CategoryController.php');
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
      $category = show($id);

      if (isset($category['error'])) {
        echo json_encode(['status' => 400, 'error' => $category['error']['message']]);
        header('HTTP/1.1 400');
        return;
      }


      echo json_encode($category);
      return;
    }

    $categories = index();

    echo json_encode($categories);
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

    $category = store($data);

    echo json_encode($category);
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
      $category = update($data, $id);
      
      echo json_encode($category);
    } else {
      echo json_encode(array('status' => 422, 'message' => 'Failed to register new category'));
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
    $category = destroy($id);
    
    echo json_encode($category);
  }