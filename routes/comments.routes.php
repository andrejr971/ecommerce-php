<?php
  include_once('./controller/CommentController.php');
  include_once('./middlewares/auth-client.php');

  function get($resources, $id = null) {
    $comments = index($id);

    echo json_encode($comments);
  }

  function post($resources, $id = null) {
    $auth = auth();

    if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }

    $data = json_decode(file_get_contents('php://input'));

    $comment = store($data, $auth->id);

    echo json_encode($comment);
  }

  function delete($resources, $id = null) {
    $auth = auth();

    if (isset($auth->error)) {
      echo json_encode(['status' => 401, 'error' => $auth->error->message]);
      header('HTTP/1.1 401');
      return;
    }
    
    $comment = destroy($id);

    echo json_encode($comment);
  }