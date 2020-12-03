<?php
  include_once('./database/index.php');

  function index() {
    $sql = 'SELECT * FROM brands';

    $results = Database()->prepare($sql);
    $results->execute();

    return $results->fetchAll(PDO::FETCH_OBJ);
  }

  function show($id) {
    $sql = 'SELECT * FROM brands WHERE id = :id';

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

    $sql = "INSERT INTO brands (name, description, created_at, updated_at) VALUES (:name, :description, :created_at, :updated_at)";

    $connection = Database();

    $results = $connection->prepare($sql);  
    $results->bindValue(':name', $name);
    $results->bindValue(':description', $description);
    $results->bindValue(':created_at', $date);
    $results->bindValue(':updated_at', $date);
    $results->execute();

    $id = $connection->lastInsertId();

    $brand = show($id);

    return $brand;
  }

  function update($request, $id) {
    $date = date('Y-m-d H:i:s');

    $name = $request->name;
    $description = $request->description;

    $sql = "UPDATE brands set name = :name, description = :description, updated_at = :updated_at WHERE id = :id";

    $results = Database()->prepare($sql);    
    $results->bindValue(':name', $name);
    $results->bindValue(':description', $description);
    $results->bindValue(':updated_at', $date);
    $results->bindValue(':id', $id);

    if (!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo()
        ]
      ];
    }

    $brand = show($id);

    return $brand;
  }

  function destroy($id) {
    $sql = 'DELETE FROM brands WHERE id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    return;
  }