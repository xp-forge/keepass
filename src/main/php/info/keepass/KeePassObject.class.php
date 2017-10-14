<?php namespace info\keepass;

class KeePassObject {
  protected $backing, $path;

  /**
   * Creates a new group
   *
   * @param  [:var] $backing
   */
  public function __construct($backing, $path) {
    $this->backing= $backing;
    $this->path= rtrim($path, '/').'/';
  }

  /** @return string */
  public function path() { return '/' === $this->path ? '/' : substr($this->path, 0, -1); }

  /**
   * Decodes a UUID
   *
   * @param  string $encoded
   * @return string 
   */
  protected function decodeUUID($encoded) {
    return implode('-', unpack('H8a/H4b/H4c/H4d/H12e', base64_decode($encoded)));
  }

  /**
   * Returns whether a field by a given name exists
   *
   * @param  string $name
   * @return bool
   */
  public function provides($name) {
    return isset($this->backing[$name]);
  }

  /**
   * Returns a named field
   *
   * @param  string $name
   * @return var
   * @throws lang.IndexOutOfBoundsException
   */
  public function field($name) {
    return $this->backing[$name];
  }
}