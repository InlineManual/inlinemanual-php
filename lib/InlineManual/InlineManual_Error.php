<?php

class InlineManual_Error extends Exception {

  public function __construct($message = NULL, $http_status = NULL) {
    parent::__construct($message);
    $this->http_status = $http_status;
  }

  public function getHttpStatus() {
    return $this->http_status;
  }
}
