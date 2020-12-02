<?php
  use \Firebase\JWT\JWT;

  function auth() {
    $headers = apache_request_headers()['Authorization'];

    $token = explode(' ', $headers);

    $key = '1f8dff82dda0cd1fade43dbe310cd7d0';

    try {
      $decoded = JWT::decode($token[1], $key, array('HS256'));
    } catch (Exception $err) {
      return (Object) [
      'error' => (Object) [
        'message' => 'Not authorized',
        ]
      ];
    } 

    return $decoded;  
  }