<?php namespace info\keepass\unittest;

use info\keepass\{Header, Key};
use test\Assert;
use test\{Test, TestCase, Values};
use util\{Objects, Secret};

class KeyTest {

  /** @return iterable */
  private function secrets() {
    yield ['test'];
    yield [new Secret('test')];
  }

  #[Test, Values(from: 'secrets')]
  public function can_create($arg) {
    new Key($arg);
  }

  #[Test, Values(from: 'secrets')]
  public function transform($arg) {
    $header= new Header();
    $header->rounds= 1;
    $header->transformSeed= "\220\202E\026\\\265\265s\003\373\n\350C\326\024";
    $header->masterSeed= "\313\231\343\222\246\207\240\275\274\302\260\027\345";
    Assert::equals(
      "zd\036x\010\t\332 N+\017\362\035A8\301@\027\017\243VL8\325\322+\3750\231\021\264\017",
      (new Key($arg))->transform($header)
    );
  }

  #[Test, Values(from: 'secrets')]
  public function string_representation_does_not_reveal_passphrase($arg) {
    Assert::false(strstr(Objects::stringOf(new Key($arg)), 'test'));
  }
}