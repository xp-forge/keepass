<?php namespace info\keepass;

class ProtectedValue extends \lang\Object {
  
  public function __construct($bytes, $random) {
    $this->bytes= $bytes;
    $this->random= $random;
  }

  public function __toString() {
    return $this->bytes ^ $this->random;
  }

  public function toString() {
    return nameof($this).'('.str_repeat('*', strlen($this->bytes)).')';
  }
}