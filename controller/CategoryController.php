<?php
  include_once('./database/index.php');

  function index() {
    $sql = 'SELECT * FROM categories';

    $results = Database()->prepare($sql);
    $results->execute();

    return $results->fetchAll(PDO::FETCH_OBJ);
  }

  function show($id) {
    $sql = 'SELECT * FROM categories WHERE id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    $obj_category = $results->fetchAll(PDO::FETCH_OBJ);

    if (!isset($obj_category[0])) {
      return [
        'error' => [
          'message' => 'Category not found',
        ]
      ];
    }

    return [
      'id' => $obj_category[0]->id,
      'name' => $obj_category[0]->name,
      'description' => $obj_category[0]->description,
      'created_at' => $obj_category[0]->created_at,
      'updated_at' => $obj_category[0]->updated_at,
    ];
  }

  function store($request) {
    $date = date('Y-m-d H:i:s');

    $name = $request->name;
    $description = $request->description ?? null;

    $sql = "INSERT INTO categories (name, description, created_at, updated_at) VALUES (:name, :description, :created_at, :updated_at)";
  
    $connection = Database();

    $results = $connection->prepare($sql);    
    $results->bindValue(':name', $name);
    $results->bindValue(':description', $description);
    $results->bindValue(':created_at', $date);
    $results->bindValue(':updated_at', $date);
    $results->execute();

    $id = $connection->lastInsertId();

    $category = show($id);

    return $category;
  }

  function update($request, $id) {
    $date = date('Y-m-d H:i:s');

    $name = $request->name;
    $description = $request->description || null;

    $sql = "UPDATE categories set name = :name, description = :description,  updated_at = :updated_at";

    $results = Database()->prepare($sql);    
    $results->bindValue(':name', $name);
    $results->bindValue(':description', $description);
    $results->bindValue(':updated_at', $date);

    if (!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo()
        ]
      ];
    }

    $category = show($id);

    return $category;
  }

  function destroy($id) {
    $sql = 'DELETE FROM categories WHERE id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    return;
  }