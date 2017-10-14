<?php namespace info\keepass;

class Cipher {
  private $method, $key, $iv;

  public function __construct($method, $key, $iv= '') {
    $this->method= $method;
    $this->key= $key;
    $this->iv= $iv;
  }

  public function encrypt($input, $times= 1) {
    for ($i = 0; $i < $times; $i++) {
      $input= openssl_encrypt($input, $this->method, $this->key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $this->iv);
    }
    return $input;
  }

  public function decrypt($bytes) {
    return openssl_decrypt(
      $bytes,
      $this->method,
      $this->key,
      OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,
      $this->iv
    );
  }
}