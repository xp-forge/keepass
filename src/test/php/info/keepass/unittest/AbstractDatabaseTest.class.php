<?php namespace info\keepass\unittest;

use info\keepass\KeePassDatabase;
use info\keepass\Key;
use info\keepass\Header;
use lang\ClassLoader;

abstract class AbstractDatabaseTest extends \unittest\TestCase {
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

  #[@test]
  public function open() {
    $this->database()->close();
  }

  #[@test]
  public function header() {
    with ($this->database(), function($db) {
      $this->assertInstanceOf(Header::class, $db->header());
    });
  }
}