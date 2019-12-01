<?php namespace info\keepass;

use util\Secret;

/**
 * Use a key for decrypting the keepass database 
 *
 * @test  xp://info.keepass.unittest.KeyTest
 */
class Key {
  const ALGORITHM = 'sha256';

  private $passphrase;

  /**
   * Creates a new key 
   *
   * @param  string|util.Secret $passphrase
   */
  public function __construct($passphrase) {
    $this->passphrase= $passphrase instanceof Secret ? $passphrase : new Secret($passphrase);
  }

  /**
   * Transform key
   *
   * @param  info.keepass.Header $header
   * @return string
   */
  public function transform($header) {
    $hash= hash(self::ALGORITHM, hash(self::ALGORITHM, $this->passphrase->reveal(), true), true);

    // Rounds is a 64-bit integer, which cannot be handled by PHP. Use the 
    // four 16-bit unsigned ints into ones, tens and hundreds, then iterate.
    $rounds= $header->rounds;
    if (is_array($rounds)) {
      $ones= $rounds[0] | (($rounds[1] & 0x3fff) << 0x10);
      $tens= (($rounds[1] & 0xc000) >> 0x0e) | ($rounds[2] << 0x02) | (($rounds[3] & 0x0fff) << 0x12);
      $hundreds= ($rounds[3] & 0xf000) >> 0x0c;
    } else {
      $ones= $tens= $hundreds= 0;
    }

    $cipher= new Cipher('aes-256-ecb', $header->transformSeed, '');
    do {
      $hash= $cipher->encrypt($hash, $ones);
      if ($tens > 0) {
        $tens--;
        $ones= 0x40000000;
      } else if ($hundreds > 0) {
        $hundreds--;
        $tens= 0x3fffffff;
        $ones= 0x40000000;
      } else {
        $ones= 0;
      }
    } while ($ones);

    return hash(self::ALGORITHM, $header->masterSeed.hash(self::ALGORITHM, $hash, true), true);
  }
}