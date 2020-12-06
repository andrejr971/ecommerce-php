<?php
  include_once('./database/index.php');

  function show($request) {
    $connection = Database();

    $sql = "SELECT product_variations.*, products.discount AS discount FROM product_variations LEFT JOIN products ON products.id = product_variations.product_id WHERE product_variations.name LIKE :q";
    $results = $connection->prepare($sql);
    $results->bindValue(':q', "%{$request['q']}%");
    if(!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo()
        ]
      ];
    };

    $products = [];

    foreach ($results->fetchAll(PDO::FETCH_OBJ) as $product) {
      $images = [];
      
      $sql = 'SELECT * FROM product_images WHERE product_variation_id = :id';
      $product_images = Database()->prepare($sql);
      $product_images->bindValue(':id', $product->id);
      $product_images->execute();

      foreach ($product_images->fetchAll(PDO::FETCH_OBJ) as $image) {
        $images[] = [
          'id' => $image->id,
          'image_url' => "http://localhost/aula-php/aplicacoes/api/backend/tmp/products/{$image->path}",
          'path' => $image->path,
        ];
      }

      $products[] = [
        'id' => $product->id,
        'name' => $product->name,
        'slug' => $product->slug,
        'description' => $product->description,
        'price' => $product->price,
        'discount' => $product->discount,
        'created_at' => $product->created_at,
        'updated_at' => $product->updated_at,
        'images' => $images,
      ];
    }

    return $products;
  }