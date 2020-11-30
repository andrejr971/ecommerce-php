<?php
  function permission() {
    if($_SESSION['session']['permission'] === '0') {
      return [
        'error' => [
          'message' => 'Not authorized',
          ]
        ];
      }
    }