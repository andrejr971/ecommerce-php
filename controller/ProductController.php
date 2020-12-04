<?php
  include_once('./database/index.php');

  function index() {
    $sql = 'SELECT products.*, categories.name as category_name, brands.name as brand_name  
            FROM products 
              LEFT JOIN categories ON products.category_id = categories.id 
              LEFT JOIN brands ON products.brand_id = brands.id';

    $results = Database()->prepare($sql);
    $results->execute();

    return $results->fetchAll(PDO::FETCH_OBJ);
  }

  function show($id) {
    $sql = 'SELECT products.*, categories.name as category_name, brands.name as brand_name 
              FROM products 
                LEFT JOIN categories ON products.category_id = categories.id 
                LEFT JOIN brands ON products.brand_id = brands.id
              WHERE products.id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    $obj_product = $results->fetchAll(PDO::FETCH_OBJ);

    if (!isset($obj_product[0])) {
      return [
        'error' => [
          'message' => 'Product not found',
        ]
      ];
    }

    $relations = [];

    $sql = 'SELECT * FROM product_variations WHERE product_id = :id';
    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    $relations = $results->fetchAll(PDO::FETCH_OBJ);
    

    return [
      'id' => $obj_product[0]->id,
      'name' => $obj_product[0]->name,
      'discount' => $obj_product[0]->discount,
      'category' => [
        'id' => $obj_product[0]->category_id,
        'name' => $obj_product[0]->category_name,
      ],
      'brand' => [
        'id' => $obj_product[0]->brand_id,
        'name' => $obj_product[0]->brand_name,
      ],
      'variations' => $relations
    ];
  }

  function store($request) {
    $date = date('Y-m-d H:i:s');

    $name = $request->name;
    $category_id = $request->category;
    $brand_id = $request->brand;

    $sql = "INSERT INTO products (name, category_id, brand_id, created_at, updated_at) VALUES (:name, :category_id, :brand_id, :created_at, :updated_at)";

    $connection = Database();
    
    $results = $connection->prepare($sql);    
    $results->bindValue(':name', $name);
    $results->bindValue(':category_id', $category_id);
    $results->bindValue(':brand_id', $brand_id);
    $results->bindValue(':created_at', $date);
    $results->bindValue(':updated_at', $date);

    if(!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo()
        ]
      ];
    };

    $id = $connection->lastInsertId();

    $product = show($id);

    return $product;
  }

  function update($request, $id) {
    $date = date('Y-m-d H:i:s');

    $name = $request->name;

    $sql = "UPDATE products set name = :name, updated_at = :updated_at WHERE id = :id";

    if ($request->discount) {
      $sql = "UPDATE products set name = :name, discount = :discount, updated_at = :updated_at WHERE id = :id";
    }
    
    
    $results = Database()->prepare($sql);    
    $results->bindValue(':name', $name);
    if ($request->discount) {
      $results->bindValue(':discount', $request->discount);
    }
    $results->bindValue(':updated_at', $date);
    $results->bindValue(':id', $id);

    if (!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo()
        ]
      ];
    }

    $product = show($id);

    return $product;
  }

  function destroy($id) {
    $sql = 'DELETE FROM products WHERE id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    return;
  }
