<?php
  include_once('./database/index.php');

  function index($id) {
    $connection = Database();

    $sql = 'SELECT comments.*, clients.name AS name, clients.avatar AS image_url FROM comments LEFT JOIN clients ON clients.id = comments.client_id WHERE product_variation_id = :id ORDER BY comments.created_at DESC';
    $results = $connection->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    $data = [];

    foreach($results->fetchAll(PDO::FETCH_OBJ) as $comment) {
      $data[] = [
        'id' => intval($comment->id),
        'comment' => $comment->comment,
        'client_id' => intval($comment->client_id),
        'product_variation_id' => $comment->product_variation_id,
        'created_at' => $comment->created_at,
        'name' => $comment->name,
        'image_url' => $comment->image_url ? "http://localhost/aula-php/aplicacoes/api/backend/tmp/{$comment->image_url}" : null,
      ];
    }

    return $data;
  }

  function show($id) {
    $connection = Database();

    $sql = 'SELECT comments.*, clients.name AS name, clients.avatar AS image_url FROM comments LEFT JOIN clients ON clients.id = comments.client_id WHERE comments.id = :id';
    $results = $connection->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    $comment = $results->fetchAll(PDO::FETCH_OBJ)[0];

    return [
      'id' => intval($comment->id),
      'comment' => $comment->comment,
      'client_id' => intval($comment->client_id),
      'product_variation_id' => $comment->product_variation_id,
      'created_at' => $comment->created_at,
      'name' => $comment->name,
      'image_url' => $comment->image_url ? "http://localhost/aula-php/aplicacoes/api/backend/tmp/{$comment->image_url}" : null,
    ];
  }

  function store($request, $client_id) {
    $connection = Database();
    
    $sql = 'INSERT INTO comments (comment, client_id, product_variation_id, created_at, updated_at) VALUES (:comment, :client_id, :product_variation_id, :created_at, :updated_at)';
    
    $date = date('Y-m-d H:i:s');

    $results = $connection->prepare($sql);
    $results->bindValue(':comment', $request->comment);
    $results->bindValue(':client_id', $client_id);
    $results->bindValue(':product_variation_id', $request->product_id);
    $results->bindValue(':created_at', $date);
    $results->bindValue(':updated_at', $date);
    if(!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo()
        ]
      ];
    }

    $comment = show($connection->lastInsertId());

    return $comment;
  }

  function destroy($id) {
    $sql = 'DELETE FROM comments WHERE id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    return;
  }