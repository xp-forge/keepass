<?php namespace info\keepass;

use lang\FormatException;
use lang\IndexOutOfBoundsException;

class Reader implements \lang\Closeable {
  const MAGIC = "\x03\xD9\xA2\x9A\x67\xFB\x4B\xB5";

  private static $fields;
  private $input;
  private $hash;

  static function __static() {
    self::$fields= [
      0x0 => function($header, $bytes) { /* EOF */ },
      0x1 => function($header, $bytes) { $header->comment= $bytes; },
      0x2 => function($header, $bytes) { $header->cipher= $bytes; },
      0x3 => function($header, $bytes) { $header->compression= current(unpack('Vid', $bytes)); },
      0x4 => function($header, $bytes) { $header->masterSeed= $bytes; },
      0x5 => function($header, $bytes) { $header->transformSeed= $bytes; },
      0x6 => function($header, $bytes) { $header->rounds= array_values(unpack('v4', $bytes)); },
      0x7 => function($header, $bytes) { $header->encryptionIV= $bytes; },
      0x8 => function($header, $bytes) { $header->randomStreamKey= $bytes; },
      0x9 => function($header, $bytes) { $header->startBytes= $bytes; },
      0xa => function($header, $bytes) { $header->randomStream= current(unpack('Vid', $bytes)); },
    ];
  }

  /**
   * Creates a new reader
   *
   * @param  io.streams.InputStream
   * @param  string $algorithm Hashing algorithm
   */
  public function __construct($input, $algorithm) {
    $this->input= $input;
    $this->hash= hash_init($algorithm);

    if (self::MAGIC !== $this->read(8)) {
      throw new FormatException('Header format error - not a KeePass database?');
    }
  }

  /**
   * Reads an exact amount of bytes
   *
   * @param  int $n
   * @return string
   * @throws lang.FormatException
   */
  private function read($n) {
    $bytes= '';
    do {
      if (false === ($chunk= $this->input->read($n))) {
        throw new FormatException('Cannot read '.$n.' bytes, EOF after '.strlen($bytes).' bytes');
      }
      $bytes.= $chunk;
    } while (strlen($bytes) < $n);
    hash_update($this->hash, $bytes);
    return $bytes;
  }

  /**
   * Reads version
   *
   * @return string
   */
  public function version() {
    $version= unpack('vmajor/vminor', $this->read(4));
    return $version['major'].'.'.$version['minor'];
  }

  /**
   * Reads header
   *
   * @return info.keepass.Header
   * @throws lang.FormatException
   */
  public function header() {
    $header= new Header();
    try {
      do {
        $field= unpack('cid/vlength', $this->read(3));
        $bytes= $this->read($field['length']);
        $decode= self::$fields[$field['id']];
        $decode($header, $bytes);
      } while ($field['id'] !== 0);
    } catch (IndexOutOfBoundsException $e) {
      throw new FormatException('Unknown header field #'.$field['id']);
    }
    $header->digest= hash_final($this->hash, true);
    return $header;
  }

  /**
   * Reads remaining bytes
   *
   * @return string
   */
  public function remaining() {
    $bytes= '';
    while ($this->input->available()) {
      $bytes.= $this->input->read();
    }
    return $bytes;
  }

  /**
   * Closes input
   *
   * @return void
   */
  public function close() {
    $this->input->close();
  }
}