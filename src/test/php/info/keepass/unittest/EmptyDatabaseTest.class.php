<?php namespace info\keepass\unittest;

use info\keepass\KeePassDatabase;
use info\keepass\Key;
use info\keepass\Header;
use lang\ClassLoader;

class EmptyDatabaseTest extends \unittest\TestCase {
  private $key, $input;

  /** @return void */
  public function setUp() {
    $this->key= new Key('test');
    $this->input= ClassLoader::getDefault()->getResourceAsStream('fixtures/empty.kdbx')->in();
  }

  #[@test]
  public function open() {
    KeePassDatabase::open($this->input, $this->key)->close();
  }

  #[@test]
  public function header() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertInstanceOf(Header::class, $db->header());
    });
  }

  #[@test]
  public function groups_are_empty() {
    with (KeePassDatabase::open($this->input, $this->key), function($db) {
      $this->assertEquals([], iterator_to_array($db->groups()));
    });
  }
}