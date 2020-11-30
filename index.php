<?php
  error_reporting(-1);
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  
  session_start();
  
  include('./routes/index.php');

  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  // header('Content-Type: multipart/form-data');
  header('Content-Type: application/x-www-form-urlencoded');

  if ($_GET) {
    $url = trim($_GET['url'], '/');
    $url = explode('/', $url);
    $method = $_SERVER['REQUEST_METHOD'];

    routes($method, $url);    
  } else {
    echo (json_encode(array('status' => 404, 'data' => 'Not Found')));
    header('HTTP/1.1 404 Not Found');
  }