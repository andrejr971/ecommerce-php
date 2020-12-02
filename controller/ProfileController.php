<?php
  include_once('./database/index.php');

  function show($id) {
    $sql = "SELECT * FROM users WHERE id = :id";

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    $obj_user = $results->fetchAll(PDO::FETCH_OBJ)[0];

    $user = [
      'id' => intval($obj_user->id),
      'username' => $obj_user->username,
      'name' => $obj_user->name,
      'email' => $obj_user->email,
      'permission' => $obj_user->permission,
      'avatar_url' => $obj_user->avatar ? "http://localhost/aula-php/aplicacoes/api/backend/tmp/{$obj_user->avatar}" : null,
      'created_at' => $obj_user->created_at,
      'updated_at' => $obj_user->updated_at,
    ];

    return $user;
  }

  function update($request, $id) {
    if (isset($request->password) && !isset($request->password_confirmation)) {
      return [
        'error' => [
          'message' => 'Confirmation password is required',
        ]
      ];
    }

    if (!isset($request->password)) {
      $sql = "UPDATE users SET name = :name, email = :email, updated_at = :updated_at WHERE id = :id";
    } else {
      $sql = "UPDATE users SET name = :name, email = :email, password = :password ,updated_at = :updated_at WHERE id = :id";
    }

    $results = Database()->prepare($sql);
    $results->bindValue(':name', $request->name);
    $results->bindValue(':email', $request->email);
    if (isset($request->password)) {
      if ($request->password !== $request->password_confirmation) {
        return [
          'error' => [
            'message' => "passwords don't match"
          ]
        ];
      }

      $results->bindValue(':password', password_hash($request->password , PASSWORD_DEFAULT, ['cost' => 15]));
    }

    $results->bindValue(':updated_at', date('Y-m-d H:i:s'));
    $results->bindValue(':id', $id);
    $results->execute();

    $user = show($id);

    return $user;
  }