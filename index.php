<?php
  error_reporting(-1);
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  
  require 'vendor/autoload.php';
  
  include('./routes/index.php');

  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
  header("Access-Control-Allow-Headers: Origin, Content-Type");
  header('Content-Type: application/json');
  header('Access-Control-Max-Age: 86400');
  // header('Content-Type: multipart/form-data');
  header('Content-Type: application/x-www-form-urlencoded');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { 
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
      header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
      header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    }
    exit(0); 
  }

  if ($_GET) {
    $url = trim($_GET['url'], '/');
    $url = explode('/', $url);
    $method = $_SERVER['REQUEST_METHOD'];
    routes($method, $url);    
  } else {
    echo (json_encode(array('status' => 404, 'data' => 'Not Found')));
    header('HTTP/1.1 404 Not Found');
  }