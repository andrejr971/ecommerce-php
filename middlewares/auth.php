<?php
  function auth() {
    if(!isset($_SESSION['session'])) {
      return [
        'error' => [
          'message' => 'Not authorized',
          ]
        ];
      }
    }