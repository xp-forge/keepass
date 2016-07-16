<?php namespace info\keepass;

use lang\IllegalArgumentException;

/**
 * Snuffle 2005: the Salsa20 encryption function
 *
 * @see  https://cr.yp.to/snuffle.html
 */
class Salsa20 implements Randoms {
  const STATE_LEN  = 32;
  const OUTPUT_LEN = 64;
  const KEY_LEN    = 32;
  const IV_LEN     = 8;

  private $state= [
    0, 0, 0, 0, 0, 0, 0, 0,
    0, 0, 0, 0, 0, 0, 0, 0,
    0, 0, 0, 0, 0, 0, 0, 0,
    0, 0, 0, 0, 0, 0, 0, 0
  ];

  private $output;
  private $position;

  /**
   * Creates a new Salsa20 instance
   *
   * @param  string $key (32 bytes)
   * @param  string $iv (8 bytes)
   */
  public function __construct($key, $iv) {
    $this->useKey($key);
    $this->useIv($iv);

    $this->position= self::OUTPUT_LEN;
    $this->output= array_fill(0, self::OUTPUT_LEN, 0);
  }

  /**
   * Use a given key
   *
   * @param  string $key
   * @throws lang.IllegalArgumentException
   */
  private function useKey($key) {
    if (self::KEY_LEN !== strlen($key)) {
      throw new IllegalArgumentException('Key length invalid, expecting '.self::KEY_LEN.', have '.strlen($key));
    }

    $values= array_values(unpack('v16', $key));
    for ($i= 0; $i < 4; $i++) {
      $j= 2 * $i;
      $this->state[$j + 2]= $values[$j];
      $this->state[$j + 3]= $values[$j + 1];
      $this->state[$j + 22]= $values[$j + 8];
      $this->state[$j + 23]= $values[$j + 9];
    }
    $this->state[0]= 0x7865;
    $this->state[1]= 0x6170;
    $this->state[10]= 0x646e;
    $this->state[11]= 0x3320;
    $this->state[20]= 0x2d32;
    $this->state[21]= 0x7962;
    $this->state[30]= 0x6574;
    $this->state[31]= 0x6b20;
  }

  /**
   * Use a given initial vector
   *
   * @param  string $iv
   * @throws lang.IllegalArgumentException
   */
  private function useIv($iv) {
    if (self::IV_LEN !== strlen($iv)) {
      throw new IllegalArgumentException('IV length invalid, expecting '.self::IV_LEN.', have '.strlen($iv));
    }

    $values= array_values(unpack('v4', $iv));
    $this->state[12]= $values[0];
    $this->state[13]= $values[1];
    $this->state[14]= $values[2];
    $this->state[15]= $values[3];
    $this->state[16]= 0;
    $this->state[17]= 0;
    $this->state[18]= 0;
    $this->state[19]= 0;
  }

  /**
   * Add-rotate-xor (ARX) operation
   *
   * @param  int[] $state
   * @param  int $i
   * @param  int $j
   * @param  int $b
   * @param  int $target
   */
  private static function arx(&$state, $i, $j, $b, $target) {
    $s= $state[2 * $i] + $state[2 * $j];
    $r= $s >> 16;
    $s= $s & 0xffff;
    $t= ($state[2 * $i + 1] + $state[2 * $j + 1] + $r) & 0xffff;

    $m= $b < 16 ? 0 : 1;
    $b= $b % 16;
    $dt= $target * 2;
    $state[$dt + $m] ^= (($s << $b) & 0xffff) | ($t >> (16 - $b));
    $state[$dt + 1 - $m] ^= (($t << $b) & 0xffff) | ($s >> (16 - $b));
  }

  /**
   * Mutate state and re-populate output
   *
   * @return void
   */
  private function output() {
    $state= $this->state;   
    for ($i= 0; $i < 10; $i++) {
      self::arx($state, 0, 12, 7, 4);
      self::arx($state, 4, 0, 9, 8);
      self::arx($state, 8, 4, 13, 12);
      self::arx($state, 12, 8, 18, 0);
      self::arx($state, 5, 1, 7, 9);
      self::arx($state, 9, 5, 9, 13);
      self::arx($state, 13, 9, 13, 1);
      self::arx($state, 1, 13, 18, 5);
      self::arx($state, 10, 6, 7, 14);
      self::arx($state, 14, 10, 9, 2);
      self::arx($state, 2, 14, 13, 6);
      self::arx($state, 6, 2, 18, 10);
      self::arx($state, 15, 11, 7, 3);
      self::arx($state, 3, 15, 9, 7);
      self::arx($state, 7, 3, 13, 11);
      self::arx($state, 11, 7, 18, 15);
      self::arx($state, 0, 3, 7, 1);
      self::arx($state, 1, 0, 9, 2);
      self::arx($state, 2, 1, 13, 3);
      self::arx($state, 3, 2, 18, 0);
      self::arx($state, 5, 4, 7, 6);
      self::arx($state, 6, 5, 9, 7);
      self::arx($state, 7, 6, 13, 4);
      self::arx($state, 4, 7, 18, 5);
      self::arx($state, 10, 9, 7, 11);
      self::arx($state, 11, 10, 9, 8);
      self::arx($state, 8, 11, 13, 9);
      self::arx($state, 9, 8, 18, 10);
      self::arx($state, 15, 14, 7, 12);
      self::arx($state, 12, 15, 9, 13);
      self::arx($state, 13, 12, 13, 14);
      self::arx($state, 14, 13, 18, 15);
    }

    for ($i= 0; $i < self::STATE_LEN; $i+= 2) {
      $s= $state[$i] + $this->state[$i];
      $state[$i]= $s & 0xffff;
      $state[$i + 1]= ($state[$i + 1] + $this->state[$i + 1] + ($s >> 16)) & 0xffff;
    }

    $this->output= '';
    for ($i= 0; $i < self::STATE_LEN; $i++) {
      $this->output.= pack('v', $state[$i]);
    }

    $this->position= 0;
    $this->state[16]++;
    if (0xffff === $this->state[16]) {
      $this->state[16]= 0;
      $this->state[17]++;
      if (0xffff === $this->state[17]) {
        $this->state[17]= 0;
        $this->state[18]++;
        if (0xffff === $this->state[18]) {
          $this->state[18]= 0;
          $this->state[19]++;
        }
      }
    }
  }

  /**
   * Get next random bytes
   *
   * @param  int $n
   * @return string
   */
  public function next($n) {
    $bytes= '';
    while ($n > 0) {
      if (self::OUTPUT_LEN === $this->position) {
        $this->output();
      }

      $copy= min(self::OUTPUT_LEN - $this->position, $n);
      $bytes.= substr($this->output, $this->position, $copy);
      $n-= $copy;
      $this->position+= $copy;
    }

    return $bytes;
  }
}
