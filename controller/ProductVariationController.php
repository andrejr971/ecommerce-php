<?php
  include_once('./database/index.php');
  include_once('./controller/UpdateImageProduct.php');

  function index() {
    $sql = 'SELECT * FROM product_variations';
    $results = Database()->prepare($sql);
    $results->execute();

    $products = [];

    foreach ($results->fetchAll(PDO::FETCH_OBJ) as $product) {
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
        'created_at' => $product->created_at,
        'updated_at' => $product->updated_at,
        'images' => $images,
      ];
    }

    return $products;
  }

  function show($slug) {
    $sql = 'SELECT product_variations.*, products.discount AS discount FROM product_variations LEFT JOIN products ON products.id = product_variations.product_id WHERE slug = :slug';
    $results = Database()->prepare($sql);
    $results->bindValue(':slug', $slug);
    if(!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo(),
        ]    
      ];
    };

    $obj_product = $results->fetchAll(PDO::FETCH_OBJ);

    if (!isset($obj_product[0])) {
      return [
        'error' => [
          'message' => 'Product not found',
        ]
      ];
    }

    $sql = 'SELECT * FROM product_images WHERE product_variation_id = :id';
    $product_images = Database()->prepare($sql);
    $product_images->bindValue(':id', $obj_product[0]->id);
    $product_images->execute();

    foreach ($product_images->fetchAll(PDO::FETCH_OBJ) as $image) {
      $images[] = [
        'id' => $image->id,
        'image_url' => "http://localhost/aula-php/aplicacoes/api/backend/tmp/products/{$image->path}",
        'path' => $image->path,
      ];
    }

    $sql = 'SELECT * FROM product_sizes WHERE product_variation_id = :id';
    $product_sizes = Database()->prepare($sql);
    $product_sizes->bindValue(':id', $obj_product[0]->id);
    $product_sizes->execute();

    foreach ($product_sizes->fetchAll(PDO::FETCH_OBJ) as $size) {
      $sizes[] = [
        'id' => $size->id,
        'size' => $size->size,
        'quantity' => $size->quantity,
      ];
    }

    $sql = 'SELECT id, slug, name FROM product_variations WHERE product_id = :id';
    $results = Database()->prepare($sql);
    $results->bindValue(':id', $obj_product[0]->product_id);
    $results->execute();

    foreach ($results->fetchAll(PDO::FETCH_OBJ) as $variation) {
      $sql = 'SELECT * FROM product_images WHERE product_variation_id = :id';
      $product_images = Database()->prepare($sql);
      $product_images->bindValue(':id', $variation->id);
      $product_images->execute();

      $image = $product_images->fetchAll(PDO::FETCH_OBJ)[0];

      $outhers[] = [
        'id' => $variation->id,
        'slug' => $variation->slug,
        'name' => $variation->name,
        'image_url' => "http://localhost/aula-php/aplicacoes/api/backend/tmp/products/{$image->path}",
        'path' => $image->path,
      ];
    }

    return [
      'id' => $obj_product[0]->id,
      'name' => $obj_product[0]->name,
      'slug' => $obj_product[0]->slug,
      'description' => $obj_product[0]->description,
      'price' => $obj_product[0]->price,
      'discount' => $obj_product[0]->discount,
      'created_at' => $obj_product[0]->created_at,
      'updated_at' => $obj_product[0]->updated_at,
      'images' => $images,
      'sizes' => $sizes,
      'outhers' => $outhers
    ];
  }

  function store($request, $id, $options) {
    if ($options === 'image') {
      return updateImage($request['image'], $id);
    }

    $date = date('Y-m-d H:i:s');

    $name = $request->name;
    $slug = $request->slug;
    $price = $request->price;
    $description = $request->description;
    $sizes = $request->sizes;
    $quantity = $request->quantity;
    $files = $request->files;

    $sql = "INSERT INTO product_variations (name, slug, product_id, description, price, created_at, updated_at) VALUES (:name, :slug, :product_id, :description, :price, :created_at, :updated_at)";

    $connection = Database();
    
    $results = $connection->prepare($sql);    
    $results->bindValue(':name', $name);
    $results->bindValue(':slug', $slug);
    $results->bindValue(':description', $description);
    $results->bindValue(':price', $price);
    $results->bindValue(':product_id', $id);
    $results->bindValue(':created_at', $date);
    $results->bindValue(':updated_at', $date);

    if(!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo()
        ]
      ];
    };

    $variation_id = intval($connection->lastInsertId());

    foreach ($files as $file) {
      $filename = MD5($file->name) . rand(0, 9999);
      $type = substr($file->name, -4);
      
      $tmp = $file->tmp_name;
      
      $path = "./tmp/products/{$filename}{$type}";  
      
      move_uploaded_file($tmp, $path);
      
      $image = "{$filename}{$type}";

      $sql = "INSERT INTO product_images (path, product_variation_id, created_at, updated_at) VALUES (:path, :product_variation_id, :created_at, :updated_at)";
    
      $results = $connection->prepare($sql);
      $results->bindValue(':path', $image);
      $results->bindValue(':product_variation_id', $variation_id);
      $results->bindValue(':created_at', $date);
      $results->bindValue(':updated_at', $date);
      $results->execute();
    }

    foreach ($sizes as $key => $size) {
      $sql = "INSERT INTO product_sizes (size, quantity, product_variation_id, created_at, updated_at) 
      VALUES (:size, :quantity, :product_variation_id, :created_at, :updated_at)";

      $result = $connection->prepare($sql);
      $result->bindValue(':size', $size);
      $result->bindValue(':quantity', $quantity[$key]);
      $result->bindValue(':product_variation_id', $variation_id);
      $result->bindValue(':created_at', $date);
      $result->bindValue(':updated_at', $date);

      if(!$result->execute()) {
        return [
          'error' => [
            'message' => $result->errorInfo()
          ]
        ];
      };
    }

    return show($slug);
  }

  function update($request, $id) {   

    $date = date('Y-m-d H:i:s');

    $name = $request->name;
    $slug = $request->slug;
    $price = $request->price;
    $description = $request->description;

    $sql = "UPDATE product_variations set name = :name, slug = :slug, description = :description, price = :price, updated_at = :updated_at WHERE id = :id";

    $connection = Database();
    
    $results = $connection->prepare($sql);    
    $results->bindValue(':name', $name);
    $results->bindValue(':slug', $slug);
    $results->bindValue(':description', $description);
    $results->bindValue(':price', $price);
    $results->bindValue(':updated_at', $date);
    $results->bindValue(':id', $id);

    if(!$results->execute()) {
      return [
        'error' => [
          'message' => $results->errorInfo()
        ]
      ];
    };

    return show($slug);
  }

  function destroy($id) {
    $sql = 'SELECT * FROM product_variations WHERE id = :id';
    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    foreach ($results->fetchAll(PDO::FETCH_OBJ) as $product) {
      $sql = 'SELECT * FROM product_images WHERE product_variation_id = :id';
      $product_images = Database()->prepare($sql);
      $product_images->bindValue(':id', $product->id);
      $product_images->execute();

      foreach ($product_images->fetchAll(PDO::FETCH_OBJ) as $image) {
        unlink("./tmp/products/{$image->path}");
      }
    }


    $sql = 'DELETE FROM product_variations WHERE id = :id';

    $results = Database()->prepare($sql);
    $results->bindValue(':id', $id);
    $results->execute();

    return;
  }
