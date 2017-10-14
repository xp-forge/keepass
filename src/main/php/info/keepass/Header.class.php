<?php namespace info\keepass;

use lang\IllegalStateException;

class Header {
  private static $compressions= ['NONE', 'GZIP'];
  private static $randomStreams= ['NONE', 'ARC4', 'SALSA20'];

  public $comment;
  public $cipher;
  public $compression;
  public $masterSeed;
  public $transformSeed;
  public $rounds;
  public $encryptionIV;
  public $randomStreamKey;
  public $startBytes;
  public $randomStream;
  public $digest;

  /**
   * Returns cipher algorithm used
   *
   * @return string
   * @throws lang.IllegalStateException
   */
  public function algorithm() {
    if ("\x31\xC1\xF2\xE6\xBF\x71\x43\x50\xBE\x58\x05\x21\x6A\xFC\x5A\xFF" === $this->cipher) {
      return 'aes-256-cbc';
    }

    throw new IllegalStateException('Unknown cipher '.addcslashes($this->cipher, "\0..\37!\177..\377"));
  }

  /**
   * Returns randoms to be used
   *
   * @return info.keepass.Randoms
   * @throws lang.IllegalStateException
   */
  public function randoms() {
    switch ($this->randomStream) {
      case 0: return null;
      case 2: return new Salsa20(hash('sha256', $this->randomStreamKey, true), "\xE8\x30\x09\x4B\x97\x20\x5D\x2A");
      default: throw new IllegalStateException('Random stream #'.$this->randomStream.' not implemented');
    }
  }

  /** @return bool */
  public function isCompressed() { return 1 === $this->compression; }

  /** @return string */
  public function toString() {
    return sprintf(
      "%s@{\n".
      "  [compression    ] #%d -> %s\n".
      "  [randomStream   ] #%d -> %s\n".
      "  [rounds         ] %s\n".
      "  [cipher         ] %s\n".
      "  [masterSeed     ] %s\n".
      "  [transformSeed  ] %s\n".
      "  [encryptionIV   ] %s\n".
      "  [randomStreamKey] %s\n".
      "  [startBytes     ] %s\n".
      "  [digest         ] %s\n".
      "}",
      nameof($this),
      $this->compression, @self::$compressions[$this->compression],
      $this->randomStream, @self::$randomStreams[$this->randomStream],
      implode(', ', $this->rounds),
      addcslashes($this->cipher, "\0..\37!\177..\377"),
      addcslashes($this->masterSeed, "\0..\37!\177..\377"),
      addcslashes($this->transformSeed, "\0..\37!\177..\377"),
      addcslashes($this->encryptionIV, "\0..\37!\177..\377"),
      addcslashes($this->randomStreamKey, "\0..\37!\177..\377"),
      addcslashes($this->startBytes, "\0..\37!\177..\377"),
      addcslashes($this->digest, "\0..\37!\177..\377")
    );
  }
}
