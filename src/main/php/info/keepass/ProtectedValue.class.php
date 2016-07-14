<?php namespace info\keepass;

class ProtectedValue implements \lang\Value {
  private $bytes, $random;
  
  public function __construct($bytes, $random) {
    $this->bytes= $bytes;
    $this->random= $random;

    // \util\cmd\Console::writeLine(new \util\Bytes($this->bytes));
    // \util\cmd\Console::writeLine(new \util\Bytes($this->random));
  }

  /** @return string */
  public function __toString() {
    return $this->bytes ^ $this->random;
  }

  /** @return string */
  public function toString() {
    return nameof($this).'('.str_repeat('*', strlen($this->bytes)).')';
  }

  /** @return string */
  public function hashCode() {
    return md5($this->bytes ^ $this->random);
  }

  /**
   * Compares this protected value to another
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->bytes ^ $this->random, $value->bytes ^ $value->random) : 1;
  }
}