<?php
  use \Firebase\JWT\JWT;

  include_once('./database/index.php');

  function store($request) {
    $username = $request->username;
    $password = $request->password;

    if (!$password) {
      return [
        'error' => [
          'message' => 'Username or password incorrected',
        ]
      ];
    }

    $connection = Database();

    $sql = "SELECT * FROM users WHERE username = :username";

    $results = $connection->prepare($sql);
    $results->bindValue(':username', $username);

    if (!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo()
        ]
      ];
    }

    $user = $results->fetchAll(PDO::FETCH_OBJ);

    if (!isset($user[0])) {
      return [
        'error' => [
          'message' => 'Username or password incorrected',
        ]
      ];
    } 

    $comparer = password_verify($password, $user[0]->password);

    if (!$comparer) {
      return [
        'error' => [
          'message' => 'Username or password incorrected',
        ]
      ];
    }

    $key = '1f8dff82dda0cd1fade43dbe310cd7d0';

    $payload = [
      "id" => $user[0]->id,
      "permission" => $user[0]->permission,
    ];

    $token = JWT::encode($payload, $key);
      
    return [
      'user' => [
        'id' => $user[0]->id,
        'name' => $user[0]->name,
        'email' => $user[0]->email,
        'permission' => $user[0]->permission,
        'avatar_url' => $user[0]->avatar ? "http://localhost/aula-php/aplicacoes/api/backend/tmp/{$user[0]->avatar}" : null,
        'created_at' => $user[0]->created_at,
      ],
      'token' => $token
    ]; 
  }