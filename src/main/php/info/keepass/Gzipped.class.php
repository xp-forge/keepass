<?php namespace info\keepass;

use lang\FormatException;

/**
 * Handles decompressing GZipped data
 *
 * @test xp://info.keepass.unittest.GzippedTest
 * @see  http://php.net/manual/en/function.gzdecode.php#82930
 */
class Gzipped {
  const INFLATE = 8;

  private $header, $data;

  /**
   * Creates new GZipped data
   *
   * @param  string $bytes
   * @throws lang.FormatException
   */
  public function __construct($bytes) {
    if (strlen($bytes) < 18) {
      throw new FormatException('Not GZIP format: Too short');
    } else if (0 !== strncmp($bytes, "\x1f\x8b", 2)) {
      throw new FormatException('Not GZIP format: Magic bytes mismatch');
    }

    $this->header= unpack('cmethod/cflags/Vmtime/cxfl/cos', substr($bytes, 2, 8));
    $offset= 10;

    // EXTRA
    if ($this->header['flags'] & 4) {
      $extra= unpack('v', substr($bytes, 8, 2));
      $offset+= $extra + 2;
    }

    // Filenames
    if ($this->header['flags'] & 8) {
      $filename= strpos($data, "\0", $offset);
      $this->header['filename']= substr($data, $offset, $filename);
      $offset+= $filename + 1;
    }

    // Comments
    if ($this->header['flags'] & 16) {
      $comment= strpos($data, "\0", $offset);
      $this->header['comment']= substr($data, $offset, $comment);
      $offset+= $comment + 1;
    }

    // CRC32
    if ($this->header['flags'] & 2) {
      $offset+= 2;
    }

    $this->data= substr($bytes, $offset);
  }

  /** @return [:var] */
  public function header() { return $this->header; }

  /**
   * Decompress data
   *
   * @return string
   * @throws lang.FormatException
   */
  public function decompress() {
    if (self::INFLATE === $this->header['method']) {
      return gzinflate($this->data);
    }

    throw new FormatException('Cannot handle compression method #'.$this->header['method']);
  }
}