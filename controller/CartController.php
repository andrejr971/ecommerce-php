<?php
  include_once('./database/index.php');

  function index($id) {
    $connection = Database();
    $sql = "SELECT * FROM cart WHERE client_id = :id";

    $carts = $connection->prepare($sql);
    $carts->bindValue(':id', $id);
    $carts->execute();

    return $carts->fetchAll(PDO::FETCH_OBJ);
  }

  function show($id) {
    $connection = Database();

    $sql = "SELECT * FROM cart_items WHERE cart_id = :id";

    $items = $connection->prepare($sql);
    $items->bindValue(':id', $id);
    $items->execute();

    $data = [];

    foreach ($items->fetchAll(PDO::FETCH_OBJ) as $item) {
      $sql = "SELECT * FROM product_variations WHERE id = :id LIMIT 1";

      $product = $connection->prepare($sql);
      $product->bindValue(':id', $item->product_variation_id);
      $product->execute();

      $product = $product->fetchAll(PDO::FETCH_OBJ)[0];

      $sql = "SELECT * FROM product_sizes WHERE id = :id LIMIT 1";

      $size = $connection->prepare($sql);
      $size->bindValue(':id', $item->size_id);
      $size->execute();

      $size = $size->fetchAll(PDO::FETCH_OBJ)[0];

      $sql = "SELECT * FROM product_images WHERE product_variation_id = :id LIMIT 1";

      $image = $connection->prepare($sql);
      $image->bindValue(':id', $item->product_variation_id);
      $image->execute();

      $image = $image->fetchAll(PDO::FETCH_OBJ)[0];

      $data[] = [
        'id' => $item->id,
        'product_id' => $product->id,
        'name' => $product->name,
        'slug' => $product->slug,
        'quantity' => $item->quantity,
        'size' => $size->size,
        'image_url' => "http://localhost/aula-php/aplicacoes/api/backend/tmp/products/{$image->path}",
      ];
    }

    return $data;
  }

  function store($request, $client_id) {
    $sql = "SELECT * FROM cart WHERE client_id = :client_id AND status = '0'";

    $connection = Database();

    $cart = $connection->prepare($sql);
    $cart->bindValue(':client_id', $client_id);
    
    if(!$cart->execute()) {
      return [
        'error' => [
          'message' => $cart->errorInfo()
        ]
      ];
    }

    $response = $cart->fetchAll(PDO::FETCH_OBJ);

    if (count($response) === 0) {
      $sql = 'INSERT INTO cart (client_id, status, created_at, updated_at) VALUES (:client_id, :status, :created_at, :updated_at)';
  
      $date = date('Y-m-d H:i:s');

      $cart = $connection->prepare($sql);
      $cart->bindValue(':client_id', $client_id);
      $cart->bindValue(':status', '0');
      $cart->bindValue(':created_at', $date);
      $cart->bindValue(':updated_at', $date);

      $cart->execute();

      $id = $connection->lastInsertId();

      $sql = 'SELECT * FROM cart WHERE id = :id';

      $connection = Database();
  
      $cart = $connection->prepare($sql);
      $cart->bindValue(':id', $id);
      $cart->execute();

      $response = $cart->fetchAll(PDO::FETCH_OBJ);
    }

    $cart = $response[0];

    $sql = 'INSERT INTO cart_items (cart_id, product_variation_id, quantity, size_id, created_at, updated_at) VALUES (:cart_id, :product_variation_id, :quantity, :size_id, :created_at, :updated_at)';
  
    $date = date('Y-m-d H:i:s');

    $item = $connection->prepare($sql);
    $item->bindValue(':cart_id', $cart->id);
    $item->bindValue(':product_variation_id', $request->id);
    $item->bindValue(':quantity', $request->quantity);
    $item->bindValue(':size_id', $request->size);
    $item->bindValue(':created_at', $date);
    $item->bindValue(':updated_at', $date);

    if(!$item->execute()) {
      return [
        'error' => [
          'message' => $item->errorInfo()
        ]
      ];
    }

    $cart = show(($cart->id));

    return $cart;
  }

  function update($option, $id) {
    $cart_id = $option[1];
    
    $connection = Database();

    $sql = "SELECT * FROM cart_items WHERE id = :id";

    $item = $connection->prepare($sql);
    $item->bindValue(':id', $cart_id);
    $item->execute();

    $item = $item->fetchAll(PDO::FETCH_OBJ)[0];

    if ($option[0] === 'add') {
      $quantity = $item->quantity + 1;
    } else {
      $quantity = $item->quantity - 1;
    }

    $sql = "UPDATE cart_items SET quantity = :quantity, updated_at = :updated_at WHERE id = :id";

    $date = date('Y-m-d H:i:s');

    $item = $connection->prepare($sql);
    $item->bindValue(':id', $cart_id);
    $item->bindValue(':quantity', $quantity);
    $item->bindValue(':updated_at', $date);

    if(!$item->execute()) {
      return [
        'error' => [
          'message' => $item->errorInfo()
        ]
      ];
    }

    return;
  }

  function destroy($id) {
    $sql = 'DELETE FROM cart_items WHERE id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    return;
  }