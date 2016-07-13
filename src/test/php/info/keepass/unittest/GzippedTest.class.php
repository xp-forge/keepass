<?php namespace info\keepass\unittest;

use info\keepass\Gzipped;
use lang\FormatException;

class GzippedTest extends \unittest\TestCase {
  const COMPRESSED = "\037\213\010\000\000\000\000\000\000\013\013I-.\001\0002\321Mx\004\000\000\000";

  #[@test]
  public function gzencoded_test() {
    new Gzipped(self::COMPRESSED);
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Too short/')]
  public function empty_input_is_invalid() {
    new Gzipped('');
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Magic bytes mismatch/')]
  public function invalid_data() {
    new Gzipped('******************');
  }

  #[@test]
  public function method() {
    $gzipped= new Gzipped(self::COMPRESSED);
    $this->assertEquals(Gzipped::INFLATE, $gzipped->header()['method']);
  }

  #[@test]
  public function decompress() {
    $gzipped= new Gzipped(self::COMPRESSED);
    $this->assertEquals('Test', $gzipped->decompress());
  }
}
