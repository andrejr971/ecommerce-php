<?php
  function auth() {
    if(!isset($_SESSION['session-client'])) {
      return [
        'error' => [
          'message' => 'Not authorized',
          ]
        ];
      }
    }