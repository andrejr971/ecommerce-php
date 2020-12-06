<?php
  function updateImage($request, $id, $product_id) {
    $connection = Database();

    $date = date('Y-m-d H:i:s');

    if (!$id) {
      $filename = MD5($request['name']) . rand(0, 9999);
      $type = substr($request['name'], -4);
      
      $tmp = $request['tmp_name'];
      
      $path = "./tmp/products/{$filename}{$type}";  
      
      move_uploaded_file($tmp, $path);
      
      $image = "{$filename}{$type}";

      $sql = "INSERT INTO product_images (path, product_variation_id, created_at, updated_at) VALUES (:path, :product_variation_id, :created_at, :updated_at)";
    
      $results = $connection->prepare($sql);
      $results->bindValue(':path', $image);
      $results->bindValue(':product_variation_id', $product_id);
      $results->bindValue(':created_at', $date);
      $results->bindValue(':updated_at', $date);
      $results->execute();

      return;
    }

      $sql = 'SELECT * FROM product_images WHERE id = :id';
      $product_images = Database()->prepare($sql);
      $product_images->bindValue(':id', $id);
      $product_images->execute();
      
      $image = $product_images->fetchAll(PDO::FETCH_OBJ)[0];
      
      unlink("./tmp/products/{$image->path}");
      
      $filename = MD5($request['name']) . rand(0, 9999);
      $type = substr($request['name'], -4);
      
      $tmp = $request['tmp_name'];
      
    $path = "./tmp/products/{$filename}{$type}";  
    
    move_uploaded_file($tmp, $path);
    
    $image = "{$filename}{$type}";

    $sql = "UPDATE product_images SET path = :path, updated_at = :updated_at WHERE id = :id";
    
    $date = date('Y-m-d H:i:s');
  
    
    $results = $connection->prepare($sql);
    $results->bindValue(':path', $image);
    $results->bindValue(':updated_at', $date);
    $results->bindValue(':id', $id);
    $results->execute();

    return;
  }