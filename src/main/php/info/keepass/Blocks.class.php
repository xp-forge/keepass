<?php namespace info\keepass;

use lang\FormatException;

class Blocks extends \lang\Object {
  const HEADER = 40;

  private $bytes, $start;

  /**
   * Creates a new blocks instance from given bytes at a given offset
   *
   * @param  string $bytes
   * @param  int $start Start offset, defaults to 0
   */
  public function __construct($bytes, $start= 0) {
    $this->bytes= $bytes;
    $this->start= $start;
  }

  /**
   * Reads all blocks
   *
   * @return string
   * @throws lang.FormatException
   */
  public function all() {
    $offset= $this->start;
    $length= strlen($this->bytes);
    $number= 0;

    $blocks= '';
    do {
      $header= unpack('Vid/a32hash/Vsize', substr($this->bytes, $offset, self::HEADER));
      if ($number !== $header['id']) {
        throw new FormatException('Blocks out of order, expected '.$number.', have '.$header['id']);
      }

      if ($header['size'] <= 0) {
        return $blocks;
      } else {
        $offset+= self::HEADER;
        $blocks.= substr($this->bytes, $offset, $header['size']);
        $number++;
        $offset+= $header['size'];
      }
    } while ($offset < $length);

    throw new FormatException('Buffer underrun, expected to read '.$header['size'].' bytes at offset '.$this->offset);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'('.strlen($this->bytes).' bytes, offset= '.$this->start.')';
  }
}