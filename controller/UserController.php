<?php
  include_once('./database/index.php');
  
  function index() {
    $sql = "SELECT * FROM users";

    $results = Database()->prepare($sql);
    $results->execute();

    $users = [];

    foreach ($results->fetchAll(PDO::FETCH_OBJ) as $user) {
      $users[] = [
        'id' => intval($user->id),
        'username' => $user->username,
        'name' => $user->name,
        'email' => $user->email,
        'permission' => $user->permission,
        'avatar_url' => $user->avatar ? "http://localhost/aula-php/aplicacoes/api/backend/tmp/{$user->avatar}" : null,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
      ];
    }

    return $users;
  }

  function show($id) {
    $sql = "SELECT * FROM users WHERE id = :id";

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

    $user = [
      'id' => intval($obj_user[0]->id),
      'username' => $obj_user[0]->username,
      'name' => $obj_user[0]->name,
      'email' => $obj_user[0]->email,
      'permission' => $obj_user[0]->permission,
      'avatar_url' => $obj_user[0]->avatar ? "http://localhost/aula-php/aplicacoes/api/backend/tmp/{$obj_user[0]->avatar}" : null,
      'created_at' => $obj_user[0]->created_at,
      'updated_at' => $obj_user[0]->updated_at,
    ];

    return $user;

  }
  
  function store($request) {
    $username = $request->username;
    $name = $request->name;
    $email = $request->email;
    $password = $request->password;
    $password_confirmation = $request->password_confirmation;
    $permission = $request->permission;

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

      $sql = "INSERT INTO users (name, username, email, password, permission, created_at, updated_at) VALUES (:name, :username, :email, :password, :permission, :created_at, :updated_at);SELECT LAST_INSERT_ID();";
      
      $connection = Database();

      $user = $connection->prepare($sql);

      $user->bindValue(':username', $username);
      $user->bindValue(':name', $name);
      $user->bindValue(':email', $email);
      $user->bindValue(':password', password_hash($password , PASSWORD_DEFAULT, ['cost' => 15]));
      $user->bindValue(':permission', $permission);
      $user->bindValue(':created_at', $date);
      $user->bindValue(':updated_at', $date);

      if (!$user->execute()) {
        return [
          'error' => [
            'message' => $user->errorInfo()
          ]
        ];
      }

      $id = $connection->lastInsertId();

      $user = show($id); 

      return $user;

    } catch (PDOException $err) {
      return [
        'error' => [
          'message' => $err->getMessage()
        ]
      ];
    }
  }

  function destroy($id) {
    $sql = 'DELETE FROM users WHERE id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    return;
  }

