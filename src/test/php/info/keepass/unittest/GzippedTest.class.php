<?php namespace info\keepass\unittest;

use info\keepass\Gzipped;
use lang\FormatException;
use test\Assert;
use test\{Expect, Test};

class GzippedTest {
  const COMPRESSED = "\037\213\010\000\000\000\000\000\000\013\013I-.\001\0002\321Mx\004\000\000\000";

  #[Test]
  public function gzencoded_test() {
    new Gzipped(self::COMPRESSED);
  }

  #[Test, Expect(class: FormatException::class, message: '/Too short/')]
  public function empty_input_is_invalid() {
    new Gzipped('');
  }

  #[Test, Expect(class: FormatException::class, message: '/Magic bytes mismatch/')]
  public function invalid_data() {
    new Gzipped('******************');
  }

  #[Test]
  public function method() {
    $gzipped= new Gzipped(self::COMPRESSED);
    Assert::equals(Gzipped::INFLATE, $gzipped->header()['method']);
  }

  #[Test]
  public function decompress() {
    $gzipped= new Gzipped(self::COMPRESSED);
    Assert::equals('Test', $gzipped->decompress());
  }
}