<?php

function Database() {
  $credentials = json_decode(file_get_contents("./database/credentials.json"));

  try {
    $connection = new PDO("mysql:host={$credentials->host};port=3306;dbname={$credentials->database}", $credentials->user, $credentials->password, 
      array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
    );
    return $connection;
  } catch (Exception $err) {
    echo $err->getMessage();
  }
}
