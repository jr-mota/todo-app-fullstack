<?php

namespace App;

class Auth {
  private \App\Sys\Connection $Connection;
  private \App\Sys\Validator $Validator;
  private \App\Sys\RequestBodyReader $RequestBodyReader;
  private \App\Sys\Messenger $Messenger;

  public function __construct(\App\Sys\Connection $Connection, \App\Sys\Validator $Validator, \App\Sys\RequestBodyReader $RequestBodyReader, \App\Sys\Messenger $Messenger)
  {
    $this->Connection = $Connection;
    $this->Validator = $Validator;
    $this->RequestBodyReader = $RequestBodyReader;
    $this->Messenger = $Messenger;
  }

  public function auth() {
    switch($_SERVER["REQUEST_METHOD"]) {
      case "POST":
        $this->signUp();
        break;
      case "GET":
        $this->signIn();
        break;
    }
  }

  public function signUp() {
    $body = $this->RequestBodyReader->getBody();

    $login = trim(htmlspecialchars( (isset($body["login"]) ? $body["login"] : "") ));
    $password = trim(htmlspecialchars( (isset($body["password"]) ? $body["password"] : "") ));

    $loginIsOccupy = $this->Connection->query("SELECT * FROM users WHERE id = 1");

    print_r($loginIsOccupy);

    # Login ocuppied
    if ($loginIsOccupy) {
      $this->Messenger->sendResponse(400, [
        "error" => "Логин занят!"
      ]);
    }

    # Invalid data
    if (!($this->Validator->loginValid($login) && $this->Validator->passwordValid($password))) {
      $this->Messenger->sendResponse(400, [
        "error" => "Введите корректные данные!"
      ]);
    }

    # Signup
    $this->Messenger->sendResponse(201, [
      "auth" => true,
      "id" => 1,
      "hash" => "1421gsfqr51aefa"
    ]);
  }

  public function signIn() {
    return;
  }
}