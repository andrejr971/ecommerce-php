<?php
  include_once('./database/index.php');

  function store($request) {
    $email = $request->email;
    $password = $request->password;

    if (!$password) {
      return [
        'error' => [
          'message' => 'E-mail or password incorrected',
        ]
      ];
    }

    $sql = "SELECT * FROM clients WHERE email = :email";

    $results = Database()->prepare($sql);
    $results->bindValue(':email', $email);
    $results->execute();

    $client = $results->fetchAll(PDO::FETCH_OBJ);

    if (!isset($client[0])) {
      return [
        'error' => [
          'message' => 'E-mail or password incorrected',
        ]
      ];
    } 

    $comparer = password_verify($password, $client[0]->password);

    if (!$comparer) {
      return [
        'error' => [
          'message' => 'E-mail or password incorrected',
        ]
      ];
    }

    $_SESSION['session-client'] = [
      'id' => $client[0]->id
    ];
      
    return [
      'id' => $client[0]->id,
      'name' => $client[0]->name,
      'email' => $client[0]->email,
      'avatar_url' => $client[0]->avatar ? "http://localhost/aula-php/aplicacoes/api/backend/tmp/{$client[0]->avatar}" : null,
      'created_at' => $client[0]->created_at,
    ]; 
  }

  function destroy() {
    unset($_SESSION['session-client']);
  }