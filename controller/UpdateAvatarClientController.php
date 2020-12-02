<?php 
  include_once('./database/index.php');

  function store($request, $id) {
    $filename = MD5($request['name']) . rand(0, 9999);
    $type = substr($request['name'], -4);
    
    $tmp = $request['tmp_name'];
    
    $path = "./tmp/{$filename}{$type}";  
    
    move_uploaded_file($tmp, $path);
    
    $avatar = "{$filename}{$type}";

    $sql = "UPDATE clients SET avatar = :avatar, updated_at = :updated_at WHERE id = :id";
    $results = Database()->prepare($sql);
    $results->bindValue(':avatar', $avatar);
    $results->bindValue(':updated_at', date('Y-m-d H:i:s'));
    $results->bindValue(':id', $id);
    $results->execute();

    $sql = "SELECT * FROM users WHERE id = :id";

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    $obj_user = $results->fetchAll(PDO::FETCH_OBJ)[0];

    $user = [
      'id' => intval($obj_user->id),
      'name' => $obj_user->name,
      'email' => $obj_user->email,
      'avatar_url' => $obj_user->avatar ? "http://localhost/aula-php/aplicacoes/api/backend/tmp/{$obj_user->avatar}" : null,
      'created_at' => $obj_user->created_at,
      'updated_at' => $obj_user->updated_at,
    ];

    return $user;
  }