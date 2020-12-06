<?php
  
  function routes($method, $resource) {
    $methodPermited = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
    $resourcePermited = [
      'users', 
      'profile',
      'session',
      'categories',
      'brands',
      'products',
      'product-variations',
      'clients',
      'session-client',
      'profile-client',
      'cart',
      'comments',
      'search'
    ];
    
    if (in_array($method, $methodPermited)) {
      if (in_array($resource[0], $resourcePermited)) {   
        if(file_exists("./routes/{$resource[0]}.routes.php")) {
          include_once("./routes/{$resource[0]}.routes.php");          
          call_user_func_array($method, $resource);
        } else {
          echo (json_encode(array('status' => 404, 'data' => 'Not Found')));
          header('HTTP/1.1 404 Not Found');
        }
      } else {
        echo (json_encode(array('status' => 404, 'data' => 'Not Found')));
        header('HTTP/1.1 404 Not Found');
      }
    } else {
      echo (json_encode(array('status' => 401, 'message' => 'Unathorization')));
      header('HTTP/1.1 405 Method Not Allowed');
      header('Allow: POST, GET, PUT, DELETE');
    }
  }