<?php
  function permission($auth) {
    if($auth->permission === '0') {
      return [
        'error' => [
          'message' => 'Not authorized',
          ]
        ];
      }
    }