<?php namespace info\keepass\unittest;

use info\keepass\{Header, KeePassDatabase, Key};
use lang\ClassLoader;
use test\Assert;
use test\Test;

abstract class AbstractDatabaseTest {
  protected $fixture;

  /**
   * Open a database
   *
   * @return info.keepass.KeePassDatabase
   */
  protected function database() {
    return KeePassDatabase::open(
      ClassLoader::getDefault()->getResourceAsStream('fixtures/'.$this->fixture.'.kdbx')->in(),
      new Key($this->fixture)
    );
  }

  #[Test]
  public function open() {
    $this->database()->close();
  }

  #[Test]
  public function header() {
    with ($this->database(), function($db) {
      Assert::instance(Header::class, $db->header());
    });
  }
}