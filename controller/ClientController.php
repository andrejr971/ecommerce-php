<?php
  include_once('./database/index.php');
  
  function show($id) {
    $sql = "SELECT * FROM clients WHERE id = :id";

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    $obj_user = $results->fetchAll(PDO::FETCH_OBJ);

    if (!isset($obj_user[0])) {
      return [
        'error' => [
          'message' => 'User not found',
        ]
      ];
    } 

    $client = [
      'id' => intval($obj_user[0]->id),
      'name' => $obj_user[0]->name,
      'email' => $obj_user[0]->email,
      'avatar_url' => $obj_user[0]->avatar ? "http://localhost/aula-php/aplicacoes/api/backend/tmp/{$obj_user[0]->avatar}" : null,
      'created_at' => $obj_user[0]->created_at,
      'updated_at' => $obj_user[0]->updated_at,
    ];

    return $client;

  }
  
  function store($request) {
    $name = $request->name;
    $email = $request->email;
    $password = $request->password;
    $password_confirmation = $request->password_confirmation;

    if (strlen($password) < 6) {
      return [
        'error' => [
          'message' => 'Password at least 6 characters'
        ]
      ];
    }

    if ($password !== $password_confirmation) {
      return [
        'error' => [
          'message' => "passwords don't match"
        ]
      ];
    }

    try {
      $date = date('Y-m-d H:i:s');

      $sql = "INSERT INTO clients (name, email, password, created_at, updated_at) VALUES (:name, :email, :password, :created_at, :updated_at);SELECT LAST_INSERT_ID();";
      
      $connection = Database();

      $client = $connection->prepare($sql);

      $client->bindValue(':name', $name);
      $client->bindValue(':email', $email);
      $client->bindValue(':password', password_hash($password , PASSWORD_DEFAULT, ['cost' => 15]));
      $client->bindValue(':created_at', $date);
      $client->bindValue(':updated_at', $date);

      if (!$client->execute()) {
        return [
          'error' => [
            'message' => $client->errorInfo()
          ]
        ];
      }

      $id = $connection->lastInsertId();

      $client = show($id); 

      return $client;

    } catch (PDOException $err) {
      return [
        'error' => [
          'message' => $err->getMessage()
        ]
      ];
    }
  }

  function destroy($id) {
    $sql = 'DELETE FROM clients WHERE id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    return;
  }
