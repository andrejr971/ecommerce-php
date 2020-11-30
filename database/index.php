<?php

function Database() {
  $credentials = json_decode(file_get_contents("./database/credentials.json"));

    try {
      $connection = new PDO("mysql:host={$credentials->host};port=3306;dbname={$credentials->database}", $credentials->user, $credentials->password);
      return $connection;
    } catch (Exception $err) {
      echo $err->getMessage();
    }
  }