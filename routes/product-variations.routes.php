<?php 
  include_once('./controller/ProductVariationController.php');
  include_once('./middlewares/auth.php');
  include_once('./middlewares/permission.php');

  function get($resource, $id = null) {
    if ($id) {
      $products = show($id);

      if (isset($products['error'])) {
        echo json_encode(['status' => 400, 'error' => $products['error']['message']]);
        header('HTTP/1.1 400');
        return;
      }


      echo json_encode($products);
      return;
    }

    $categories = index();

    echo json_encode($categories);
  }

  function post($resources, $id = null, ...$rest) {
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

    if (!isset($id)) {
      echo json_encode(['status' => 400, 'error' => 'Param not found']);
      header('HTTP/1.1 400');
      return; 
    }


    foreach ($_FILES as $file) {
      $files[] = (Object) $file;
    }

    
    if (isset($_POST['name'])) {
      $data = (Object) [
        'name' => $_POST['name'],
        'slug' => $_POST['slug'],
        'price' => $_POST['price'],
        'description' => $_POST['description'],
        'sizes' => explode(',', $_POST['sizes']),
        'quantity' => explode(',', $_POST['quantity']),
        'files' => $files
      ];

      $product = store($data, $id);
    } else {
      $product = store($_FILES, $rest[1] ?? null, 'image', $id);
    }


    echo json_encode($product);
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
      $product = update($data, $id);
      
      echo json_encode($product);
    } else {
      echo json_encode(array('status' => 422, 'message' => 'Failed to register new product'));
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
    
    $product = destroy($id);
    
    echo json_encode($product);
  }